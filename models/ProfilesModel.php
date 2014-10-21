<?php
class ProfilesModel extends ModelBase{
    
    public function getAllProfiles()
    {
        //Se realiza la consulta para obtener todos los perfiles de usuario
        $consulta = $this->db->prepare("SELECT 
                        id_profile
                        , label_profile
                    FROM cas_profile 
                    ORDER BY label_profile ");
        
        $consulta->execute();
        
        //Devolvemos la colección para que la vista la presente
        return $consulta;
    }
    
}
