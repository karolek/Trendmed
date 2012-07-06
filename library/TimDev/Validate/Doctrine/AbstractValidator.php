<?php
namespace TimDev\Validate\Doctrine;

abstract class AbstractValidator extends \Zend_Validate_Abstract{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $_em;


    public function __construct(\Doctrine\ORM\EntityManager $em){
        $this->_em = $em;
    }

    public function em(){
        return $this->_em;
    }
}