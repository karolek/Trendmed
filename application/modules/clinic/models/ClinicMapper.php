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
            'lastlogintime'     => $model->getLastLoginTime(),
            'wantbill'          => $model->getWantBill(),        
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
    
    protected function _createNewModelFromRow($row)
    {
        $model = new Clinic_Model_Clinic();
        // TODO: use populate on model
        $model->name = $row->name;
        $model->street = $row->street;
        $model->city = $row->city;
        $model->postcode = $row->postcode;
        $model->id = $row->id;
        $model->province = $row->province;
        $model->representantname = $row->representantname;
        $model->representantphone = $row->representantphone;
        $model->email = $row->email;
        $model->password = $row->password;
        $model->salt = $row->salt;
        $model->aclrole_id = $row->aclrole_id;
        $model->created = $row->created;
        $model->modified = $row->modified;
        $model->token = $row->token;
        $model->tokenValidUntil = $row->tokenvaliduntil;
        $model->lastLoginTime = $row->lastlogintime;
        $model->wantBill = $row->wantbill;
        $model->bankAccount = $row->bankaccount;

        return $model;
    }
}

