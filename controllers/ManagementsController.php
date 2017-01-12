<?php
class ManagementsController extends ControllerBase
{
    /*******************************************************************************
    * Managements
    *******************************************************************************/

    //DT
    public function managementsDt($error_flag = 0, $message = "")
    {
        $session = FR_Session::singleton();

        #support global messages
        if(isset($_GET['error_flag']))
            $error_flag = $_GET['error_flag'];
        if(isset($_GET['message']))
            $message = $_GET['message'];

        //Incluye el modelo que corresponde
        require_once 'models/ManagementsModel.php';
        require_once 'models/CustomersModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new ManagementsModel();

        //Creamos una instancia del modelo de los clientes
        $clientModel = new CustomersModel();

        //Cargar listado de clientes
        $listadoClientes = $clientModel->getAllCustomers($session->id_tenant);

        //Le pedimos al modelo todos los items
        $listado = $model->getAllManagementsByTenant($session->id_tenant);

        //Pasamos a la vista toda la información que se desea representar
        $data['listado'] = $listado;
        $data['listadoClientes'] = $listadoClientes;

        //Titulo pagina
        $data['titulo'] = "Gestiones";

        //Controller
        $data['controller'] = "managements";
        $data['action'] = "managementsEditForm";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag,$message);

        //Finalmente presentamos nuestra plantilla
        $this->view->show("managements_dt.php", $data);

    }

    /**
    * Get customers for ajax dynamic query
    * AJAX
    * @return json
    */
    public function ajaxManagementsDt()
    {
        $session = FR_Session::singleton();

        //Incluye el modelo que corresponde
        require_once 'models/ManagementsModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new ManagementsModel();

        $status_column = "status_management";

        /*
        * Build up dynamic query
        */
        $sTable = $model->getTableName();

        $aColumns = array('a.id_management'
                    , 'a.code_management'
                    , 'b.id_tenant'
                    , 'a.label_management'
                    , 'a.id_customer'
                    , 'c.label_customer'
            );

        $sIndexColumn = "id_management";

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

        $sWhere .= " a.status_management < 9 "; # avoid deleted tasks (status = 9)

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
    public function managementsAddForm($error_flag = 0)
    {
        //Import models
        require_once 'models/ManagementsModel.php';

        $data['titulo'] = "Nueva Materia";

        $this->view->show("managements_new.php", $data);
    }



    public function managementsAdd()
    {
        $session = FR_Session::singleton();

        $label_management = $_POST["label_management"];
        $id_customer = $_POST["id_customer"];
        //$label_management = filter_input(INPUT_POST, "label_management");
        //$label_management = "Ejemplo";
        $code_management = Utils::guidv4();
        echo "Label: ".$label_management. " - Customer: ".$id_customer;
        exit();

        //Incluye el modelo que corresponde
        require_once 'models/managementsModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new ManagementsModel();


        //Le pedimos al modelo todos los items
        $result = $model->addNewManagement(null, $code_management, $session->id_tenant, $label_management, $id_customer);

        $error = $result->errorInfo();
        $rows_n = $result->rowCount();

        return "hola ".$label_managementº;
        /*
        if($error[0] == 00000 && $rows_n > 0){
            header("Location: ".$this->root."?controller=managements&action=managementsDt&error_flag=1");
        }
        elseif($error[0] == 00000 && $rows_n < 1){
            header("Location: ".$this->root."?controller=managements&action=managementsDt&error_flag=10&message='Ha ocurrido un error grave'");
        }
        else{
            header("Location: ".$this->root."?controller=managements&action=managementsDt&error_flag=10&message='Ha ocurrido un error: ".$error[2]."'");
        }

         */
    }

    public function ajaxManagementsAdd()
    {
        $session = FR_Session::singleton();

        if(isset($_POST['label_management']) && $_POST['label_management'] != ""):
            $label_management = $_POST['label_management'];
            //Incluye el modelo que corresponde
            require_once 'models/ManagementsModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new ManagementsModel();

            $code_management = Utils::guidv4();
            $new_management[] = null;

            //Le pedimos al modelo todos los items
            $resultPdo = $model->addNewManagement(null, $code_management, $session->id_tenant, $label_management);

            $error = $resultPdo->errorInfo();
            $rows_n = $resultPdo->rowCount();

            if($error[0] == 00000 && $rows_n > 0){
                $result = $model->getLastManagement($session->id_tenant);
                $values = $result->fetch(PDO::FETCH_ASSOC);

                $id_management = $values['id_management'];

                $new_management[0] = $id_management;
                $new_management[1] = $label_management;
            }
            elseif($error[0] == 00000 && $rows_n < 1){
                $new_management[0] = "0";
                $new_management[1] = "No se ha podido ingresar el registro";
            }
            else{
                $new_management[0] = "0";
                $new_management[1] = $error[2];
            }

            print json_encode($new_management);

            return true;
        else:
            return false;
        endif;
    }

    public function ajaxManagementsAddWithCustomer()
    {
        $session = FR_Session::singleton();

        if(isset($_POST['label_management']) && $_POST['label_management'] != ""):
            $label_management = $_POST['label_management'];
            $id_customer = $_POST["id_customer"];
            //Incluye el modelo que corresponde
            require_once 'models/ManagementsModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new ManagementsModel();

            $code_management = Utils::guidv4();
            $new_management[] = null;

            #fecha actual
            $currentDateTime = date('Y/m/d H:i:s');

            $id_user = $session->id_user;

            //Le pedimos al modelo todos los items
            $resultPdo = $model->addNewManagementWithCustomer(null, $code_management, $session->id_tenant, $label_management, $id_customer, $currentDateTime, $id_user);

            $error = $resultPdo->errorInfo();
            $rows_n = $resultPdo->rowCount();

            if($error[0] == 00000 && $rows_n > 0){
                $result = $model->getLastManagement($session->id_tenant);
                $values = $result->fetch(PDO::FETCH_ASSOC);

                $id_management = $values['id_management'];

                $new_management[0] = $id_management;
                $new_management[1] = $label_management;
            }
            elseif($error[0] == 00000 && $rows_n < 1){
                $new_management[0] = "0";
                $new_management[1] = "No se ha podido ingresar el registro";
            }
            else{
                $new_management[0] = "0";
                $new_management[1] = $error[2];
            }

            print json_encode($new_management);

            return true;
        else:
            return false;
        endif;
    }

    public function ajaxManagementsUpdate()
    {
        $session = FR_Session::singleton();

        if(isset($_POST['row_id']) && $_POST['row_id'] != ""):

            //Incluye el modelo que corresponde
            require_once 'models/ManagementsModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new ManagementsModel();
            $targetManagementPdo = $model->getManagementByID($session->id_tenant, filter_input(INPUT_POST, 'row_id'));

            $new_value = filter_input(INPUT_POST, 'value');
            #fecha actual
            $currentDateTime = date('Y/m/d H:i:s');
            $id_user = $session->id_user;

            $targetManagement = $targetManagementPdo->fetch(PDO::FETCH_ASSOC);
            if($targetManagement != null && $targetManagement != false){
                //apply change
                $result = $model->updateManagement(
                        $targetManagement['id_management']
                        , $targetManagement['code_management']
                        , $targetManagement['id_tenant']
                        , $new_value
                        , $currentDateTime
                        , $id_user);

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
     * Remove a management
     */
    public function managementsRemove()
    {
        $session = FR_Session::singleton();
        $id_management = null;

        // Support POST & GET
        if(filter_input(INPUT_POST, 'management_id') != ''){
            $id_management = filter_input(INPUT_POST, 'management_id');
        }
        else{
            $id_management = filter_input(INPUT_GET, 'management_id');
        }

        if($id_management != null){
            require_once 'models/ManagementsModel.php';
            $model = new ManagementsModel();

            $status = 9; // 9 removed status

            // remove
            $result = $model->updateStatusManagement($id_management, $session->id_tenant, $status);

            if($result != null){
                $error = $result->errorInfo();
                $numr = $result->rowCount();

                if($error[0] == 00000 && $numr > 0){
                    header("Location: ".$this->root."?controller=managements&action=managementsDt&error_flag=1'");
                }
                else{
                    header("Location: ".$this->root."?controller=managements&action=managementsDt&error_flag=10&message='No se lograron aplicar cambios: ".$error[2]."'");
                }
            }
            else{
                header("Location: ".$this->root."?controller=managements&action=managementsDt&error_flag=10&message='Error: no se ha podido eliminar!");
            }
        }
        else{
            header("Location: ".$this->root."?controller=managements&action=managementsDt&error_flag=10&message='Error: esta materia ya no existe!");
        }
    }

    public function ajaxGetManagementsByCustomer() {
        $session = FR_Session::singleton();
        require_once 'models/ManagementsModel.php';
        $model = new ManagementsModel();

        $id_customer = $_POST["id_customer"];
        $id_type = $_POST["id_type"];

        //Le pedimos al modelo todos los items
        $customerManagements = $model->getManagementsByCustomerType($session->id_tenant, $id_customer, $id_type);
        $allManagements = $model->getManagementsByCustomer($session->id_tenant, $id_customer);

        $trabajos = $customerManagements->fetchAll(PDO::FETCH_ASSOC);
        $trabajosTodos = $allManagements->fetchAll(PDO::FETCH_ASSOC);
        $trabajosFiltrados = array_diff_assoc($trabajosTodos, $trabajos);   // solo las restantes

        //Gestiones del cliente
        if(sizeof($trabajos) > 0){
          $respuesta .= "<optgroup label='Gestiones de Materia'>";
          foreach ($trabajos as $key => $value)
          {
              $respuesta .= "<option value='".$value['id_management']."'>".$value['label_management']."</option>";
          }
          $respuesta .= "</optgroup>";
        }

        //Gestionas otras
        if(sizeof($trabajosFiltrados) > 0){
          $respuesta .= "<optgroup label='Otras Gestiones del Cliente'>";
          foreach ($trabajosFiltrados as $key => $value)
          {
              $respuesta .= "<option value='".$value['id_management']."'>".$value['label_management']."</option>";
          }
          $respuesta .= "</optgroup>";
        }

        echo $respuesta;
    }

    public function ajaxGetManagements() {
        $session = FR_Session::singleton();
        require_once 'models/ManagementsModel.php';
        $model = new ManagementsModel();

        //Le pedimos al modelo todos los items
        $allManagements = $model->getManagements($session->id_tenant);


        $respuesta = "<option value=''>Seleccione Gestión</option>";
         //Todas las gestiones
        $respuesta .= "<optgroup label='Todas las Gestiones'>";
        while($todasMateria = $allManagements->fetch(PDO::FETCH_ASSOC))
        {
            $respuesta .= "<option value='".$todasMateria['id_management']."'>".$todasMateria['label_management']."</option>";
        }
        $respuesta .= "</optgroup>";
        /*
        foreach($listado as $materia) {
            $respuesta .= "<option value='".$materia['id_management']."'>".$materia['label_management']."</option>";
        }
        */
        /*
        print_r($respuesta);
        exit();
         */

        echo $respuesta;
    }

    public function getManagementsByCustomer($id_customer)
    {
        $session = FR_Session::singleton();

        require_once 'models/ManagementsModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new ManagementsModel();

        //Le pedimos al modelo todos los items
        $listado = $model->getManagementsByCustomer($session->id_tenant, $id_customer);

        return $listado;
    }

    public function ajaxUpdateManagement()
    {
        $session = FR_Session::singleton();

        //Incluye el modelo que corresponde
        require_once 'models/ManagementsModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new ManagementsModel();

        //Ajax requested vars
        $idManagement = $_REQUEST['idManagement'];
        $column = $_REQUEST['column'];
        $newValue = $_REQUEST['value'];

        #fecha actual
        $currentDateTime = date('Y/m/d H:i:s');
        $id_user = $session->id_user;

        $target_column = "";
        if($column == 1)
            $target_column = "cod_management";
        else if($column == 2)
            $target_column = "label_management";
        else if($column == 3)
            $target_column = "id_customer";

        $result = $model->updateManagementDinamic($idManagement, $target_column, $newValue, $currentDateTime, $id_user);

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
