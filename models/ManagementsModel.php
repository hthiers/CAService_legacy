<?php
class ManagementsModel extends ModelBase
{
	/*******************************************************************************
	* CUSTOMERS
	*******************************************************************************/

	/**
         * Get all managements by tenant
         * @param int $id_tenant
         * @return pdo
         */
	public function getAllManagementsByTenant($id_tenant)
	{
                $consulta = $this->db->prepare("
                        select 
                            a.id_management
                            , a.code_management
                            , a.label_management
                        from cas_management a
                        where a.id_tenant = $id_tenant 
                        and a.status_management < 9
                        order by a.label_management asc");

		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}
        
        /**
         * Get managements by customer (and tenant)
         * @param int $id_tenant
         * @return pdo
         */
	public function getManagementsByCustomer($id_tenant, $id_customer)
	{
                $consulta = $this->db->prepare("
                        select 
                            a.id_management
                            , a.code_management
                            , a.label_management
                        from cas_management a
                        where a.id_tenant = $id_tenant 
                        and a.id_customer = $id_customer 
                        and a.status_management < 9
                        order by a.label_management asc");

		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}
        
        /**
         * Get managements by other customer
         * @param int $id_tenant
         * @return pdo
         */
	public function getManagementsOtherCustomer($id_tenant, $id_customer)
	{
                $consulta = $this->db->prepare("
                        select 
                            a.id_management
                            , a.code_management
                            , a.label_management
                        from cas_management a
                        where a.id_tenant = $id_tenant 
                        and a.status_management < 9
                        order by a.label_management asc");

		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}
        
        /**
         * Get all managements by tenant
         * @param int $id_tenant
         * @return pdo
         */
	public function getManagements($id_tenant)
	{
                $consulta = $this->db->prepare("
                        select 
                            a.id_management
                            , a.code_management
                            , a.label_management
                        from cas_management a
                        where a.id_tenant = $id_tenant 
                        and a.status_management < 9
                        order by a.label_management asc");

		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}
        
        /**
         * Get managements por código de management
         * @param int $id_tenant
         * @param varchar $code_management
         * @return PDO 
         */
        public function getManagementByCode($id_tenant, $code_management)
        {
            $consulta = $this->db->prepare("
				SELECT id_management
                                    , code_management
                                    , id_tenant
                                    , label_management
                                FROM cas_management
                                WHERE code_management = '$code_management'
                                  and id_tenant = $id_tenant");
            
            $consulta->execute();

            return $consulta;
        }
        
        /**
         * Get management por ID de management (materia)
         * @param int $id_tenant
         * @param varchar $id_management
         * @return PDO 
         */
        public function getManagementByID($id_tenant, $id_management)
        {
            $consulta = $this->db->prepare("
				SELECT id_management 
                                    , code_management
                                    , id_tenant
                                    , label_management
                                FROM cas_management a
                                WHERE id_management = '$id_management'
                                  and id_tenant = $id_tenant");
            
            $consulta->execute();

            return $consulta;
        }
        
        /**
         * Add new customer
         * @param int $id_customer
         * @param varchar $code_customer
         * @param int $id_tenant
         * @param varchar $label_customer
         * @return pdo
         */
        public function addNewManagement($id_management, $code_management, $id_tenant, $label_management)
	{            
            $this->db->exec("set names utf8");
            
            $consulta = $this->db->prepare("
                    INSERT INTO cas_management
                            (id_management
                            , code_management
                            , id_tenant
                            , label_management) 
                    VALUES 
                            (NULL
                            ,'$code_management'
                            ,$id_tenant
                            ,'$label_management'
                            )"
                    
                    , array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

            $consulta->execute();

            return $consulta;
	}
        
        /**
         * Add new customer
         * @param int $id_customer
         * @param varchar $code_customer
         * @param int $id_tenant
         * @param varchar $label_customer
         * @return pdo
         */
        public function addNewManagementWithCustomer($id_management, $code_management, $id_tenant, $label_management, $id_customer, $created_at, $id_user)
	{            
            $this->db->exec("set names utf8");
            
            $consulta = $this->db->prepare("
                    INSERT INTO cas_management
                            (id_management
                            , code_management
                            , id_tenant
                            , label_management
                            , id_customer
                            , created_at
                            , id_user
                            , status_management) 
                    VALUES 
                            (NULL
                            ,'$code_management'
                            ,$id_tenant
                            ,'$label_management'
                            ,$id_customer
                            ,'$created_at'
                            ,$id_user
                            , 1)"
                    
                    , array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

            $consulta->execute();

            return $consulta;
	}
        
        /**
         * 
         * @param type $id_tenant
         * @param type $id_customer
         * @param type $id_management
         * @param type $id_user
         * @return type MariaDB String
         */
        public function addManagementCustomerPair($id_tenant, $id_customer, $id_management, $id_user) {
            $this->db->exec("set names utf8");
            
            $consulta = $this->db->prepare("
                    INSERT INTO cas_customer_management
                            (id_customer_management
                            , id_tenant
                            , id_customer
                            , id_management
                            , created_at
                            , updated_at
                            , user_id) 
                    VALUES 
                            (NULL
                            ,$id_tenant
                            ,$id_customer
                            ,$id_management
                            ,NOW()
                            ,NULL
                            ,$id_user)"
                    
                    , array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

            $consulta->execute();

            return $consulta;
        }
        
                
        /**
         * Get last management (by tenant)
         * @param int $id_tenant
         * @return pdo 
         */
        public function getLastManagement($id_tenant)
	{
            $consulta = $this->db->prepare("
                        SELECT id_management
                            , code_management
                            , id_tenant
                            , label_management
                        FROM cas_management a
                        WHERE id_tenant = $id_tenant
                        ORDER BY id_management DESC
                        LIMIT 1");

            $consulta->execute();

            return $consulta;
	}

        
        
        
        /**
         * Add new customer
         * @param int $id_customer
         * @param varchar $code_customer
         * @param int $id_tenant
         * @param varchar $label_customer
         * @return pdo
         */
        public function updateFieldManagement($id_management, $code_management, $id_tenant, $label_management)
	{            
            $this->db->exec("set names utf8");
            
            $consulta = $this->db->prepare("
                    INSERT INTO cas_management
                            (id_management
                            , code_management
                            , id_tenant
                            , label_management) 
                    VALUES 
                            (NULL
                            ,'$code_management'
                            ,$id_tenant
                            ,'$label_management')"
                    , array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

            $consulta->execute();

            return $consulta;
	}
        
        /**
         * Update management
         * @param int $id_customer
         * @param varchar $code_customer
         * @param int $id_tenant
         * @param varchar $label_customer
         * @param varchar $detail_customer
         * @return pdo
         */
        public function updateManagement($id_management, $code_management, $id_tenant, $label_management, $updated_at, $id_user)
	{            
            $this->db->exec("set names utf8");
            
            $consulta = $this->db->prepare("UPDATE cas_management 
                        SET 
                            label_management = '$label_management'
                            , updated_at = '$updated_at'
                            , id_user = $id_user
                        WHERE id_tenant = $id_tenant
                          AND id_management = $id_management");

            $consulta->execute();

            return $consulta;
	}
        
        public function updateManagementDinamic($id_management, $column, $value, $updated_at, $id_user)
    {
         $this->db->exec("set names utf8");

        $consulta = $this->db->prepare("UPDATE cas_management 
                        SET 
                            $column = '$value'
                            , updated_at = '$updated_at'
                            , id_user = $id_user
                        WHERE id_management = '$id_management'");

        $consulta->execute();

        return $consulta;
    }
        
        /**
         * Cambia el estado de una gestion (management)
         * @param management $id_management
         * @param management $id_tenant
         * @param management $status_management
         * @return PDO
         */
        public function updateStatusManagement($id_management, $id_tenant, $status_management)
	{            
            $this->db->exec("set names utf8");
            
            $consulta = $this->db->prepare("UPDATE cas_management 
                        SET 
                            status_management = $status_management
                        WHERE id_tenant = $id_tenant
                          AND id_management = $id_management");

            $consulta->execute();

            return $consulta;
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

            return $consulta;
        }
        
        /**
         * Get database table name linked to this model
         * NOTA: Solo por lógica modelo = tabla
         * @return string 
         */
        public function getTableName()
        {
            $tableName = "cas_management";
            
            return $tableName;
        }
        
}
?>