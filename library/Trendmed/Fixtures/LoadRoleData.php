<?php
namespace Trendmed\Fixtures;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Trendmed\Entity;
/**
 * Created by JetBrains PhpStorm.
 * User: Bard
 * Date: 12.05.12
 * Time: 13:36
 * To change this template use File | Settings | File Templates.
 */
class LoadRoleData extends \Doctrine\Common\DataFixtures\AbstractFixture implements \Doctrine\Common\DataFixtures\FixtureInterface,
    \Doctrine\Common\DataFixtures\OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $role = new \Trendmed\Entity\Role();
        $role->setName('guest');
        $manager->persist($role);

        $role2 = new \Trendmed\Entity\Role();
        $role2->setName('clinic');
        $manager->persist($role2);

        $role3 = new \Trendmed\Entity\Role();
        $role3->setName('patient');
        $manager->persist($role3);

        $role4 = new \Trendmed\Entity\Role();
        $role4->setName('admin');
        $manager->persist($role4);

        $manager->flush();

        // storeing reference for later us in another fixtures
        $this->addReference('admin-role', $role4);
        $this->addReference('guest-role', $role);
        $this->addReference('clinic-role', $role2);
        $this->addReference('patient-role', $role3);

    }

    public function getOrder()
    {
        return 1;
    }
}
