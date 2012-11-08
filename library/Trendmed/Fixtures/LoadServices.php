<?php
namespace Trendmed\Fixtures;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Trendmed\Entity;

/**
 * Created by JetBrains PhpStorm.
 * User: Bard
 * Date: 13.05.12
 * Time: 17:30
 * To change this template use File | Settings | File Templates.
 */
class LoadServices extends \Doctrine\Common\DataFixtures\AbstractFixture implements \Doctrine\Common\DataFixtures\FixtureInterface,
    \Doctrine\Common\DataFixtures\OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $repository = $manager->getRepository('\Trendmed\Entity\Translation');
        $config = \Zend_Registry::get('config');
        $category = $this->getReference('subcategory-2');
        $clinic = $this->getReference('clinic1');

        $service = new \Trendmed\Entity\Service;
        $service->setDescription('To jest opis uslugi');
        $service->setPriceMin(100);
        $service->setPriceMax(200);
        $service->setClinic($clinic);
        $service->setCategory($category);

        $manager->persist($service);
        $clinic2 = $this->getReference('clinic2');

        $service2 = new \Trendmed\Entity\Service;
        $service2->setDescription('To jest opis drugiej uslugi');
        $service2->setPriceMin(1000);
        $service2->setPriceMax(2000);
        $service2->setClinic($clinic2);
        $service2->setCategory($category);

        $manager->persist($service2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }
}