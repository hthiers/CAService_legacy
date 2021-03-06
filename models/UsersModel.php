<?php
class UsersModel extends ModelBase
{
        public function getAllUserAccountByTenant($id_tenant)
	{
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("SELECT 
                                id_user
                                , code_user
                                , id_tenant
                                , name_user
                                , id_profile
                            FROM cas_user
                            WHERE id_tenant = '$id_tenant'");
                
		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}
    
	public function getUserAccount($username, $password)
	{
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("SELECT 
                                id_user
                                , code_user
                                , id_tenant
                                , name_user
                                , id_profile
                            FROM cas_user
                            WHERE name_user='$username'
                              AND password_user='$password'");
                
		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}
        
        public function getUserAccountByID($id_user)
	{
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("SELECT 
                                 id_user
                                , code_user
                                , id_tenant
                                , name_user
                                , id_profile
                            FROM cas_user WHERE id_user='$id_user'");
		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}
        
        public function getUserAccountByCode($code_user)
	{
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("SELECT 
                                 id_user
                                , code_user
                                , id_tenant
                                , name_user
                                , id_profile
                            FROM cas_user WHERE code_user='$code_user'");
		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}

        public function getUserInfo($id, $username)
	{
            try
            {
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("SELECT ID,USUARIO,NOMBRE,APELLIDOP, APELLIDOM,PRIVILEGIO
                            FROM t_usuario 
                            WHERE ID = '$id'
                              AND USUARIO='$username'");
                
		$consulta->execute();
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
		
            return $consulta;
	}
        
        public function getUserProfileByID($id)
	{
            try
            {
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("SELECT ID,USUARIO,PRIVILEGIO
                            FROM t_usuario 
                            WHERE ID = '$id'");
                
		$consulta->execute();
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
		
            return $consulta;
	}
        
        public function getUserModulePrivilegeByModule($id, $cod_modulo)
	{
            try
            {
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("
                            SELECT 
                                ID
                                ,USUARIO
                                ,PRIVILEGIO
                                ,C.VER
                                ,C.ESCRIBIR
                                ,C.EDITAR
                            FROM t_usuario A
                            INNER JOIN t_privilegios B
                            ON A.PRIVILEGIO = B.COD_PRIVILEGIO
                            INNER JOIN t_privilegios_permisos C
                            ON B.COD_PRIVILEGIO = C.COD_PRIVILEGIO
                            WHERE ID = '$id'
                              AND C.COD_MODULO = '$cod_modulo'");
                
		$consulta->execute();
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
		
            return $consulta;
	}

        public function getUserInfoPassword($id, $username)
	{
            try
            {
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("SELECT ID, USUARIO, CLAVE
                            FROM t_usuario
                            WHERE ID = '$id'
                              AND USUARIO = '$username'");
                
		$consulta->execute();
            }
            catch(PDOException $e)
            {
                #return $e->getMessage();
                return 0;
            }
		
            return $consulta;
	}
        
        public function editUserInfo($id, $username, $name, $apellidop, $apellidom)
        {
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_usuario WHERE '$username'";
            
            $session = FR_Session::singleton();
            
            try
            {
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("UPDATE t_usuario
                            SET NOMBRE = '$name'
                                , APELLIDOP = '$apellidop'
                                , APELLIDOM = '$apellidom'
                            WHERE ID = '$id'
                              AND USUARIO = '$username'");
                
		$consulta->execute();
                
                //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
                $logModel->addNewEvent($session->usuario, $sql, 'USERS');
            }
            catch(PDOException $e)
            {
                #return $e->getMessage();
                return 0;
            }
		
            return $consulta;
        }
        
        public function editUserAccount($id, $username, $name, $apellidop, $apellidom, $privilegio, $password)
        {
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_usuario WHERE '$username'";
            
            //Asegurar que session esta abierta
            $session = FR_Session::singleton();
            
            //New pass
            if($password != "")
                $new_password = md5($password);
            else
                $new_password = "";
   
            //password change requested
            if($new_password != "")
            {
                //realizamos la consulta de todos los segmentos
                $consulta = $this->db->prepare("UPDATE t_usuario
                        SET NOMBRE = '$name'
                            , APELLIDOP = '$apellidop'
                            , APELLIDOM = '$apellidom'
                            , USUARIO = '$username'
                            , PRIVILEGIO = '$privilegio'
                            , CLAVE = '$new_password'
                        WHERE ID = '$id'");
            }
            else
            {
                //realizamos la consulta de todos los segmentos
                $consulta = $this->db->prepare("UPDATE t_usuario
                        SET NOMBRE = '$name'
                            , APELLIDOP = '$apellidop'
                            , APELLIDOM = '$apellidom'
                            , USUARIO = '$username'
                            , PRIVILEGIO = '$privilegio'
                        WHERE ID = '$id'");
            }

            $consulta->execute();
            
            //Save to events log
            $logModel->addNewEvent($session->usuario, $sql, 'USERS');
            
            return $consulta;
        }
        
        public function editUserPassword($id, $username, $password)
        {
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_usuario PASSW WHERE '$username'";
            
            $session = FR_Session::singleton();
            
            try
            {
                $new_password = md5($password);
                
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("UPDATE t_usuario
                            SET CLAVE = '$new_password'
                            WHERE ID = '$id'
                              AND USUARIO = '$username'");
                
		$consulta->execute();
                
                //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
                $logModel->addNewEvent($session->usuario, $sql, 'USERS');
            }
            catch(PDOException $e)
            {
                return $e->getCode();
                #return 0;
            }
		
            return $consulta;
        }
        
        public function addNewUserAlpha($nombre, $apellidop, $apellidom, $username, $password, $privilegio)
        {
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_usuario VALUES '$username'";
            
            $session = FR_Session::singleton();
            
            try
            {
                $new_password = md5($password);
                
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("INSERT INTO t_usuario
                                (NOMBRE, APELLIDOP, APELLIDOM, USUARIO, CLAVE, PRIVILEGIO)
                                VALUES
                                ('$nombre', '$apellidop', '$apellidom', '$username', '$new_password', $privilegio)");
                
		$consulta->execute();
                
                //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
                $logModel->addNewEvent($session->usuario, $sql, 'USERS');
            }
            catch(PDOException $e)
            {
                #return $e->getMessage();
                return 0;
            }
		
            return $consulta;
        }
        
        public function getAllUserAccounts()
	{
            try {
                $consulta = $this->db->prepare("
                    SELECT 
                        ID
                        ,USUARIO
                        ,NOMBRE
                        ,APELLIDOP
                        ,APELLIDOM
                        ,PRIVILEGIO
                        ,B.NAME_PRIVILEGIO
                    FROM t_usuario as A
                    INNER JOIN t_privilegios as B
                    ON A.PRIVILEGIO = B.COD_PRIVILEGIO");
                
		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
                
            } catch (PDOException $exc) {
                return $exc->getCode();
            }
            
            return null;
	}
        
        public function deleteUserAccount($id_user)
        {
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            
            $session = FR_Session::singleton();
            
            $consulta = $this->db->prepare("DELETE FROM t_usuario WHERE ID = $id_user");
                
            if($consulta->execute()){
                if($consulta->rowCount() > 0){
                    //Save to events log
                    $sql = "DELETE FROM t_usuario WHERE '$id_user'";
                    $logModel->addNewEvent($session->usuario, $sql, 'USERS');
                }
            }
            
            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
        }
        
        //Nuevos métodos para contexto de user
        public function addNewUser($id_tenant, $code_user, $name_user, $id_profile, $password_user)
        {
            $name = empty($name_user) ? "NULL" : "$name_user";
            $password = md5($password_user);
            
            $consulta = $this->db->prepare("INSERT INTO cas_user "
                    . "(id_user, code_user, id_tenant, name_user, "
                    . "id_profile, password_user) VALUES (NULL, "
                    . "'$code_user', $id_tenant, '$name', $id_profile, '$password');");
            
            $consulta->execute();
            
            return $consulta;
            
        }
        
        public function getLastCodeUser()
        {
            $consulta = $this->db->prepare("SELECT MAX( code_user ) 'code' FROM cas_user");
            $consulta->execute();
            return $consulta;
        } 
        
        public function getUserById(UserVO $user)
        {
            $query = $this->db->prepare("SELECT id_user, code_user, id_tenant, name_user, "
                    . "id_profile, password_user from cas_user where id_user =".$user->getIdUser());
            $query->execute();
            return $query;
        }
        
        public function editUser(UserVO $user)
        {   
            $query = $this->db->prepare("UPDATE cas_user set code_user='".$user->getCodeUser()."', "
                    . "id_tenant=".$user->getIdTenant().", name_user='".$user->getNameUser()."', "
                    . "id_profile=".$user->getIdProfile().", password_user='".$user->getPasswordUser()."' "
                    . "where id_user=".$user->getIdUser());
            $query->execute();
            return $query; 
        }
        
        public function getAllUsers(UserVO $user)
        {
            $query = $this->db->prepare("SELECT u.id_user, u.code_user, u.id_tenant, u.name_user, p.label_profile "
                    . "from cas_user u inner join cas_profile p on u.id_profile = p.id_profile where u.id_tenant = ".$user->getIdTenant()." "
                    . "order by u.name_user");
        
            $query->execute();
            return $query;
        }
        
        public function getBoolUsername(UserVO $user)
        {
            $query = $this->db->prepare("SELECT if(COUNT(*)>0,'true','false') AS result FROM cas_user "
                    . "WHERE id_tenant= ".$user->getIdTenant()." AND name_user LIKE '".$user->getNameUser()."'");
            
            $query->execute();
            return $query;
        }
        
        public function removeUserAction()
        {
        
        }
        
        /**
         * Get PDO object from custom sql query
         * NOTA: Esta función impide tener un control de la consulta sql (depende desde donde se llame).
         * @param string $sql
         * @return PDO
         */
        public function goCustomQuery($sql)
        {
            $consulta = $this->db->prepare($sql, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

            $consulta->execute();
            //print_r($consulta);
            return $consulta;
            //print_r($consulta);
        }
        
        /**
         * Get database table name linked to this model
         * NOTA: Solo por lógica modelo = tabla
         * @return string 
         */
        public function getTableName()
        {
            $tableName = "cas_user";
            
            return $tableName;
        }
}
?>