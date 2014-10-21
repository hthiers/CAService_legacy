<?php

/* Value Object para manejar datos de perfiles */
class ProfileVO 
{
    
    private $id_profile;
    private $code_profile;
    private $label_profile;
    
    public function ProfileVO()
    {
        
    }
    
    public function setIdProfile($id_profile)
    {
        $this->id_profile = $id_profile;
    }
    
    public function getIdProfile()
    {
        return $this->id_profile;
    }
    
    public function setCodeProfile($code_profile)
    {
        $this->code_profile = $code_profile;
    }
    
    public function getCodeProfile()
    {
        return $this->code_profile;
    }
    
    public function setLabelProfile($label_profile)
    {
        $this->label_profile = $label_profile;
    }
    
    public function getLabelProfile()
    {
        return $this->label_profile;
    }
}
