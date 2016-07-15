<?php
class CustomersController extends ControllerBase
{
    /*******************************************************************************
    * CLIENTES
    *******************************************************************************/
        
    //DT
    public function customersDt($error_flag = 0, $message = "")
    {
        $session = FR_Session::singleton();
        
        #support global messages
        if(isset($_GET['error_flag']))
            $error_flag = $_GET['error_flag'];
        if(isset($_GET['message']))
            $message = $_GET['message'];
        
        //Incluye el modelo que corresponde
        require_once 'models/CustomersModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new CustomersModel();

        //Le pedimos al modelo todos los items
        $listado = $model->getAllCustomers($session->id_tenant);

        //Pasamos a la vista toda la información que se desea representar
        $data['listado'] = $listado;

        // Obtener permisos de edición
//        require_once 'models/UsersModel.php';
//        $userModel = new UsersModel();

//        $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 2);
//        if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
//            $data['permiso_editar'] = $row['EDITAR'];
//        }

        //Titulo pagina
        $data['titulo'] = "Clientes";

        //Controller
        $data['controller'] = "customers";
        $data['action'] = "customersEditForm";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag,$message);

        //Finalmente presentamos nuestra plantilla
        $this->view->show("customers_dt.php", $data);
    }

    /**
    * Get customers for ajax dynamic query
    * AJAX
    * @return json
    */
    public function ajaxCustomersDt()
    {
        $session = FR_Session::singleton();
        
        //Incluye el modelo que corresponde
        require_once 'models/CustomersModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new CustomersModel();

        /*
        * Build up dynamic query
        */
        $sTable = $model->getTableName();

        $aColumns = array('a.id_customer'
                    , 'a.code_customer'
                    , 'b.id_tenant'
                    , 'a.label_customer'
                    , 'a.detail_customer');
        
        $sIndexColumn = "id_customer";

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
                                    mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
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
            $sWhere
            $sOrder
            $sLimit";

        $result_data = $model->goCustomQuery($sql);

        $found_rows = $model->goCustomQuery("SELECT FOUND_ROWS()");

        $total_rows = $model->goCustomQuery("SELECT COUNT(`".$sIndexColumn."`) FROM $sTable");

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
    public function customersAddForm($error_flag = 0)
    {
        //Import models
        require_once 'models/CustomersModel.php';

        $data['titulo'] = "Nuevo Cliente";

        $this->view->show("customers_new.php", $data);
    }
    
    public function customersAdd()
    {
        $session = FR_Session::singleton();

        $label_customer = $_POST['customer_name'];
        $detail_customer = $_POST['customer_detail'];
        
        $code_customer = Utils::guidv4();
        
        //Incluye el modelo que corresponde
        require_once 'models/CustomersModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new CustomersModel();
        
        //Le pedimos al modelo todos los items
        $result = $model->addNewCustomer(null, $code_customer, $session->id_tenant, $label_customer, $detail_customer);

        $error = $result->errorInfo();
        $rows_n = $result->rowCount();
        
        if($error[0] == 00000 && $rows_n > 0){
            header("Location: ".$this->root."?controller=Customers&action=customersDt&error_flag=1");
        }
        elseif($error[0] == 00000 && $rows_n < 1){
            header("Location: ".$this->root."?controller=Customers&action=customersDt&error_flag=10&message='Ha ocurrido un error grave'");
        }
        else{
            header("Location: ".$this->root."?controller=Customers&action=customersDt&error_flag=10&message='Ha ocurrido un error: ".$error[2]."'");
        }
    }
    
    public function ajaxCustomersAdd()
    {
        $session = FR_Session::singleton();

        if(isset($_POST['name']) && $_POST['name'] != ""):
            $label_customer = $_POST['name'];
            $detail_customer = $_POST['desc'];
            #$code_customer = rand(1, 100);
            #$code_customer = "c".$code_customer;

            //Incluye el modelo que corresponde
            require_once 'models/CustomersModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new CustomersModel();

            $result = $model->getLastCustomer($session->id_tenant);
            $values = $result->fetch(PDO::FETCH_ASSOC);
            
            // UUID code
            $code_customer = Utils::guidv4();
            
            $new_customer[] = null;

            //Le pedimos al modelo todos los items
            $result = $model->addNewCustomer(null, $code_customer, $session->id_tenant, $label_customer, $detail_customer);

            $error = $result->errorInfo();
            $rows_n = $result->rowCount();

            if($error[0] == 00000 && $rows_n > 0){
                $result = $model->getLastCustomer($session->id_tenant);
                $values = $result->fetch(PDO::FETCH_ASSOC);

                $id_customer = $values['id_customer'];

                $new_customer[0] = $id_customer;
                $new_customer[1] = $label_customer;
            }
            elseif($error[0] == 00000 && $rows_n < 1){
                $new_customer[0] = "0";
                $new_customer[1] = "No se ha podido ingresar el registro";
            }
            else{
                $new_customer[0] = "0";
                $new_customer[1] = $error[2];
            }

            print json_encode($new_customer);

            return true;
        else:
            return false;
        endif;
    }
    
    public function ajaxCustomersUpdate()
    {
        $session = FR_Session::singleton();

        if(isset($_POST['row_id']) && $_POST['row_id'] != ""):

            //Incluye el modelo que corresponde
            require_once 'models/CustomersModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new CustomersModel();
            $targetCustomerPdo = $model->getCustomerByID($session->id_tenant, filter_input(INPUT_POST, 'row_id'));
            
            $new_value = filter_input(INPUT_POST, 'value');
            $column_updated = filter_input(INPUT_POST, 'column');
            
            $targetCustomer = $targetCustomerPdo->fetch(PDO::FETCH_ASSOC);
            if($targetCustomer != null && $targetCustomer != false){
                //apply change
                
                if($column_updated == 3)
                {
                    $result = $model->updateCustomer(
                        $targetCustomer['id_customer']
                        , $targetCustomer['code_customer']
                        , $targetCustomer['id_tenant']
                        , $new_value
                        , $targetCustomer['detail_customer']);
                }
                else if($column_updated == 4)
                {
                    $result = $model->updateCustomer(
                        $targetCustomer['id_customer']
                        , $targetCustomer['code_customer']
                        , $targetCustomer['id_tenant']
                        , $targetCustomer['label_customer']
                        , $new_value);
                }
                
                
                
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

    /**
     * show project info 
     */
    public function customersView()
    {
        $session = FR_Session::singleton();
        $paused_date = null;

        $id_customer = $_REQUEST['id_customer'];
        $session->id_customer = $id_customer;

        require_once 'models/CustomersModel.php';
        $model = new CustomersModel();

        $pdo = $model->getCustomerById($session->id_tenant, $id_customer);
        
        $values = $pdo->fetch(PDO::FETCH_ASSOC);
        if($values != null && $values != false){
            #data
            $data['id_customer'] = $values['id_customer'];
            $data['code_customer'] = $values['code_customer'];
            $data['id_tenant'] = $values['id_tenant'];
            $data['label_customer'] = $values['label_customer'];
            $data['detail_customer'] = $values['detail_customer'];
        }
        
        $data['action_type'] = $id_customer;
        $data['titulo'] = "Customer #";
        $data['pdo'] = $pdo;

        $this->view->show("customers_view.php", $data);
    }
    
    public function customersEdit()
    {
        $session = FR_Session::singleton();
        $paused_date = null;

        $id_customer = $_REQUEST['id_customer'];
        $session->id_customer = $id_customer;

        require_once 'models/CustomersModel.php';
        $model = new CustomersModel();

        $pdo = $model->getCustomerById($session->id_tenant, $id_customer);
        
        $values = $pdo->fetch(PDO::FETCH_ASSOC);
        if($values != null && $values != false){
            #data
            $data['id_customer'] = $values['id_customer'];
            $data['code_customer'] = $values['code_customer'];
            $data['id_tenant'] = $values['id_tenant'];
            $data['label_customer'] = $values['label_customer'];
            $data['detail_customer'] = $values['detail_customer'];
        }
        
        $data['action_type'] = 2;
        $data['titulo'] = "Editar Cliente";
        $data['pdo'] = $pdo;

        $this->view->show("customers_view.php", $data);
    }
    
    /**
     * Returns Customers list grouped by user
     * prints json array
     * @return boolean
     */
    public function ajaxCustomersList()
    {
        $session = FR_Session::singleton();

        if(isset($_POST['name']) && $_POST['name'] != ""):
            $label_customer = $_POST['name'];
            $detail_customer = $_POST['desc'];
            #$code_customer = rand(1, 100);
            #$code_customer = "c".$code_customer;

            //Incluye el modelo que corresponde
            require_once 'models/CustomersModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new CustomersModel();

            $result = $model->getLastCustomer($session->id_tenant);
            $values = $result->fetch(PDO::FETCH_ASSOC);
            
            // UUID code
            $code_customer = Utils::guidv4();
            
            $new_customer[] = null;

            //Le pedimos al modelo todos los items
            $result = $model->addNewCustomer(null, $code_customer, $session->id_tenant, $label_customer, $detail_customer);

            $error = $result->errorInfo();
            $rows_n = $result->rowCount();

            if($error[0] == 00000 && $rows_n > 0){
                $result = $model->getLastCustomer($session->id_tenant);
                $values = $result->fetch(PDO::FETCH_ASSOC);

                $id_customer = $values['id_customer'];

                $new_customer[0] = $id_customer;
                $new_customer[1] = $label_customer;
            }
            elseif($error[0] == 00000 && $rows_n < 1){
                $new_customer[0] = "0";
                $new_customer[1] = "No se ha podido ingresar el registro";
            }
            else{
                $new_customer[0] = "0";
                $new_customer[1] = $error[2];
            }

            print json_encode($new_customer);

            return true;
        else:
            return false;
        endif;
    }
    
    public function getCustomersByTenant() {
        
        $session = FR_Session::singleton();

        require_once 'models/CustomersModel.php';

        $modelCustomers = new CustomersModel();
        
        
        $pdo_listado = $modelCustomers->getAllCustomers($session->id_tenant);
       
        if($pdo_listado->rowCount() > 0){
           $listado = $pdo_listado->fetchAll(PDO::FETCH_ASSOC);
           $result = json_encode($listado);
           
           //print_r($result);
           //exit();
           
           echo $result;
        }
       
        else{
            return false;
            
        }
        //return $listado;
    }
    
    public function getCustomersByTenantJSON() {
        
        $session = FR_Session::singleton();

        require_once 'models/CustomersModel.php';

        $modelCustomers = new CustomersModel();
        
        
        $listado = $modelCustomers->getAllCustomers($session->id_tenant);
       
        $output = array();

        while ($row = $listado->fetch(PDO::FETCH_ASSOC))
        {
            $output[$row['id_customer']] = utf8_encode($row['label_customer']);
        }

        $output['selected'] = utf8_encode($_GET['current']);

        echo json_encode( $output );
    }
}