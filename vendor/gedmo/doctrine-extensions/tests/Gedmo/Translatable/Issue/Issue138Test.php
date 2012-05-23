<?php

namespace Gedmo\Translatable;

use Doctrine\Common\EventManager;
use Tool\BaseTestCaseORM;
use Translatable\Fixture\Issue138\Article;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Doctrine\ORM\Query;

/**
 * These are tests for translatable behavior
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @package Gedmo.Translatable
 * @link http://www.gediminasm.org
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Issue138Test extends BaseTestCaseORM
{
    const ARTICLE = 'Translatable\\Fixture\\Issue138\\Article';
    const TRANSLATION = 'Gedmo\\Translatable\\Entity\\Translation';
    const TREE_WALKER_TRANSLATION = 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker';

    private $translatableListener;

    protected function setUp()
    {
        parent::setUp();

        $evm = new EventManager;
        $this->translatableListener = new TranslationListener();
        $this->translatableListener->setTranslatableLocale('en');
        $this->translatableListener->setDefaultLocale('en');
        $this->translatableListener->setTranslationFallback(true);
        $evm->addEventSubscriber($this->translatableListener);

        $this->getMockSqliteEntityManager($evm);
    }

    public function testIssue138()
    {
        $this->populate();
        $dql = 'SELECT a FROM ' . self::ARTICLE . ' a';
        $dql .= " WHERE a.title LIKE '%foo%'";
        $q = $this->em->createQuery($dql);
        $q->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, self::TREE_WALKER_TRANSLATION);

        // array hydration
        $this->translatableListener->setTranslatableLocale('en_us');
        //die($q->getSQL());
        $result = $q->getArrayResult();
        $this->assertEquals(1, count($result));
        $this->assertEquals('Food', $result[0]['title']);
    }

    protected function getUsedEntityFixtures()
    {
        return array(
            self::ARTICLE,
            self::TRANSLATION,

        );
    }

    private function populate()
    {
        $repo = $this->em->getRepository(self::ARTICLE);

        $food = new Article;
        $food->setTitle('Food');
        $food->setTitleTest('about food');

        $citron = new Article;
        $citron->setTitle('Citron');
        $citron->setTitleTest('something citron');

        $this->em->persist($food);
        $this->em->persist($citron);
        $this->em->flush();

        $this->translatableListener->setTranslatableLocale('lt_lt');
        $food->setTitle('Maistas');
        $food->setTitleTest('apie maista');

        $citron->setTitle('Citrina');
        $citron->setTitleTest('kazkas citrina');

        $this->em->persist($food);
        $this->em->persist($citron);
        $this->em->flush();
        $this->em->clear();
    }
}
