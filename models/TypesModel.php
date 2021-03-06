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
                            , a.id_customer
                            , b.label_customer
                        from cas_type a
                        left join cas_customer b on (b.id_customer = a.id_customer)
                        where a.id_tenant = $id_tenant
                        and a.status_type < 9
                        order by a.label_type asc");

		$consulta->execute();

		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}

        /**
         * Get all types by tenant
         * @param int $id_tenant
         * @return pdo
         */
	public function getTypesByCustomer($id_tenant, $id_customer)
	{
                $consulta = $this->db->prepare("
                        select
                            a.id_type
                            , a.code_type
                            , a.label_type
                        from cas_type a
                        where a.id_tenant = $id_tenant
                        and a.id_customer = $id_customer
                        and a.status_type < 9
                        order by a.label_type asc");

		$consulta->execute();

		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}

        /**
         * Get typw por código de type
         * @param int $id_tenant
         * @param varchar $code_type
         * @return PDO
         */
        public function getTypeByCode($id_tenant, $code_type)
        {
            $consulta = $this->db->prepare("
				SELECT id_type
                                    , code_type
                                    , id_tenant
                                    , label_type
                                FROM cas_type
                                WHERE code_type = '$code_type'
                                  and id_tenant = $id_tenant");

            $consulta->execute();

            return $consulta;
        }

        /**
         * Get type por ID de type (materia)
         * @param int $id_tenant
         * @param varchar $id_type
         * @return PDO
         */
        public function getTypeByID($id_tenant, $id_type)
        {
            $consulta = $this->db->prepare("
				SELECT id_type
                                    , code_type
                                    , id_tenant
                                    , label_type
                                FROM cas_type a
                                WHERE id_type = '$id_type'
                                  and id_tenant = $id_tenant");

            $consulta->execute();

            return $consulta;
        }

				/**
         * Get type por ID de type (materia)
         * @param int $id_tenant
         * @param varchar $id_type
         * @return PDO
         */
        public function getTypeByLabel($label_type, $id_tenant)
        {
            $consulta = $this->db->prepare("
				SELECT id_type
                                    , code_type
                                    , id_tenant
                                    , label_type
                                FROM cas_type a
                                WHERE label_type = '$label_type'
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
                            ,'$label_type'
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
        public function addNewTypeWithCustomer($id_type, $code_type, $id_tenant, $label_type, $id_customer)
	{
            $this->db->exec("set names utf8");

            $consulta = $this->db->prepare("
                    INSERT INTO cas_type
                            (id_type
                            , code_type
                            , id_tenant
                            , label_type
                            , id_customer)
                    VALUES
                            (NULL
                            ,'$code_type'
                            ,$id_tenant
                            ,'$label_type'
                            ,$id_customer
                            )"

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
	 * Get last type (by tenant)
	 * @param int $id_tenant
	 * @return pdo
	 */
	public function getMyLastType($id_tenant, $id_user)
{
			$consulta = $this->db->prepare("
			SELECT
				a.id_type
			FROM
				zadmin_casmtdb.cas_task a
			INNER JOIN (select max(x.date_ini) AS date_ini from zadmin_casmtdb.cas_task x
									where x.id_user = $id_user) b
			ON a.date_ini = b.date_ini
			WHERE
				a.id_tenant = $id_tenant
			AND a.id_user = $id_user");

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
        public function updateFieldType($id_type, $code_type, $id_tenant, $label_type)
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
         * Update customer
         * @param int $id_customer
         * @param varchar $code_customer
         * @param int $id_tenant
         * @param varchar $label_customer
         * @param varchar $detail_customer
         * @return pdo
         */
        public function updateType($id_type, $code_type, $id_tenant, $label_type)
	{
            $this->db->exec("set names utf8");

            $consulta = $this->db->prepare("UPDATE cas_type
                        SET
                            label_type = '$label_type'
                        WHERE id_tenant = $id_tenant
                          AND id_type = $id_type");

            $consulta->execute();

            return $consulta;
	}

        public function updateTypeDinamic($id_types, $column, $value)
    {
         $this->db->exec("set names utf8");

        $consulta = $this->db->prepare("UPDATE cas_type
                        SET
                            $column = '$value'
                        WHERE id_type = '$id_types'");

        $consulta->execute();

        return $consulta;
    }

        /**
         * Cambia el estado de una materia (type)
         * @param type $id_type
         * @param type $id_tenant
         * @param type $status_type
         * @return PDO
         */
        public function updateStatusType($id_type, $id_tenant, $status_type)
	{
            $this->db->exec("set names utf8");

            $consulta = $this->db->prepare("UPDATE cas_type
                        SET
                            status_type = $status_type
                        WHERE id_tenant = $id_tenant
                          AND id_type = $id_type");

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
            $tableName = "cas_type";

            return $tableName;
        }

}
?>
