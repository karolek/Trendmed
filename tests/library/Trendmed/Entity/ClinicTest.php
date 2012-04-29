<?php
namespace Trendmed\Entity;
/**
 * Description of CategoryTestCase
 *
 * @author Bartosz Rychlicki <bartosz.rychlicki@gmail.com>
 */
class ClinicTest extends \ModelTestCase {
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