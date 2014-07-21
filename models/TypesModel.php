<?php
class TypesModel extends ModelBase
{
	/*******************************************************************************
	* CUSTOMERS
	*******************************************************************************/

	/**
         * Get all types by tenant
         * @param int $id_tenant
         * @return pdo
         */
	public function getAllTypesByTenant($id_tenant)
	{
                $consulta = $this->db->prepare("
                        select 
                            a.id_type
                            , a.code_type
                            , a.label_type
                        from cas_type a
                        where a.id_tenant = $id_tenant
                        order by a.label_type asc");

		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
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
        public function addNewType($id_type, $code_type, $id_tenant, $label_type)
	{            
            $this->db->exec("set names utf8");
            
            $consulta = $this->db->prepare("
                    INSERT INTO cas_type
                            (id_type
                            , code_type
                            , id_tenant
                            , label_type) 
                    VALUES 
                            (NULL
                            ,'$code_type'
                            ,$id_tenant
                            ,'$label_type')"
                    , array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

            $consulta->execute();

            return $consulta;
	}
                
        /**
         * Get last type (by tenant)
         * @param int $id_tenant
         * @return pdo 
         */
        public function getLastType($id_tenant)
	{
            $consulta = $this->db->prepare("
                        SELECT id_type
                            , code_type
                            , id_tenant
                            , label_type
                        FROM cas_type a
                        WHERE id_tenant = $id_tenant
                        ORDER BY id_type DESC
                        LIMIT 1");

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
            $tableName = "cas_type";
            
            return $tableName;
        }
}
?>