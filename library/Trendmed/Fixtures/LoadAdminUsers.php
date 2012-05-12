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
class LoadAdminUsers extends \Doctrine\Common\DataFixtures\AbstractFixture implements \Doctrine\Common\DataFixtures\FixtureInterface,
    \Doctrine\Common\DataFixtures\OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $admin = new \Trendmed\Entity\Admin();
        $admin->setLogin('admin');
        $admin->setPassword('admin');
        $admin->setRole($this->getReference('admin-role'));
        $manager->persist($admin);
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
