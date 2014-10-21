<?php

/* Value Object para manejar datos de usuario */
class UserVO {
        
    private $id_user;
    private $code_user;
    private $id_tenant;
    private $name_user;
    private $id_profile;
    private $password_user;
    
    public function UserVO()
    {
        
    }
    
    //ID USER
    public function setIdUser($id_user)
    {
        $this->id_user = $id_user;
    }
    
    public function getIdUser()
    {   
        return $this->id_user;
    }
    
    //CODE USER
    public function setCodeUser($code_user)
    {
        $this->code_user = $code_user;
    }
    
    public function getCodeUser()
    {
        return $this->code_user;
    }
    
    //ID TENANT
    public function setIdTenant($id_tenant)
    {
        $this->id_tenant = $id_tenant;
    }
    
    public function getIdTenant()
    {
        return $this->id_tenant;
    }
    
    //USER NAME|
    public function setNameUser($name_user)
    {
        $this->name_user = $name_user;
    }
    
    public function getNameUser()
    {
        return $this->name_user;
    }
    
    //ID PROFILE
    public function setIdProfile($id_profile)
    {
        $this->id_profile =$id_profile;
    }
    
    public function getIdProfile()
    {
        return $this->id_profile;
    }
    
    //PASSWORD USER
    public function setPasswordUser($password_user)
    {
        $this->password_user = $password_user;
    }
    
    public function getPasswordUser()
    {
        return $this->password_user;
    }
}
