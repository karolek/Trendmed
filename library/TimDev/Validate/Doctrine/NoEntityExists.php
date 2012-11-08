<?php
namespace TimDev\Validate\Doctrine;

class NoEntityExists extends AbstractValidator{

    private $_ec = null;
    private $_property = null;
    private $_exclude = null;

    const ERROR_ENTITY_EXISTS = 1;

    protected $_messageTemplates = array(
        self::ERROR_ENTITY_EXISTS => 'Another record already contains %value%'
    );

    public function __construct($opts){
        $this->_ec = $opts['class'];
        $this->_property = $opts['property'];
        $this->_exclude = $opts['exclude'];
        parent::__construct($opts['entityManager']);

    }

    public function getQuery(){
        $qb = $this->em()->createQueryBuilder();
        $qb->select('o')
            ->from($this->_ec,'o')
            ->where('o.' . $this->_property .'=:value');

        if ($this->_exclude !== null){
            if (is_array($this->_exclude)){

                foreach($this->_exclude as $k=>$ex){
                    $qb->andWhere('o.' . $ex['property'] .' != :value'.$k);
                    $qb->setParameter('value'.$k,$ex['value'] ? $ex['value'] : '');
                }
            }
        }
        $query = $qb->getQuery();
        return $query;
    }
    public function isValid($value){
        $valid = true;

        $this->_setValue($value);

        $query = $this->getQuery();
        $query->setParameter("value", $value);

        $result = $query->execute();

        if (count($result)){
            $valid = false;
            $this->_error(self::ERROR_ENTITY_EXISTS);
        }
        return $valid;

    }
}