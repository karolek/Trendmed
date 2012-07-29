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
        $clinic->setDescription('To jest pierwsza placówka testowa w systemie');
        $clinic->setPostcode('80-033');
        $clinic->setType('clinic');
        $clinic->setStreetaddress('Rubinowa 4');
        $clinic->setRepEmail('b@br-design.pl');
        $clinic->setLogin('b@br-design.pl');
        $clinic->setProvince('Pomorskie');
        $clinic->setRepPhone('+48512129709');
        $clinic->setRepName('Bartosz Rychlicki');
        $manager->persist($clinic);

        $clinic2 = new \Trendmed\Entity\Clinic();
        $clinic2->setName('BR-DESIGN');
        $clinic2->setRole($this->getReference('clinic-role'));
        $clinic2->setPassword('nataniel');
        $clinic2->setCity('Poznań');
        $clinic2->setDescription('To jest druga placówka testowa w systemie');
        $clinic2->setPostcode('80-255');
        $clinic2->setType('salon');
        $clinic2->setStreetaddress('Bździa 4');
        $clinic2->setRepEmail('bartosz.rychlicki@gmail.com');
        $clinic2->setLogin('bartosz.rychlicki@gmail.com');
        $clinic2->setProvince('Mazowieckie');
        $clinic2->setRepPhone('+48512129709');
        $clinic2->setRepName('Bartosz Rychlicki');
        $manager->persist($clinic2);

        $clinic3 = new \Trendmed\Entity\Clinic();
        $clinic3->setName('SwissMed');
        $clinic3->setRole($this->getReference('clinic-role'));
        $clinic3->setPassword('nataniel');
        $clinic3->setCity('Warszawa');
        $clinic3->setDescription('To jest trzecia placówka testowa w systemie');
        $clinic3->setPostcode('80-255');
        $clinic3->setType('hospital');
        $clinic3->setStreetaddress('Bździa 4');
        $clinic3->setRepEmail('b.a.r.t.o.s.z.rychlicki@gmail.com');
        $clinic3->setLogin('b.a.r.t.o.s.z.rychlicki@gmail.com');
        $clinic3->setProvince('Mazowieckie');
        $clinic3->setRepPhone('+48512129709');
        $clinic3->setRepName('Bartosz Rychlicki');
        $manager->persist($clinic2);


        $manager->flush();

        // storeing reference for later us in another fixtures
        $this->addReference('clinic1', $clinic);
        $this->addReference('clinic2', $clinic2);
        $this->addReference('clinic3', $clinic3);
    }

    public function getOrder()
    {
        return 3;
    }
}
