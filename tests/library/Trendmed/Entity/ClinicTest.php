<?php
namespace Trendmed\Entity;
use \Doctrine\Common\Util\Debug as Debug;
/**
 * Description of CategoryTestCase
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class ClinicTest extends \ModelTestCase {
	
    public function testAddingTheSameClinicWillFail()
    {
        $this->setExpectedException('\PDOException');
        $clinic = $this->em->getRepository('\Trendmed\Entity\Clinic')
                ->findOneAsArray(1);

        $clinic2 = new \Trendmed\Entity\Clinic;
        $clinic2->setOptions($clinic);
        try {
            $clinic2->setRole($this->em->getRepository('\Trendmed\Entity\Role')->findOneByName('clinic'));
            $this->em->persist($clinic2);
            $this->em->flush();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $this->fail('You should not be able to save the same clinic,'. $clinic2->name);
    }
    
    public function testFetchedEntityIsTheSame()
    {
        $clinic = $this->em->getRepository('\Trendmed\Entity\Clinic')
                ->findOneByName('TrendMed');
        
        // assertions
        $this->assertEquals('Gdańsk', $clinic->getCity());
        $this->assertEquals(false, $clinic->getWantbill());
    }
    
    public function testIfModifiedTimeIsCorrect()
    {
        $clinic = $this->em->getRepository('\Trendmed\Entity\Clinic')
                ->findOneByName('TrendMed');
        $timeBefore = time();
        $clinic->city = 'Poznań';
        $this->em->persist($clinic);
        $this->em->flush();
        $timeAfter = time();
        $this->assertTrue(($clinic->modified->getTimestamp() >= $timeBefore) AND ($clinic->modified->getTimestamp() <= $timeAfter));
    }
    
    public function testCanAuthorizeUser()
    {
        \Zend_Session::$_unitTestEnabled = true;
        $clinic = $this->em->getRepository('\Trendmed\Entity\Clinic')
                ->findOneByLogin('b@br-design.pl');        
        $result = $clinic->authorize('nataniel');
        $this->assertTrue($result);
    }
    
    public function testWrongPasswordWillNotAuthorize()
    {
        \Zend_Session::$_unitTestEnabled = true;
        $clinic = $this->em->getRepository('\Trendmed\Entity\Clinic')
                ->findOneByLogin('b@br-design.pl');        
        $result = $clinic->authorize('nataniel1');
        $this->assertFalse($result);       
    }
    
    public function testClinicWillSetUpLoginFromRepEmailAndRole()
    {
        $clinic = new \Trendmed\Entity\Clinic;
        $clinic->name = 'TrendMed2';
        $clinic->streetaddress = 'Topolowa 2/7';
        $clinic->province = 'Pomorskie';
        $clinic->city = 'Gdańsk';
        $clinic->postcode = '80-233';
        $clinic->repPhone = '+48 512 129 709';
        $clinic->repName = 'Bartosz';
        $clinic->repEmail = 'b@br-design.pl';
        $clinic->type = 'Clinic';
        $clinic->password = 'nataniel';
        $clinic->salt = $clinic->generateSalt();
        $clinic->geoLat = 54.377608;
        $clinic->getLon = 18.595605;
        
        $role = $this->em->getRepository('\Trendmed\Entity\Role')
            ->findOneByName($clinic->getRoleName());
        if(!$role) throw new Exception('Given role ('.$model->getRoleName().'
            not found in DB');
        $clinic->setRole($role);
        
        $this->em->persist($clinic);
        
        $this->assertNotEmpty($clinic->getRole());
        $this->assertEquals('b@br-design.pl', $clinic->login);
    }
    
}