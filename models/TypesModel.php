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