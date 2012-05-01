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
        $this->setExpectedException('PDOException');
        $clinic = $this->em->getRepository('\Trendmed\Entity\Clinic')
                ->findOneAsArray(1);
        $clinic2 = new \Trendmed\Entity\Clinic;
        $clinic2->setOptions($clinic);
        try {
            $this->em->persist($clinic2);
            $this->em->flush();
        } catch (Exception $e) {
            
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
    
}