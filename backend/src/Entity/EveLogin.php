<?php

declare(strict_types=1);

namespace Neucore\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
/* @phan-suppress-next-line PhanUnreferencedUseNormal */
use Doctrine\ORM\Mapping as ORM;
/* @phan-suppress-next-line PhanUnreferencedUseNormal */
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "name", "description", "esiScopes", "eveRoles"}
 * )
 * @ORM\Entity
 * @ORM\Table(name="eve_logins")
 */
class EveLogin implements \JsonSerializable
{
    /**
     * Prefix of all internal login IDs.
     */
    public const INTERNAL_LOGIN_PREFIX = 'core.';

    /**
     * Default login.
     */
    public const NAME_DEFAULT = self::INTERNAL_LOGIN_PREFIX . 'default';

    /**
     * Member tracking login.
     */
    public const NAME_TRACKING = self::INTERNAL_LOGIN_PREFIX . 'tracking';

    /**
     * Login for "managed" accounts.
     */
    public const NAME_MANAGED = self::INTERNAL_LOGIN_PREFIX . 'managed';

    /**
     * Login of the character that is used to send mails.
     */
    public const NAME_MAIL = self::INTERNAL_LOGIN_PREFIX . 'mail';

    /**
     * All internal login IDs.
     */
    public const INTERNAL_LOGIN_NAMES = [
        self::NAME_DEFAULT,
        self::NAME_TRACKING,
        self::NAME_MANAGED,
        self::NAME_MAIL,
    ];

    public const SCOPE_MAIL = 'esi-mail.send_mail.v1';
    public const SCOPE_ROLES = 'esi-characters.read_corporation_roles.v1';
    public const SCOPE_TRACKING = 'esi-corporations.track_members.v1';
    public const SCOPE_STRUCTURES = 'esi-universe.read_structures.v1';
    public const SCOPE_MEMBERSHIP = 'esi-corporations.read_corporation_membership.v1';

    public const ROLE_DIRECTOR = 'Director';

    /**
     * @OA\Property()
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id = null;

    /**
     * @OA\Property(
     *     maxLength=20,
     *     pattern="^[-._a-zA-Z0-9]+$",
     *     nullable=false,
     *     description="Names starting with 'core.' are reserved for internal use."
     * )
     * @ORM\Column(type="string", unique=true, length=20)
     */
    private string $name = '';

    /**
     * @OA\Property(maxLength=1024)
     * @ORM\Column(type="string", length=1024)
     */
    private string $description = '';

    /**
     * @OA\Property(maxLength=8192)
     * @ORM\Column(type="string", name="esi_scopes", length=8192)
     */
    private string $esiScopes = '';

    /**
     * @OA\Property(
     *     type="array",
     *     @OA\Items(type="string"),
     *     description="Maximum length of all roles separated by comma: 1024."
     * )
     * @ORM\Column(type="string", name="eve_roles", length=1024)
     */
    private string $eveRoles = '';

    /**
     * @ORM\OneToMany(targetEntity="EsiToken", mappedBy="eveLogin")
     * @var Collection
     */
    private $esiTokens;

    public static function isValidObject(\stdClass $data): bool
    {
        return
            property_exists($data, 'id')          && is_int($data->id) &&
            property_exists($data, 'name')        && is_string($data->name) &&
            property_exists($data, 'description') && is_string($data->description) &&
            property_exists($data, 'esiScopes')   && is_string($data->esiScopes) &&
            property_exists($data, 'eveRoles')    && is_array($data->eveRoles);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'esiScopes' => $this->esiScopes,
            'eveRoles' => $this->getEveRoles(),
        ];
    }

    public function __construct()
    {
        $this->esiTokens = new ArrayCollection();
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Validate (only whitespaces, not values) and set scopes.
     */
    public function setEsiScopes(string $esiScopes): self
    {
        // remove extra white space characters
        $scopes = preg_split('/\s+/', $esiScopes, -1, PREG_SPLIT_NO_EMPTY);
        if (is_array($scopes)) {
            $this->esiScopes = implode(' ', $scopes);
        }
        return $this;
    }

    public function getEsiScopes(): string
    {
        return $this->esiScopes;
    }

    /**
     * @param string[] $eveRoles
     */
    public function setEveRoles(array $eveRoles): self
    {
        $this->eveRoles = implode(',', $eveRoles);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getEveRoles(): array
    {
        if (empty($this->eveRoles)) {
            return [];
        }
        return explode(',', $this->eveRoles);
    }

    public function addEsiToken(EsiToken $token): self
    {
        $this->esiTokens[] = $token;
        return $this;
    }

    public function removeEsiToken(EsiToken $token): bool
    {
        return $this->esiTokens->removeElement($token);
    }

    /**
     * @return EsiToken[]
     */
    public function getEsiTokens(): array
    {
        $esiTokens = $this->esiTokens->toArray();

        uasort($esiTokens, function (EsiToken $a, EsiToken $b) {
            $nameA = $a->getCharacter() ? $a->getCharacter()->getName() : '';
            $nameB = $b->getCharacter() ? $b->getCharacter()->getName() : '';
            if ($nameA < $nameB) {
                return -1;
            } elseif ($nameA > $nameB) {
                return 1;
            }
            return 0;
        });

        return array_values($esiTokens);
    }
}
