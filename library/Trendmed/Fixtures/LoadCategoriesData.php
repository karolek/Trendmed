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
class LoadCategoriesData extends \Doctrine\Common\DataFixtures\AbstractFixture implements \Doctrine\Common\DataFixtures\FixtureInterface,
    \Doctrine\Common\DataFixtures\OrderedFixtureInterface
{

    private $_categoryPolishNames = array(
      array('pl_PL' => 'Zabiegi chirugiczne', 'en_GB' => 'English name', 'de_DE' => 'Germanishe version' ),
      array('pl_PL' => 'Pobyty SPA', 'en_GB' => 'English name', 'de_DE' => 'Germanishe version' ),
      array('pl_PL' => 'Pobyty w sanatoriach', 'en_GB' => 'English name', 'de_DE' => 'Germanishe version' ),
    );

    private $_subcategoriesNames = array(
        array('pl_PL' => 'Odsysanie tłuszczu', 'en_GB' => 'English name', 'de_DE' => 'Germanishe version' ),
        array('pl_PL' => 'Wybielanie zębów', 'en_GB' => 'English name', 'de_DE' => 'Germanishe version' ),
        array('pl_PL' => 'Powiększanie piersi', 'en_GB' => 'English name', 'de_DE' => 'Germanishe version' ),
    );

    public function load(ObjectManager $manager)
    {
        $root = new \Trendmed\Entity\Category;
        $root->setName('root');
        $manager->persist($root);

        $repository = $manager->getRepository('\Trendmed\Entity\Translation');
        $config = \Zend_Registry::get('config');
        foreach($this->_categoryPolishNames as $category) {
            $categoryObject = new \Trendmed\Entity\Category();
            foreach ($config->languages as $lang) {
                if ($lang->default == true) { // we must add default values to our main entity
                    $categoryObject->setName($category[$lang->code]);
                    continue;
                }
                $repository->translate(
                    $categoryObject, 'name', $lang->code,
                    $category[$lang->code]
                );
            }
            $categoryObject->setParent($root);
            $manager->persist($categoryObject);
            $lastCategory = $categoryObject;
        }

        $i = 1;
        foreach ($this->_subcategoriesNames as $subcategoryName) {
            $categoryObject = new \Trendmed\Entity\Category();
            foreach ($config->languages as $lang) {
                if ($lang->default == true) { // we must add default values to our main entity
                    $categoryObject->setName($subcategoryName[$lang->code]);
                    continue;
                }
                $repository->translate(
                    $categoryObject, 'name', $lang->code,
                    $subcategoryName[$lang->code]
                );
            }
            $categoryObject->setParent($lastCategory);
            $manager->persist($categoryObject);
            $this->addReference('subcategory-'.$i, $categoryObject);
            $i++;
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}