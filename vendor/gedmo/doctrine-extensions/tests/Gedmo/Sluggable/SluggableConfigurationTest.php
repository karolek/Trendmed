<?php

namespace Gedmo\Sluggable;

use Doctrine\Common\EventManager;
use Tool\BaseTestCaseORM;
use Doctrine\Common\Util\Debug,
    Sluggable\Fixture\ConfigurationArticle;

/**
 * These are tests for Sluggable behavior
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @package Gedmo.Sluggable
 * @link http://www.gediminasm.org
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SluggableConfigurationTest extends BaseTestCaseORM
{
    const ARTICLE = 'Sluggable\\Fixture\\ConfigurationArticle';

    private $articleId;

    protected function setUp()
    {
        parent::setUp();

        $evm = new EventManager;
        $evm->addEventSubscriber(new SluggableListener);

        $this->getMockSqliteEntityManager($evm);
        $this->populate();
    }

    public function testInsertedNewSlug()
    {
        $article = $this->em->find(self::ARTICLE, $this->articleId);

        $this->assertTrue($article instanceof Sluggable);
        $this->assertEquals($article->getSlug(), 'the-title-my-code');
    }

    public function testNonUniqueSlugGeneration()
    {
        for ($i = 0; $i < 5; $i++) {
            $article = new ConfigurationArticle();
            $article->setTitle('the title');
            $article->setCode('my code');

            $this->em->persist($article);
            $this->em->flush();
            $this->em->clear();
            $this->assertEquals($article->getSlug(), 'the-title-my-code');
        }
    }

    public function testSlugLimit()
    {
        $long = 'the title the title the title the title the';
        $article = new ConfigurationArticle();
        $article->setTitle($long);
        $article->setCode('my code');

        $this->em->persist($article);
        $this->em->flush();
        $this->em->clear();

        $shorten = $article->getSlug();
        $this->assertEquals(strlen($shorten), 32);
    }

    public function testNonUpdatableSlug()
    {
        $article = $this->em->find(self::ARTICLE, $this->articleId);
        $article->setTitle('the title updated');
        $this->em->persist($article);
        $this->em->flush();
        $this->em->clear();

        $this->assertEquals($article->getSlug(), 'the-title-my-code');
    }

    protected function getUsedEntityFixtures()
    {
        return array(
            self::ARTICLE,
        );
    }

    private function populate()
    {
        $article = new ConfigurationArticle();
        $article->setTitle('the title');
        $article->setCode('my code');

        $this->em->persist($article);
        $this->em->flush();
        $this->em->clear();
        $this->articleId = $article->getId();
    }

    public function testUpdatableFalseSetSlug()
    {
        $article = new ConfigurationArticle();
        $article->setTitle('my title');
        $article->setCode('my code');

        $article->setSlug('my-slug');

        $this->em->persist($article);
        $this->em->flush();

        $this->assertEquals($article->getSlug(), 'my-slug');
    }

    public function testUpdatableFalseChangeSlug()
    {
        $article = $this->em->find(self::ARTICLE, $this->articleId);
        $article->setSlug('my-new-slug');

        $this->em->persist($article);
        $this->em->flush();

        $this->assertSame('my-new-slug', $article->getSlug());
    }

    public function testUpdatableFalseChangeTitle()
    {
        $article = $this->em->find(self::ARTICLE, $this->articleId);
        $article->setTitle('my new title');

        $this->em->persist($article);
        $this->em->flush();

        $this->assertSame('the-title-my-code', $article->getSlug());
    }
    public function testUpdatableFalseSetSlugNull()
    {
        $article = $this->em->find(self::ARTICLE, $this->articleId);
        $article->setTitle('new');
        $article->setSlug('');

        $this->em->persist($article);
        $this->em->flush();

        $this->assertEquals($article->getSlug(), 'new-my-code');
    }
}

