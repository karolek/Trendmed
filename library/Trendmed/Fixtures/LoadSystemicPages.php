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
class LoadSystemicPages extends \Doctrine\Common\DataFixtures\AbstractFixture implements \Doctrine\Common\DataFixtures\FixtureInterface,
    \Doctrine\Common\DataFixtures\OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $repository = $manager->getRepository('\Trendmed\Entity\Page');
        $config = \Zend_Registry::get('config');
        $page = new \Trendmed\Entity\Page();
        $page->setTitle('Regulamin dla placówek');
        $page->setContent('To jest regulamin dla placówek, proszę wydetuj mnie w panelu administratora');
        $page->setActive(true);
        $page->setSlug('regulamin-dla-klinik');
        $page->setIsSystemic(true);

        $manager->persist($page);

        $page = new \Trendmed\Entity\Page();
        $page->setTitle('Regulamin dla pacjentów');
        $page->setContent('To jest regulamin dla pacjentów, proszę wydetuj mnie w panelu administratora');
        $page->setActive(true);
        $page->setSlug('regulamin-dla-pacjentow');
        $page->setIsSystemic(true);

        $manager->persist($page);
        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}