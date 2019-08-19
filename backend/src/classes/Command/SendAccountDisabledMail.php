<?php declare(strict_types=1);

namespace Neucore\Command;

use Neucore\Command\Traits\EsiRateLimited;
use Neucore\Command\Traits\LogOutput;
use Neucore\Entity\Player;
use Neucore\Factory\RepositoryFactory;
use Neucore\Repository\PlayerRepository;
use Neucore\Service\EveMail;
use Neucore\Service\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendAccountDisabledMail extends Command
{
    use LogOutput;
    use EsiRateLimited;

    /**
     * @var EveMail
     */
    private $eveMail;

    /**
     * @var PlayerRepository
     */
    private $playerRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var int
     */
    private $sleep;

    public function __construct(
        EveMail $eveMail,
        RepositoryFactory $repositoryFactory,
        ObjectManager $objectManager,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->logOutput($logger);
        $this->esiRateLimited($repositoryFactory->getSystemVariableRepository());

        $this->eveMail = $eveMail;
        $this->playerRepository = $repositoryFactory->getPlayerRepository();
        $this->objectManager = $objectManager;
    }

    protected function configure()
    {
        $this->setName('send-account-disabled-mail')
            ->setDescription('Sends "account disabled" EVE mail notification.')
            ->addOption(
                'sleep',
                's',
                InputOption::VALUE_OPTIONAL,
                'Time to sleep in seconds after each mail sent (ESI rate limit is 4/min)',
                20
            );
        $this->configureLogOutput($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->sleep = intval($input->getOption('sleep'));
        $this->executeLogOutput($input, $output);

        $this->writeLine('Started "send-account-disabled-mail"', false);

        $this->send();

        $this->writeLine('Finished "send-account-disabled-mail"', false);
    }

    private function send()
    {
        $notActiveReason = $this->eveMail->accountDeactivatedIsActive();
        if ($notActiveReason !== '') {
            $this->writeLine(' ' . $notActiveReason, false);
            return;
        }

        $dbResultLimit = 1000;
        $offset = $dbResultLimit * -1;
        do {
            $offset += $dbResultLimit;
            $playerIds = array_map(function (Player $player) {
                return $player->getId();
            }, $this->playerRepository->findBy(
                ['status' => Player::STATUS_STANDARD],
                ['lastUpdate' => 'ASC'],
                $dbResultLimit,
                $offset
            ));
            $this->objectManager->clear(); // detaches all objects from Doctrine

            foreach ($playerIds as $playerId) {
                if (! $this->objectManager->isOpen()) {
                    $this->logger->critical('SendAccountDisabledMail: cannot continue without an open entity manager.');
                    break;
                }
                $this->checkErrorLimit();

                $characterId = $this->eveMail->accountDeactivatedFindCharacter($playerId);
                if ($characterId === null) {
                    $this->eveMail->accountDeactivatedMailSent($playerId, false);
                    continue;
                }

                $mayNotSendReason = $this->eveMail->accountDeactivatedMaySend($characterId);
                if ($mayNotSendReason !== '') {
                    continue;
                }

                $errMessage = $this->eveMail->accountDeactivatedSend($characterId);
                if ($errMessage === '') { // success
                    $this->eveMail->accountDeactivatedMailSent($playerId, true);
                    $this->writeLine('  Mail sent to ' . $characterId);
                    usleep($this->sleep * 1000 * 1000);
                } else {
                    $this->writeLine(' ' . $errMessage, false);
                }
            }
        } while (count($playerIds) === $dbResultLimit);
    }
}
