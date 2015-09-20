<?php
class CustomersModel extends ModelBase
{
	/*******************************************************************************
	* CUSTOMERS
	*******************************************************************************/

	/**
         * Get all customers by tenant
         * @param int $id_tenant
         * @return pdo
         */
	public function getAllCustomers($id_tenant)
	{
                $consulta = $this->db->prepare("
                        select 
                            a.id_customer 
                            , a.code_customer
                            , b.id_tenant
                            , a.label_customer
                            , a.detail_customer
                        from cas_customer a
                        inner join cas_tenant b
                        on a.id_tenant = b.id_tenant
                        where b.id_tenant = $id_tenant
                        order by a.label_customer asc");

		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}

        /**
         * Get all customers by tenant + user
         * @param int $id_tenant
         * @param int $id_project
         * @return pdo
         */
	public function getAllCustomersByProject($id_tenant, $id_project)
	{
                $consulta = $this->db->prepare("
                        SELECT b.* 
                        FROM cas_project_has_cas_customer a
                        INNER JOIN cas_customer b ON a.cas_customer_id_customer = b.id_customer
                        inner join cas_tenant b
                        on a.id_tenant = b.id_tenant
                        where b.id_tenant = $id_tenant
                          and a.cas_project_id_project = $id_project");

		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}
        
        /**
         * Get cliente por código de cliente
         * @param int $id_tenant
         * @param varchar $code_customer
         * @return PDO 
         */
        public function getCustomerByCode($id_tenant, $code_customer)
        {
            $consulta = $this->db->prepare("
				SELECT id_customer 
                                    , code_customer
                                    , id_tenant
                                    , label_customer
                                    , detail_customer
                                FROM t_cliente a
                                WHERE code_customer = '$code_customer'
                                  and id_tenant = $id_tenant");
            
            $consulta->execute();

            return $consulta;
        }
        
        /**
         * Get cliente por ID de cliente
         * @param int $id_tenant
         * @param varchar $id_customer
         * @return PDO 
         */
        public function getCustomerByID($id_tenant, $id_customer)
        {
            $consulta = $this->db->prepare("
				SELECT id_customer 
                                    , code_customer
                                    , id_tenant
                                    , label_customer
                                    , detail_customer
                                FROM cas_customer a
                                WHERE id_customer = '$id_customer'
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
        public function addNewCustomer($id_customer, $code_customer, $id_tenant, $label_customer, $detail_customer = null)
	{            
            $this->db->exec("set names utf8");
            
            $consulta = $this->db->prepare("
                    INSERT INTO cas_customer 
                            (id_customer
                            , code_customer
                            , id_tenant
                            , label_customer
                            , detail_customer) 
                    VALUES 
                            (NULL
                            ,'$code_customer'
                            ,$id_tenant
                            ,'$label_customer'
                            ,'$detail_customer')"
                    , array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

            $consulta->execute();

            return $consulta;
	}
        
        /**
         * Get last customer (by tenant)
         * @param int $id_tenant
         * @return pdo 
         */
        public function getLastCustomer($id_tenant)
	{
            $consulta = $this->db->prepare("
                        SELECT id_customer 
                            , code_customer
                            , id_tenant
                            , label_customer
                            , detail_customer
                        FROM cas_customer a
                        WHERE id_tenant = $id_tenant
                        ORDER BY id_customer DESC
                        LIMIT 1");

            $consulta->execute();

            return $consulta;
	}
        
        /**
         * Update customer
         * @param int $id_customer
         * @param varchar $code_customer
         * @param int $id_tenant
         * @param varchar $label_customer
         * @param varchar $detail_customer
         * @return pdo
         */
        public function updateCustomer($id_customer, $code_customer, $id_tenant, $label_customer, $detail_customer)
	{            
            $this->db->exec("set names utf8");
            
            $consulta = $this->db->prepare("UPDATE cas_customer 
                        SET 
                            label_customer = '$label_customer'
                            , detail_customer = '$detail_customer'
                        WHERE id_tenant = $id_tenant
                          AND id_customer = $id_customer");

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
            $tableName = "cas_customer";
            
            return $tableName;
        }
}