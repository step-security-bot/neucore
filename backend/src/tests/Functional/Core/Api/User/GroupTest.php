<?php
namespace Tests\Functional\Core\Api\User;

use Tests\Functional\WebTestCase;
use Tests\Helper;
use Brave\Core\Roles;
use Brave\Core\Entity\GroupRepository;
use Brave\Core\Entity\Group;
use Brave\Core\Entity\PlayerRepository;
use Brave\Core\Entity\Player;

class GroupTest extends WebTestCase
{
    private $helper;

    private $em;

    private $gr;

    private $gid;

    private $aid;

    private $pid;

    public function setUp()
    {
        $_SESSION = null;

        $this->helper = new Helper();
        $this->em = $this->helper->getEm();
        $this->gr = new GroupRepository($this->em);
    }

    public function testAll403()
    {
        $response = $this->runApp('GET', '/api/user/group/all');
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testAll200()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response = $this->runApp('GET', '/api/user/group/all');
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertSame(
            [['id' => $this->gid, 'name' => 'group-one']],
            $this->parseJsonBody($response)
        );
    }

    public function testCreate403()
    {
        $response = $this->runApp('POST', '/api/user/group/create');
        $this->assertEquals(403, $response->getStatusCode());

        $this->setupDb();
        $this->loginUser(6); // not group-admin

        $response = $this->runApp('POST', '/api/user/group/create');
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testCreate400()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response1 = $this->runApp('POST', '/api/user/group/create');
        $this->assertEquals(400, $response1->getStatusCode());

        $response2 = $this->runApp('POST', '/api/user/group/create', ['name' => 'in va lid']);
        $this->assertEquals(400, $response2->getStatusCode());
    }

    public function testCreate409()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response = $this->runApp('POST', '/api/user/group/create', ['name' => 'group-one']);
        $this->assertEquals(409, $response->getStatusCode());
    }

    public function testCreate200()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response = $this->runApp('POST', '/api/user/group/create', ['name' => 'new-g']);
        $this->assertEquals(200, $response->getStatusCode());

        $ng = $this->gr->findOneBy(['name' => 'new-g']);
        $this->assertSame(
            ['id' => $ng->getId(), 'name' => 'new-g'],
            $this->parseJsonBody($response)
        );
    }

    public function testRename403()
    {
        $response = $this->runApp('PUT', '/api/user/group/66/rename');
        $this->assertEquals(403, $response->getStatusCode());

        $this->setupDb();
        $this->loginUser(6); // not group-admin

        $response = $this->runApp('PUT', '/api/user/group/66/rename');
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testRename404()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response = $this->runApp('PUT', '/api/user/group/'.($this->gid + 1).'/rename', ['name' => 'new-g']);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testRename400()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response1 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/rename', ['name' => '']);
        $this->assertEquals(400, $response1->getStatusCode());

        $response2 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/rename', ['name' => 'in va lid']);
        $this->assertEquals(400, $response2->getStatusCode());
    }

    public function testRename409()
    {
        $this->setupDb();
        $this->loginUser(8);

        $this->helper->addGroups(['group-two']);

        $response = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/rename', ['name' => 'group-two']);
        $this->assertEquals(409, $response->getStatusCode());
    }

    public function testRename200()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response1 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/rename', ['name' => 'group-one']);
        $response2 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/rename', ['name' => 'new-name']);
        $this->assertEquals(200, $response1->getStatusCode());
        $this->assertEquals(200, $response2->getStatusCode());

        $this->assertSame(
            ['id' => $this->gid, 'name' => 'group-one'],
            $this->parseJsonBody($response1)
        );

        $this->assertSame(
            ['id' => $this->gid, 'name' => 'new-name'],
            $this->parseJsonBody($response2)
        );

        $renamed = $this->gr->findOneBy(['name' => 'new-name']);
        $this->assertInstanceOf(Group::class, $renamed);
    }

    public function testDelete403()
    {
        $response = $this->runApp('DELETE', '/api/user/group/66/delete');
        $this->assertEquals(403, $response->getStatusCode());

        $this->setupDb();
        $this->loginUser(6); // not group-admin

        $response = $this->runApp('DELETE', '/api/user/group/66/delete');
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testDelete404()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response = $this->runApp('DELETE', '/api/user/group/'.($this->gid + 1).'/delete');
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testDelete204()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response = $this->runApp('DELETE', '/api/user/group/'.$this->gid.'/delete');
        $this->assertEquals(204, $response->getStatusCode());

        $this->em->clear();

        $deleted = $this->gr->find($this->gid);
        $this->assertNull($deleted);
    }

    public function testManagers403()
    {
        $response = $this->runApp('GET', '/api/user/group/1/managers');
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testManagers404()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response = $this->runApp('GET', '/api/user/group/'.($this->gid + 1).'/managers');
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testManagers200()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response = $this->runApp('GET', '/api/user/group/'.$this->gid.'/managers');
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertSame(
            [['id' => $this->pid, 'name' => 'Admin']],
            $this->parseJsonBody($response)
        );
    }

    public function testAddManager403()
    {
        $response = $this->runApp('PUT', '/api/user/group/69/add-manager/1');
        $this->assertEquals(403, $response->getStatusCode());

        $this->setupDb();
        $this->loginUser(6); // not group-admin

        $response = $this->runApp('PUT', '/api/user/group/69/add-manager/1');
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testAddManager404()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response1 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/add-manager/'.($this->pid + 1));
        $response2 = $this->runApp('PUT', '/api/user/group/'.($this->gid + 1).'/add-manager/'.$this->pid);

        $this->assertEquals(404, $response1->getStatusCode());
        $this->assertEquals(404, $response2->getStatusCode());
    }

    public function testAddManager204()
    {
        $this->setupDb();
        $this->loginUser(8);

        $player = new Player();
        $player->setName('Manager');
        $this->em->persist($player);
        $this->em->flush();

        $response1 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/add-manager/'.$this->pid);
        $response2 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/add-manager/'.$player->getId());
        $this->assertEquals(204, $response1->getStatusCode());
        $this->assertEquals(204, $response2->getStatusCode());

        $this->em->clear();

        $actual = [];
        $group = $this->gr->find($this->gid);
        foreach ($group->getManagers() as $mg) {
            $actual[] = $mg->getId();
        }
        $this->assertSame([$this->pid, $player->getId()], $actual);
    }

    public function testRemoveManager403()
    {
        $response = $this->runApp('PUT', '/api/user/group/69/remove-manager/1');
        $this->assertEquals(403, $response->getStatusCode());

        $this->setupDb();
        $this->loginUser(6); // not group-admin

        $response = $this->runApp('PUT', '/api/user/group/69/remove-manager/1');
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testRemoveManager404()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response = $this->runApp('PUT', '/api/user/group/'.($this->gid + 1).'/remove-manager/'.$this->pid);
        $response = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/remove-manager/'.($this->pid + 1));
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testRemoveManager204()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/remove-manager/'.$this->pid);
        $this->assertEquals(204, $response->getStatusCode());

        $player = (new PlayerRepository($this->em))->find($this->pid);
        $actual = [];
        foreach ($player->getManagerGroups() as $mg) {
            $actual[] = $mg->getId();
        }
        $this->assertSame([], $actual);
    }

    public function testApplicants403()
    {
        $this->setupDb();

        $response1 = $this->runApp('GET', '/api/user/group/'.$this->gid.'/applicants');
        $this->assertEquals(403, $response1->getStatusCode());

        $this->loginUser(7); // not manager of that group

        $response2 = $this->runApp('GET', '/api/user/group/'.$this->gid.'/applicants');
        $this->assertEquals(403, $response2->getStatusCode());
    }

    public function testApplicants404()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response = $this->runApp('GET', '/api/user/group/'.($this->gid + 1).'/applicants');
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testApplicants200()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response = $this->runApp('GET', '/api/user/group/'.$this->gid.'/applicants');
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertSame(
            [['id' => $this->aid, 'name' => 'Group']],
            $this->parseJsonBody($response)
        );
    }

    public function testAddMember403()
    {
        $this->setupDb();

        $response = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/add-member/'.$this->pid);
        $this->assertEquals(403, $response->getStatusCode());

        $this->loginUser(7); // manager, but not of this group
        $response = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/add-member/'.$this->pid);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testAddMember404()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response1 = $this->runApp('PUT', '/api/user/group/'.($this->gid + 1).'/add-member/'.$this->pid);
        $this->assertEquals(404, $response1->getStatusCode());

        $response2 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/add-member/'.($this->pid + 1));
        $this->assertEquals(404, $response2->getStatusCode());

        $response3 = $this->runApp('PUT', '/api/user/group/'.($this->gid + 1).'/add-member/'.($this->pid + 1));
        $this->assertEquals(404, $response3->getStatusCode());
    }

    public function testAddMember204()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response1 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/add-member/'.$this->pid); // already member
        $response2 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/add-member/'.$this->aid);
        $response3 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/add-member/'.$this->aid);
        $this->assertEquals(204, $response1->getStatusCode());
        $this->assertEquals(204, $response2->getStatusCode());
        $this->assertEquals(204, $response3->getStatusCode());

        $this->em->clear();

        $group = $this->gr->find($this->gid);
        $this->assertSame(2, count($group->getPlayers()));
    }

    public function testRemoveMember403()
    {
        $this->setupDb();

        $response = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/remove-member/'.$this->pid);
        $this->assertEquals(403, $response->getStatusCode());

        $this->loginUser(7); // manager, but not of this group
        $response = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/remove-member/'.$this->pid);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testRemoveMember404()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response1 = $this->runApp('PUT', '/api/user/group/'.($this->gid + 1).'/remove-member/'.$this->pid);
        $this->assertEquals(404, $response1->getStatusCode());

        $response2 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/remove-member/'.($this->pid + 1));
        $this->assertEquals(404, $response2->getStatusCode());

        $response3 = $this->runApp('PUT', '/api/user/group/'.($this->gid + 1).'/remove-member/'.($this->pid + 1));
        $this->assertEquals(404, $response3->getStatusCode());

    }

    public function testRemoveMember204()
    {
        $this->setupDb();
        $this->loginUser(8);

        $response1 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/remove-member/'.$this->aid); // not member
        $response2 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/remove-member/'.$this->pid);
        $response3 = $this->runApp('PUT', '/api/user/group/'.$this->gid.'/remove-member/'.$this->pid);
        $this->assertEquals(204, $response1->getStatusCode());
        $this->assertEquals(204, $response2->getStatusCode());
        $this->assertEquals(204, $response3->getStatusCode());

        $this->em->clear();

        $group = $this->gr->find($this->gid);
        $this->assertSame(0, count($group->getPlayers()));
    }

    private function setupDb()
    {
        $this->helper->emptyDb();

        $g = $this->helper->addGroups(['group-one']);
        $this->gid = $g[0]->getId();

        $user = $this->helper->addCharacterMain('User', 6, [Roles::USER]);

        // group manager, but not of any group
        $user = $this->helper->addCharacterMain('Group', 7, [Roles::USER, Roles::GROUP_ADMIN]);
        $this->aid = $user->getPlayer()->getId();

        $admin = $this->helper->addCharacterMain('Admin', 8, [Roles::USER, Roles::GROUP_ADMIN], ['group-one']);
        $this->pid = $admin->getPlayer()->getId();

        $g[0]->addManager($admin->getPlayer());
        $user->getPlayer()->addApplication($g[0]);

        $this->em->flush();
    }
}
