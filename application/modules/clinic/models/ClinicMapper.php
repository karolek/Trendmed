<?php
class Clinic_Model_ClinicMapper extends Me_Model_Mapper_Abstract
implements Me_User_Model_User_Mapper_Interface
{
    protected $_dbTable = 'Clinic_Model_DbTable_Clinic';

    public function save(Clinic_Model_Clinic $model)
    {
        $data = array(
            'name'              => $model->getName(),
            'street'            => $model->getStreet(),
            'city'              => $model->getCity(),
            'province'          => $model->getProvince(),
            'postcode'          => $model->getPostcode(),
            'representantname'  => $model->getRepresentantname(),
            'representantphone' => $model->getRepresentantphone(),
            'email'             => $model->getEmail(),
            'password'          => $model->getPassword(),
            'salt'              => $model->getSalt(),
            'aclrole_id'        => $model->getRole()->id,
            'token'             => $model->getToken(),
            'tokenvaliduntil'   => $model->getTokenValidUntil(),
        );
 
        if (null === ($id = $model->getId())) {
            unset($data['id']);
            $data['created']  = time();
            $id = $this->getDbTable()->insert($data);
        } else {
            $data['modified'] = time();
           $id = $this->getDbTable()->update($data, array('id = ?' => $id));
        }
        $model->id = $id;
        return $id;
    }
    
    public function findByUsername($username)
    {
        $row = $this->getDbTable()->findByUsername($username);
        if(!$row) return;
        $entry = $this->_createNewModelFromRow($row);
        return $entry;    
    }
    
    public function findByToken($token)
    {
        $row = $this->getDbTable()->findByToken($token);
        if(!$row) return;
        $entry = $this->_createNewModelFromRow($row);
        return $entry;    
    }
}

