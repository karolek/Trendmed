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
class LoadClinicData extends \Doctrine\Common\DataFixtures\AbstractFixture implements \Doctrine\Common\DataFixtures\FixtureInterface,
    \Doctrine\Common\DataFixtures\OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $clinic = new \Trendmed\Entity\Clinic();
        $clinic->setName('Trendmed');
        $clinic->setRole($this->getReference('clinic-role'));
        $clinic->setPassword('nataniel');
        $clinic->setCity('Gdańsk');
        $clinic->setDescription('To jest pierwsza klinika testowa w systemie');
        $clinic->setPostcode('80-033');
        $clinic->setType('Klinika');
        $clinic->setStreetaddress('Rubinowa 4');
        $clinic->setRepEmail('b@br-design.pl');
        $clinic->setProvince('Pomorskie');
        $clinic->setRepPhone('+48512129709');
        $clinic->setRepName('Bartosz Rychlicki');
        $manager->persist($clinic);
        $manager->flush();

        // storeing reference for later us in another fixtures
        $this->addReference('clinic1', $clinic);
    }

    public function getOrder()
    {
        return 3;
    }
}
