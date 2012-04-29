<?php
use \PHPUnit_Framework_TestCase;
/**
 * Description of ModelTestCase
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class ModelTestCase extends \PHPUnit_Framework_TestCase {
    /**
     *
     * @var \Bisna\Application\Container\DoctrineContainer
     */
    protected $doctrineContainer;
    protected $em;
    
    public function setUp()
    {
        global $application;
        $application->bootstrap();
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer
                ->getEntityManager();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->dropDatabase();
        $tool->createSchema(self::getClassMetas(
                APPLICATION_PATH . '/../library/Trendmed/Entity',
                'Trendmed\Entity\\'));
        $this->createSeedData();
        parent::setUp();
    }

    public function getClassMetas($path, $namespace) {
        $metas = array();
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if (strstr($file, '.php')) {
                    list($class) = explode('.', $file);
                    $metas[] = $this->doctrineContainer->
                                    getEntityManager()->getClassMetadata($namespace . $class);
                }
            }
        }
        return $metas;
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public static function dropSchema($params) 
    {
        $path = $params['path'];
        if(file_exists($path)) {
            unlink($path);
        }
    }
    
    private function createSeedData()
    {
        $role = new Trendmed\Entity\Role;
        $role->name = 'clinic';
        $this->em->persist($role);
        
        $clinic = new Trendmed\Entity\Clinic;
        $clinic->name = 'TrendMed';
        $clinic->streetaddress = 'Topolowa 2/7';
        $clinic->province = 'Pomorskie';
        $clinic->city = 'Gdańsk';
        $clinic->postcode = '80-233';
        $clinic->repPhone = '+48 512 129 709';
        $clinic->repName = 'Bartosz';
        $clinic->repEmail = 'b@br-design.pl';
        $clinic->type = 'Clinic';
        $clinic->login = $clinic->getRepEmail();
        $clinic->password = 'nataniel';
        $clinic->salt = $clinic->generateSalt();
        $clinic->geoLat = 54.377608;
        $clinic->getLon = 18.595605;
        $clinic->setRole($role);
        
        $this->em->persist($clinic);
        $this->em->flush();
    }
}