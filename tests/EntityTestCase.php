<?php
namespace Trendmed\Entity;

class EntityTestCase extends \DoctrineExtensions\PHPUnit\OrmTestCase {
    protected $em;
    protected function createEntityManager()
    {
        global $application;
        $application->bootstrap();
        $doctrineContainer = \Zend_Registry::get('doctrine');
        return $this->_em = $doctrineContainer->getEntityManager();
    }
    
    public function setUp()
    {
        global $application;
        $application->bootstrap();
        $this->doctrineContainer = Zend_Registry::get('doctrine');

        //self::dropSchema($this->doctrineContainer->getConnection()
          //      ->getParams());
                
        
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->doctrineContainer
                ->getEntityManager());
        $tool->dropDatabase();
        $tool->createSchema(self::getClassMetas(
                APPLICATION_PATH . '/../library/Trendmed/Entity',
                'Trendmed\Entity\\'));
        $this->createSeedData();
        parent::setUp();
    }
    
    protected function getDataSet()
    {
        return $this->createFlatXmlDataSet(__DIR__."/_files/entityFixture.xml");
    }
}


?>
