<?php
class TypesController extends ControllerBase
{
    /*******************************************************************************
    * TYPES
    *******************************************************************************/

    //DT
    public function typesDt($error_flag = 0, $message = "")
    { 
        $session = FR_Session::singleton();
        
        #support global messages
        if(isset($_GET['error_flag']))
            $error_flag = $_GET['error_flag'];
        if(isset($_GET['message']))
            $message = $_GET['message'];
        
        //Incluye el modelo que corresponde
        require_once 'models/TypesModel.php';
        require_once 'models/CustomersModel.php';
        
        //Creamos una instancia de nuestro "modelo"
        $model = new TypesModel();
        
        //Creamos una instancia del modelo de los clientes
        $clientModel = new CustomersModel();
        
        //Cargar listado de clientes
        $listadoClientes = $clientModel->getAllCustomers($session->id_tenant);
        
        //Le pedimos al modelo todos los items
        $listado = $model->getAllTypesByTenant($session->id_tenant);

        //Pasamos a la vista toda la información que se desea representar
        $data['listado'] = $listado;
        $data['listadoClientes'] = $listadoClientes;
        
        //Titulo pagina
        $data['titulo'] = "Materias";

        //Controller
        $data['controller'] = "types";
        $data['action'] = "typesEditForm";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag,$message);

        //Finalmente presentamos nuestra plantilla
        $this->view->show("types_dt.php", $data);
        
    }
    
    /**
    * Get customers for ajax dynamic query
    * AJAX
    * @return json
    */
    public function ajaxTypesDt()
    {
        $session = FR_Session::singleton();
        
        //Incluye el modelo que corresponde
        require_once 'models/TypesModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new TypesModel();
            
        $status_column = "status_type";
        
        /*
        * Build up dynamic query
        */
        $sTable = $model->getTableName();

        $aColumns = array('a.id_type'
                    , 'a.code_type'
                    , 'b.id_tenant'
                    , 'a.label_type'
                    , 'a.id_customer'
                    , 'c.label_customer'
            );
        
        $sIndexColumn = "id_type";

        /******************** Paging */
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
            $sLimit = "LIMIT ".$_GET['iDisplayStart'].", ".$_GET['iDisplayLength'];

        /******************** Ordering */
        $sOrder = "";
        if ( isset( $_GET['iSortCol_0'] ) )
        {
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
            {
                    if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                    {
                            $sOrder .= "".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
                                     $_GET['sSortDir_'.$i] .", ";
                    }
            }

            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" )
            {
                    $sOrder = "";
            }
        }

        /******************** Filtering */
        $sWhere = "";

        if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
        {
            $sWhere = "WHERE (";
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                $sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
            }

            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
        }
        if ( $sWhere == "" )
        {
                $sWhere = "WHERE ";
        }
        else
        {
                $sWhere .= " AND ";
        }

        $sWhere .= " a.status_type < 9 "; # avoid deleted tasks (status = 9)

        /********************* Individual column filtering */
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
            {
                if ( $sWhere == "" )
                {
                    $sWhere = "WHERE ";
                }
                else
                {
                    $sWhere .= " AND ";
                }

                $sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
            }
        }

        /********************** Create Query */
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS 
                ".str_replace(" , ", " ", implode(", ", $aColumns))."
            FROM $sTable a
            INNER JOIN cas_tenant b
            ON (a.id_tenant = b.id_tenant
                AND 
                b.id_tenant = $session->id_tenant)
            LEFT JOIN cas_customer c ON (a.id_customer = c.id_customer)
            $sWhere
            $sOrder
            $sLimit";

        $result_data = $model->goCustomQuery($sql);

        $found_rows = $model->goCustomQuery("SELECT FOUND_ROWS()");

        $total_rows = $model->goCustomQuery("SELECT COUNT(`".$sIndexColumn."`) FROM $sTable where $status_column < 9");

        /*
        * Output
        */
        $iTotal = $total_rows->fetch(PDO::FETCH_NUM);
        $iTotal = $iTotal[0];

        $iFilteredTotal = $found_rows->fetch(PDO::FETCH_NUM);
        $iFilteredTotal = $iFilteredTotal[0];

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        $k = 1;
        while($aRow = $result_data->fetch(PDO::FETCH_NUM))
        {
            $row = array();

            for ($i=0;$i<count($aColumns);$i++)
            {
//                $row[] = utf8_encode($aRow[ $i ]);
                $row[] = $aRow[$i];
                $row['DT_RowId'] = $aRow[0];
            }

            $output['aaData'][] = $row;

            $k++;
        }

        #echo $sql;
        echo json_encode( $output );
    }
    
    //NEW
    public function typesAddForm($error_flag = 0)
    {
        //Import models
        require_once 'models/TypesModel.php';

        $data['titulo'] = "Nueva Materia";

        $this->view->show("types_new.php", $data);
    }
    
    
    
    public function typesAdd()
    {
        $session = FR_Session::singleton();
        
        $label_type = $_POST["label_type"];
        $id_customer = $_POST["id_customer"];
        //$label_type = filter_input(INPUT_POST, "label_type");
        //$label_type = "Ejemplo";
        $code_type = Utils::guidv4();
        echo "Label: ".$label_type. " - Customer: ".$id_customer;
        exit();
        
        //Incluye el modelo que corresponde
        require_once 'models/TypesModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new TypesModel();
        
        
        //Le pedimos al modelo todos los items
        $result = $model->addNewType(null, $code_type, $session->id_tenant, $label_type, $id_customer);

        $error = $result->errorInfo();
        $rows_n = $result->rowCount();
        
        return "hola ".$label_type;
        /*
        if($error[0] == 00000 && $rows_n > 0){
            header("Location: ".$this->root."?controller=types&action=typesDt&error_flag=1");
        }
        elseif($error[0] == 00000 && $rows_n < 1){
            header("Location: ".$this->root."?controller=types&action=typesDt&error_flag=10&message='Ha ocurrido un error grave'");
        }
        else{
            header("Location: ".$this->root."?controller=types&action=typesDt&error_flag=10&message='Ha ocurrido un error: ".$error[2]."'");
        }
         
         */
    }
    
    public function ajaxTypesAdd()
    {   
        $session = FR_Session::singleton();

        if(isset($_POST['label_type']) && $_POST['label_type'] != ""):
            $label_type = $_POST['label_type'];
            //Incluye el modelo que corresponde
            require_once 'models/TypesModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new TypesModel();

            $code_type = Utils::guidv4();
            $new_type[] = null;

            //Le pedimos al modelo todos los items
            $resultPdo = $model->addNewType(null, $code_type, $session->id_tenant, $label_type);

            $error = $resultPdo->errorInfo();
            $rows_n = $resultPdo->rowCount();

            if($error[0] == 00000 && $rows_n > 0){
                $result = $model->getLastType($session->id_tenant);
                $values = $result->fetch(PDO::FETCH_ASSOC);

                $id_type = $values['id_type'];

                $new_type[0] = $id_type;
                $new_type[1] = $label_type;
            }
            elseif($error[0] == 00000 && $rows_n < 1){
                $new_type[0] = "0";
                $new_type[1] = "No se ha podido ingresar el registro";
            }
            else{
                $new_type[0] = "0";
                $new_type[1] = $error[2];
            }

            print json_encode($new_type);

            return true;
        else:
            return false;
        endif;
    }
    
    public function ajaxTypesAddWithCustomer()
    {   
        $session = FR_Session::singleton();

        if(isset($_POST['label_type']) && $_POST['label_type'] != ""):
            $label_type = $_POST['label_type'];
            $id_customer = $_POST["id_customer"];
            //Incluye el modelo que corresponde
            require_once 'models/TypesModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new TypesModel();

            $code_type = Utils::guidv4();
            $new_type[] = null;

            //Le pedimos al modelo todos los items
            $resultPdo = $model->addNewTypeWithCustomer(null, $code_type, $session->id_tenant, $label_type, $id_customer);

            $error = $resultPdo->errorInfo();
            $rows_n = $resultPdo->rowCount();

            if($error[0] == 00000 && $rows_n > 0){
                $result = $model->getLastType($session->id_tenant);
                $values = $result->fetch(PDO::FETCH_ASSOC);

                $id_type = $values['id_type'];

                $new_type[0] = $id_type;
                $new_type[1] = $label_type;
            }
            elseif($error[0] == 00000 && $rows_n < 1){
                $new_type[0] = "0";
                $new_type[1] = "No se ha podido ingresar el registro";
            }
            else{
                $new_type[0] = "0";
                $new_type[1] = $error[2];
            }

            print json_encode($new_type);

            return true;
        else:
            return false;
        endif;
    }
    
    public function ajaxTypesUpdate()
    {
        $session = FR_Session::singleton();

        if(isset($_POST['row_id']) && $_POST['row_id'] != ""):

            //Incluye el modelo que corresponde
            require_once 'models/TypesModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new TypesModel();
            $targetTypePdo = $model->getTypeByID($session->id_tenant, filter_input(INPUT_POST, 'row_id'));
            
            $new_value = filter_input(INPUT_POST, 'value');
            
            $targetType = $targetTypePdo->fetch(PDO::FETCH_ASSOC);
            if($targetType != null && $targetType != false){
                //apply change
                $result = $model->updateType(
                        $targetType['id_type']
                        , $targetType['code_type']
                        , $targetType['id_tenant']
                        , $new_value);
                
                if($result){
                    $error = $result->errorInfo();
                    $rows_n = $result->rowCount();
                    #$query = $result->debugDumpParams();

                    if($error[0] == 00000 && $rows_n > 0){
                        print 'actualizacion correcta';
                    }
                    elseif($error[0] == 00000 && $rows_n < 1){
                        print 'no se actualizaron datos';
                    }
                    else{
                        print 'error en actualizacion: '. $error[2];
                    }
                }
                else{
                    print 'hubo un error al intentar ejecutar la sentencia';
                }
            }
            else{
                print 'no se encontro elemento a actualizar';
            }
            
            return true;
        else:
            return false;
        endif;
    }

    /*
     * Remove a type
     */
    public function typesRemove()
    {
        $session = FR_Session::singleton();
        $id_type = null;
        
        // Support POST & GET
        if(filter_input(INPUT_POST, 'type_id') != ''){
            $id_type = filter_input(INPUT_POST, 'type_id');
        }
        else{
            $id_type = filter_input(INPUT_GET, 'type_id');
        }
        
        if($id_type != null){
            require_once 'models/TypesModel.php';
            $model = new TypesModel();
            
            $status = 9; // 9 removed status

            // remove
            $result = $model->updateStatusType($id_type, $session->id_tenant, $status);

            if($result != null){
                $error = $result->errorInfo();
                $numr = $result->rowCount();

                if($error[0] == 00000 && $numr > 0){
                    header("Location: ".$this->root."?controller=types&action=typesDt&error_flag=1'");
                }
                else{
                    header("Location: ".$this->root."?controller=types&action=typesDt&error_flag=10&message='No se lograron aplicar cambios: ".$error[2]."'");
                }
            }
            else{
                header("Location: ".$this->root."?controller=types&action=typesDt&error_flag=10&message='Error: no se ha podido eliminar!");
            }
        }
        else{
            header("Location: ".$this->root."?controller=types&action=typesDt&error_flag=10&message='Error: esta materia ya no existe!");
        }
    }   
    
    public function ajaxGetTypesByCustomer() {
        $session = FR_Session::singleton();
        require_once 'models/TypesModel.php';
        $model = new TypesModel();
        
        $id_customer = $_POST["id_customer"];
        
        //Le pedimos al modelo todos los items
        $listadoPDO = $model->getTypesByCustomer($session->id_tenant, $id_customer);
        
        $respuesta = "<option value=''>Seleccione Materia</option>";
        while($materia = $listadoPDO->fetch(PDO::FETCH_ASSOC))
        {
            $respuesta .= "<option value='".$materia['id_type']."'>".$materia['label_type']."</option>";
        }
        /*
        foreach($listado as $materia) {
            $respuesta .= "<option value='".$materia['id_type']."'>".$materia['label_type']."</option>";
        }
        */
        echo $respuesta;
    }
    
    public function getTypesByCustomer($id_customer)
    { 
        $session = FR_Session::singleton();
        
        require_once 'models/TypesModel.php';
        
        //Creamos una instancia de nuestro "modelo"
        $model = new TypesModel();
        
        //Le pedimos al modelo todos los items
        $listado = $model->getTypesByCustomer($session->id_tenant, $id_customer);

        return $listado;
    }
    
    public function ajaxUpdateType()
    {
        $session = FR_Session::singleton();

        //Incluye el modelo que corresponde
        require_once 'models/TypesModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new TypesModel();
        
        //Ajax requested vars
        $idType = $_REQUEST['idtype'];
        $column = $_REQUEST['column'];
        $newValue = $_REQUEST['value'];
        
        $target_column = ""; 
        if($column == 1)
            $target_column = "cod_type";
        else if($column == 2)
            $target_column = "label_type";
        else if($column == 3)
            $target_column = "id_customer";
        
        $result = $model->updateTypeDinamic($idType, $target_column, $newValue);
        
        if($result){
            $error = $result->errorInfo();
            $rows_n = $result->rowCount();
            #$query = $result->debugDumpParams();

            if($error[0] == 00000 && $rows_n > 0){
                print 'actualizacion correcta';
            }
            elseif($error[0] == 00000 && $rows_n < 1){
                print 'no se actualizaron datos';
            }
            else{
                print 'error en actualizacion: '. $error[2];
            }
        }
        else{
            print 'hubo un error al intentar ejecutar la sentencia';
        }
    }
}