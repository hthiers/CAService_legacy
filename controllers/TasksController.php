<?php
class TasksController extends ControllerBase
{
    /***************************************************************************
    * PROJECTS
    ***************************************************************************/

    /**
     * Show tasks dt
     * @param type $error_flag
     * @param type $message 
     */
    public function tasksDt($error_flag = 0, $message = "")
    {
        $session = FR_Session::singleton();
        
        #support global messages
        if(isset($_GET['error_flag'])){
            $error_flag = $_GET['error_flag'];
        }
        if(isset($_GET['message'])){
            $message = $_GET['message'];
        }
        
        //Incluye el modelo que corresponde
//        require_once 'models/ProjectsModel.php';
//        require_once 'models/UsersModel.php';
        require_once 'models/TasksModel.php';
        $taskModel = new TasksModel();
        
        require_once 'models/CustomersModel.php';
        $customerModel = new CustomersModel();

        //Le pedimos al modelo todos los items
        $pdoTask = $taskModel->getAllTasksByTenant($session->id_tenant);
        $pdoCustomer = $customerModel->getAllCustomers($session->id_tenant);
        
        // meses
        $arrayDates = Utils::getMonths();
        $data['arrayDates'] = $arrayDates;
        
        //Pasamos a la vista toda la información que se desea representar
        $data['listado'] = $pdoTask;
        
        $clientes = array();
        
        while($aRow = $pdoCustomer->fetch(PDO::FETCH_NUM))
        {
            
            $clientes[] = $aRow;
        }
        
        $data['clientes'] = $clientes;

        //Materias (types)
        require_once 'models/TypesModel.php';
        $typesModel = new TypesModel();
        $pdoTypes = $typesModel->getAllTypesByTenant($session->id_tenant);
        $types = array();
        
        while($aRow = $pdoTypes->fetch(PDO::FETCH_NUM))
        {
            $types[] = $aRow;
        }
        
        $data['types'] = $types;
        
        //Responsables (users)
        require_once 'models/UsersModel.php';
        $usersModel = new UsersModel();
        $pdoUsers = $usersModel->getAllUserAccountByTenant($session->id_tenant);
        $users = array();
        
        while($aRow = $pdoUsers->fetch(PDO::FETCH_NUM))
        {
            $users[] = $aRow;
        }
        
        $data['users'] = $users;
        
        
        //Titulo pagina
        $data['titulo'] = "Lista de Trabajos";

        $data['controller'] = "tasks";
        $data['action'] = "tasksView";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag, $message);

        $this->view->show("tasks_dt.php", $data);
    }

    public function ajaxTasksDt()
    {
        $session = FR_Session::singleton();
        require_once 'models/TasksModel.php';
        $model = new TasksModel();

        /*
        * Building dynamic query
        */
        #$sTable = $model->getTableName();
        $sTable = "cas_task";

        $aColumns = array(
            'a.date_ini'
            , 'a.date_end'
            , 'c.label_customer'
            , 'g.label_type'
            , 'a.label_task'
            , 'e.name_user'
            , 'a.time_total'
            , 'a.id_task'
            , 'a.id_tenant'
            , 'b.id_project'
            , 'c.id_customer'
            , 'e.id_user'
            , 'f.cas_type_id_type');

        $sIndexColumn = "code_task";
        $aTotalColumns = count($aColumns);

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
                                        $_GET['sSortDir_'.$i].", ";
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
                $sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
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

                $sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
            }
        }

        /******************** Custom Filtering */
        if( isset($_GET['filCliente']) && $_GET['filCliente'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " c.id_customer = '".$_GET['filCliente']."' ";
        }
	if( isset($_GET['filAnio']) && $_GET['filAnio'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " YEAR(a.date_ini) = '".$_GET['filAnio']."' ";
        }    
	if( isset($_GET['filMes']) && $_GET['filMes'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " MONTH(a.date_ini) = '".$_GET['filMes']."' ";
        }
        if( isset($_GET['filDia']) && $_GET['filDia'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " DAY(a.date_ini) = '".$_GET['filDia']."' ";
        }
        if( isset($_GET['filType']) && $_GET['filType'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " f.cas_type_id_type = '".$_GET['filType']."' ";
        }
        if( isset($_GET['filEstado']) && $_GET['filEstado'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " a.status_task = '".$_GET['filEstado']."' ";
        }
        if ($_GET['filEstado'] == "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }
            
            $sWhere .= " a.status_task < 9 "; # avoid deleted tasks (status = 9)
        }
        if( isset($_GET['filUser']) && $_GET['filUser'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " e.id_user = '".$_GET['filUser']."' ";
        }
        
        # TENANT
        if ( $sWhere == "" )
        {
                $sWhere = "WHERE a.id_tenant = ".$session->id_tenant;
        }
        else
        {
                $sWhere .= " AND a.id_tenant = ".$session->id_tenant;
        }
        
        # PATCH
//        unset($aColumns[5]);    // replace column by group
//        $aColumns[5] = "IFNULL(a.time_total/3600, '') AS time_total";

        /********************** Create Query */
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS 
                ".str_replace(" , ", " ", implode(", ", $aColumns))."
            FROM $sTable a
            LEFT OUTER JOIN cas_project b
            ON (a.cas_project_id_project = b.id_project
                AND 
                a.id_tenant = b.id_tenant)
            LEFT OUTER JOIN cas_customer c
            ON (a.cas_customer_id_customer = c.id_customer
                AND 
                a.id_tenant = c.id_tenant)
            LEFT OUTER JOIN cas_task_has_cas_user d
            ON a.id_task = d.cas_task_id_task
            LEFT OUTER JOIN cas_user e
            ON d.cas_user_id_user = e.id_user
            LEFT OUTER JOIN cas_task_has_cas_type f
            ON a.id_task = f.cas_task_id_task
            LEFT OUTER JOIN cas_type g
            ON f.cas_type_id_type = g.id_type
            $sWhere
            $sOrder
            $sLimit";

        #print($sql);

        //Result needed data
        $result_data = $model->goCustomQuery($sql);
        
        $found_rows = $model->goCustomQuery("SELECT FOUND_ROWS()");
        $foundTotal = $found_rows->fetch(PDO::FETCH_NUM);
        $iFilteredTotal = $foundTotal[0];
        
        $total_rows = $model->goCustomQuery("SELECT COUNT(`".$sIndexColumn."`) FROM $sTable");

        //Found ids
        $sql_ids = "SELECT a.id_task as id_task FROM $sTable a
                    LEFT OUTER JOIN cas_project b
                    ON (a.cas_project_id_project = b.id_project
                        AND 
                        a.id_tenant = b.id_tenant)
                    LEFT OUTER JOIN cas_customer c
                    ON (a.cas_customer_id_customer = c.id_customer
                        AND 
                        a.id_tenant = c.id_tenant)
                    LEFT OUTER JOIN cas_task_has_cas_user d
                    ON a.id_task = d.cas_task_id_task
                    LEFT OUTER JOIN cas_user e
                    ON d.cas_user_id_user = e.id_user
                    LEFT OUTER JOIN cas_task_has_cas_type f
                    ON a.id_task = f.cas_task_id_task
                    LEFT OUTER JOIN cas_type g
                    ON f.cas_type_id_type = g.id_type
                    $sWhere
                    $sLimit";

        $idsPdo = $model->goCustomQuery($sql_ids);
        $ids_array = null;
        $ids_cols = "";
        
        #----------------- PROBANDO COLUMNAS QUE LLEGAN PARA SQL DE SUMATORIA TIEMPO!!!
        $realTotal = $idsPdo->rowCount();
        for($k = 0; $k<$realTotal; $k++){
            $ids_row = $idsPdo->fetch(PDO::FETCH_ASSOC);
            $ids_array[$k] = $ids_row['id_task'];
            $ids_cols = $ids_cols.$ids_row['id_task'];
            
            if($k < $realTotal-1){
                $ids_cols = $ids_cols.", ";
            }
        }

        //Sum found task times
        $sql_time = "SELECT SUM(a.time_total) FROM $sTable a
                    LEFT OUTER JOIN cas_project b
                    ON (a.cas_project_id_project = b.id_project
                        AND 
                        a.id_tenant = b.id_tenant)
                    LEFT OUTER JOIN cas_customer c
                    ON (a.cas_customer_id_customer = c.id_customer
                        AND 
                        a.id_tenant = c.id_tenant)
                    LEFT OUTER JOIN cas_task_has_cas_user d
                    ON a.id_task = d.cas_task_id_task
                    LEFT OUTER JOIN cas_user e
                    ON d.cas_user_id_user = e.id_user
                    LEFT OUTER JOIN cas_task_has_cas_type f
                    ON a.id_task = f.cas_task_id_task
                    LEFT OUTER JOIN cas_type g
                    ON f.cas_type_id_type = g.id_type
                    $sWhere
                    and a.id_task in ($ids_cols)";
        
        $total_time = $model->goCustomQuery($sql_time);
        
        /*
        * Output
        */
        $iTotal = $total_rows->fetch(PDO::FETCH_NUM);
        $iTotal = $iTotal[0];
        
        $iTotalTime = $total_time->fetch(PDO::FETCH_NUM);
        $iTotalTime = $iTotalTime[0];

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array(),
            "iTotalTime" => $iTotalTime,
            "iIdUser" => $session->id_user,
            "iIdProfile" => $session->id_profile
        );

        $k = 1;
        while($aRow = $result_data->fetch(PDO::FETCH_NUM))
        {
            $row = array();

            for($i=0;$i<$aTotalColumns;$i++)
            {
                // FORCE UTF8
                #$row[] = utf8_encode($aRow[ $i ]);
                $row[] = $aRow[$i];
            }

            $output['aaData'][] = $row;

            $k++;
        }

        #echo $sql; //debug
        echo json_encode($output);
    }
    
    public function ajaxTasksList()
    {
        $id_project = $_GET['id_project'];
        
        $session = FR_Session::singleton();
        require_once 'models/TasksModel.php';
        $taskModel = new TasksModel();

        $pdo = $taskModel->getAllTasksByTenantProject($session->id_tenant, $id_project);
//        $result = $pdo->fetchAll(PDO::FETCH_ASSOC);
        
        
        if($pdo->rowCount() > 0){
            echo json_encode($pdo->fetchAll(PDO::FETCH_ASSOC));
        }
        else{
            return false;
            
        }
    }

    /**
     * show task info 
     */
    public function tasksView()
    {
        $session = FR_Session::singleton();
        $paused_date = null;
        
        $id_task = null;
        
        // Support POST & GET
        if(filter_input(INPUT_POST, 'task_id') != ''){
            $id_task = filter_input(INPUT_POST, 'task_id');
        }
        else{
            $id_task = filter_input(INPUT_GET, 'task_id');
        }

        $session->id_task = $id_task;

        require_once 'models/TasksModel.php';
        $model = new TasksModel();

        $pdo = $model->getTaskById($session->id_tenant, $id_task);
        
        $values = $pdo->fetch(PDO::FETCH_ASSOC);
        if($values != null && $values != false){
            #time
            if($values['time_total'] != null){
                $time_s = round($values['time_total'], 2);
                $time_m = round((float)$time_s / 60, 2);
                $time_h = round((float)$time_m / 60, 2);
                $data['time_s'] = $time_s;
                $data['time_m'] = $time_m;
                $data['time_h'] = $time_h;
            }
            
            #current time
            $now = date("Y/m/d H:i:s");
            $currentDateTime = new DateTime($now);
            $timezone = new DateTimeZone($session->timezone);
            $current_date = $currentDateTime->setTimezone($timezone)->format("Y-m-d H:i:s");
            $data['currentTime'] = $current_date;
            
            #current progress
            $total_progress = Utils::diffDates($current_date, $values['date_ini'], 'S', false);

            #paused time
            if($values['date_pause'] != null){
                $total_progress = $total_progress - $values['time_paused'];
                
                $paused_date = Utils::diffDates($values['date_pause'], $values['date_ini'], 'S', false);
                $paused_date = $paused_date - $values['time_paused'];
            }
            
            #data
            $data['id_task'] = $values['id_task'];
            $data['code_task'] = $values['code_task'];
            $data['id_tenant'] = $values['id_tenant'];
            $data['label_task'] = $values['label_task'];
            $data['date_ini'] = $values['date_ini'];
            $data['date_end'] = $values['date_end'];
            $data['time_total'] = $values['time_total']; 
            $data['desc_task'] = $values['desc_task'];
            $data['date_pause'] = $values['date_pause'];
            $data['time_paused'] = $values['time_paused'];
            $data['status_task'] = $values['status_task'];
            $data['id_project'] = $values['cas_project_id_project'];
            $data['id_customer'] = $values['cas_customer_id_customer'];
            $data['id_user'] = $values['id_user'];
            $data['name_user'] = $values['name_user'];
            $data['label_customer'] = $values['label_customer'];
            $data['label_type'] = $values['label_type'];
            
            $data['total_progress'] = $total_progress;
            $data['paused_date'] = $paused_date;
            $data['currentTime'] = $current_date;
        }

        $data['titulo'] = "Detalle de Trabajo";
        $data['pdo'] = $pdo;
        
        $data['action'] = "view";
        
        $this->view->show("tasks_view.php", $data);
    }
    
    public function tasksEditForm()
    {
        $session = FR_Session::singleton();
        $paused_date = null;

        $id_task = filter_input(INPUT_POST, 'task_id');
        $session->id_task = $id_task;

        require_once 'models/TasksModel.php';
        require_once 'models/CustomersModel.php';
        require_once 'models/TypesModel.php';
        $model = new TasksModel();
        $modelCustomer = new CustomersModel();
        $modelTypes = new TypesModel();

        $pdoTypes = $modelTypes->getAllTypesByTenant($session->id_tenant);
        $data['pdoTypes'] = $pdoTypes;
        
        $pdo = $model->getTaskById($session->id_tenant, $id_task);
        
        $values = $pdo->fetch(PDO::FETCH_ASSOC);
        if($values != null && $values != false){
            #time
            if($values['time_total'] != null){
                $time_s = round($values['time_total'], 2);
                $time_m = round((float)$time_s / 60, 2);
                $time_h = round((float)$time_m / 60, 2);
                $data['time_s'] = $time_s;
                $data['time_m'] = $time_m;
                $data['time_h'] = $time_h;
            }
            
            #current time
            $now = date("Y/m/d H:i:s");
            $currentDateTime = new DateTime($now);
            $timezone = new DateTimeZone($session->timezone);
            $current_date = $currentDateTime->setTimezone($timezone)->format("Y-m-d H:i:s");
            $data['currentTime'] = $current_date;
            
            #current progress
            $total_progress = Utils::diffDates($current_date, $values['date_ini'], 'S', false);

            #paused time
            if($values['date_pause'] != null){
                $total_progress = $total_progress - $values['time_paused'];
                
                $paused_date = Utils::diffDates($values['date_pause'], $values['date_ini'], 'S', false);
                $paused_date = $paused_date - $values['time_paused'];
            }
            
            #customers
            $pdoCustomer = $modelCustomer->getAllCustomers($session->id_tenant);
            $data['pdoCustomer'] = $pdoCustomer;
            
            #data
            $data['id_task'] = $values['id_task'];
            $data['code_task'] = $values['code_task'];
            $data['id_tenant'] = $values['id_tenant'];
            $data['label_task'] = $values['label_task'];
            $data['date_ini'] = $values['date_ini'];
            $data['date_end'] = $values['date_end'];
            $data['time_total'] = $values['time_total']; 
            $data['desc_task'] = $values['desc_task'];
            $data['date_pause'] = $values['date_pause'];
            $data['time_paused'] = $values['time_paused'];
            $data['status_task'] = $values['status_task'];
            $data['id_project'] = $values['cas_project_id_project'];
            $data['id_customer'] = $values['cas_customer_id_customer'];
            $data['id_user'] = $values['id_user'];
            $data['name_user'] = $values['name_user'];
            $data['label_customer'] = $values['label_customer'];
            $data['label_type'] = $values['label_type'];
            
            $data['total_progress'] = $total_progress;
            $data['paused_date'] = $paused_date;
            $data['currentTime'] = $current_date;
        }

        $data['titulo'] = "Edición de Trabajo";
        $data['pdo'] = $pdo;
        
        $data['controller'] = "tasks";
        $data['action'] = "edit";
        
        $this->view->show("tasks_edit.php", $data);
    }

    /*
     * Show new project form 
     */
    public function tasksNewForm()
    {
        $session = FR_Session::singleton();

        require_once 'models/ProjectsModel.php';
        require_once 'models/TasksModel.php';
        require_once 'models/UsersModel.php';
        require_once 'models/CustomersModel.php';
        require_once 'models/TypesModel.php';

        $model = new ProjectsModel();
        $modelTask = new TasksModel();
        $modelUser = new UsersModel();
        $modelCustomer = new CustomersModel();
        $modelTypes = new TypesModel();

        $pdo = $modelTask->getLastTask($session->id_tenant);
        $error = $pdo->errorInfo();
        $value = null;
        $value = $pdo->fetch(PDO::FETCH_ASSOC);

        if($error[0] != 00000){
            $data['error'] = "ERROR: ".$error[2];
        }
        else
        {
            $data['error'] = "No Hay trabajos";
        }

        $data['titulo'] = "Nuevo Trabajo";

        $pdoUser = $modelUser->getUserAccountByID($session->id_user);
        $value = null;
        $value = $pdoUser->fetch(PDO::FETCH_ASSOC);

        if($value != null){
            $data['name_user'] = $value['name_user'];
            $data['id_user'] = $value['id_user'];
        }
        else{
            $data['name_user'] = "ERROR";
            $data['id_user'] = 0;
        }

        $pdoCustomer = $modelCustomer->getAllCustomers($session->id_tenant);
        $data['pdoCustomer'] = $pdoCustomer;
        
        $pdoProject = $model->getAllProjectsByTenant($session->id_tenant);
        $data['pdoProject'] = $pdoProject;
        
        //$pdoTypes = $modelTypes->getAllTypesByTenant($session->id_tenant);
        //$data['pdoTypes'] = $pdoTypes;
        
        //$pdoTasks = $modelTask->getAllTasksByTenant($session->id_tenant);
        //$data['pdoTasks'] = $pdoTasks;

        #fecha actual
        $now = date("Y/m/d H:i:s");
        $currentDateTime = new DateTime($now);
        $timezone = new DateTimeZone($session->timezone);
        $currentDateTime = $currentDateTime->setTimezone($timezone);

        $data['current_date'] = $currentDateTime->format("Y-m-d");
        $data['current_time'] = $currentDateTime->format("H:i:s");

        $this->view->show("tasks_new.php", $data);
    }

    /*
     * Add project action
     */
    public function tasksAdd()
    {
        $session = FR_Session::singleton();
        $customer = null;
        $error_user = null;
        $error_cust = null;
        $id_created_task = null;
        $id_project = null;
        $id_customer = null;
        $id_type = null;

        #$new_code = $_POST['new_code'];
        
        // UUID code
        $new_code = Utils::guidv4();
        
        $user = $_POST['resp'];
        $id_user = $_POST['id_user'];
        
        if(isset($_POST['cboprojects'])){
            if(is_numeric($_POST['cboprojects']) && $_POST['cboprojects'] > 0){
                $id_project = $_POST['cboprojects'];
            }
        }
        
        if(isset($_POST['cbocustomers'])){
            if(is_numeric($_POST['cbocustomers']) && $_POST['cbocustomers'] > 0){
                $id_customer = $_POST['cbocustomers'];
            }
        }
        
        if(isset($_POST['cbotypes'])){
            if(is_numeric($_POST['cbotypes']) && $_POST['cbotypes'] > 0){
                $id_type = $_POST['cbotypes'];
            }
        }
        
        $desc = $_POST['descripcion'];

        $fecha = null;
        $hora_ini = null;
        
        if($_POST['chk_past']){
            $fecha = $_POST['fecha'];
            $hora_ini = $_POST['hora_ini'];
            $duration = $_POST['duration'];
            
            //Get end and duration if past job
            $fecha_ini_full = $fecha.' '.$hora_ini;
            $total_time = Utils::formatTimeSeconds($duration);
            $fecha_fin = date('Y/m/d H:i:s', strtotime($fecha_ini_full)+$total_time);
        }
        else{
            $fecha = date("Y/m/d"); #usar fecha de servidor
            $hora_ini = date("H:i:s"); #usar hora de servidor
        }
        
        $etiqueta = $_POST['etiqueta'];
        $estado = 1; #active by default

        require_once 'models/TasksModel.php';
        $model = new TasksModel();
        
        if($_POST['chk_past']){
            $estado = 2;
            $result = $model->addNewTask($session->id_tenant,$new_code,$etiqueta,$fecha, $hora_ini, $fecha_fin, $total_time,$desc,$estado,$id_project, $id_customer);
        }
        else{
            $result = $model->addNewTask($session->id_tenant,$new_code,$etiqueta,$fecha, $hora_ini, null,null,$desc,$estado,$id_project, $id_customer);
        }
        
        $query = $result->queryString;
        
        $error = $result->errorInfo();
        $rows_n = $result->rowCount();

        if($error[0] == 00000 && $rows_n > 0){
            // result OK
            
            $result = $model->getTaskIDByCode($session->id_tenant, $new_code);
            $values = $result->fetch(PDO::FETCH_ASSOC);
            
            $result_user = $model->addUserToTask($values['id_task'], $id_user);
            $error_user = $result_user->errorInfo();
            
            $result_type = $model->addTypeToTask($values['id_task'], $id_type);
            $error_type = $result_type->errorInfo();
            
            #$this->projectsDt(1);
            #header("Location: ".$this->root."?controller=Tasks&action=tasksDt&error_flag=1");
            header("Location: ".$this->root."?controller=Tasks&action=tasksView&task_id=".$values['id_task']);
        }
        elseif($error[0] == 00000 && $rows_n < 1){
            // No rows added

            #$this->projectsDt(10, "Ha ocurrido un error grave!");
            header("Location: ".$this->root."?controller=Tasks&action=tasksDt&error_flag=10&message='Ha ocurrido un error grave'");
        }
        else{
            // Something went wrong

            header("Location: ".$this->root."?controller=Tasks&action=tasksDt&error_flag=10&message='Ha ocurrido un error: ".$error[2].", >> SQL: ".$query." '");
        }
    }

    public function ajaxTaskAdd()
    {
        $session = FR_Session::singleton();

        $label = $_POST['label'];
        $desc = $_POST['desc'];
        $new_code = $_POST['new_code'];
        $status = 1; // 1 by default
        $project = null;
        
        if(isset($_POST['cboproject']))
            $project = $_POST['cboproject'];
        
        #current time
        $now = date("Y/m/d H:i:s");
        $currentDateTime = new DateTime($now);
        $timezone = new DateTimeZone($session->timezone);
        $current_date = $currentDateTime->setTimezone($timezone)->format("Y-m-d H:i:s");
        
        #$code_customer = rand(1, 100);
        #$code_customer = "c".$code_customer;
        
        require_once 'models/TasksModel.php';
        require_once 'models/ProjectsModel.php';

        $modelProject = new ProjectsModel();
        $result = $modelProject->getLastProject($session->id_tenant);
        $values = $result->fetch(PDO::FETCH_ASSOC);
        $code = $values['code_project'];
        $code = (int)$code + 1;
        
        $result = $modelProject->addNewProject($session->id_tenant, $code, 'Sin Proyecto #'.$code, null, null, 'Sin Proyecto #'.$code);
        
        $modelTask = new TasksModel();
        
        $result = $modelTask->getLastTask($session->id_tenant);
        $values = $result->fetch(PDO::FETCH_ASSOC);
        $code = $values['code_task'];
        $code = (int)$code + 1;
        $new_task[] = null;
                
        $result = $modelTask->addNewTask($session->id_tenant, $code, $label, $current_date, null, $desc, $status);

        $error = $result->errorInfo();
        $rows_n = $result->rowCount();
        
        if($error[0] == 00000 && $rows_n > 0){
            $result = $modelTask->getLastTask($session->id_tenant);
            $values = $result->fetch(PDO::FETCH_ASSOC);
            
            $id_task = $values['id_task'];
            
            $new_task[0] = $id_task;
            $new_task[1] = $label_task;
        }
        elseif($error[0] == 00000 && $rows_n < 1){
            $new_task[0] = "0";
            $new_task[1] = "No se ha podido ingresar el registro";
        }
        else{
            $new_task[0] = "0";
            $new_task[1] = $error[2];
        }

        print json_encode($new_task);
        
        return true;
    }

    public function tasksPause()
    {
        $session = FR_Session::singleton();
        $id_task = $_REQUEST['id_task'];
        
        require_once 'models/TasksModel.php';
        require_once 'models/ProjectsModel.php';
        
//        $model = new ProjectsModel();
        $modelTask = new TasksModel();
//        $pdoProject = $model->getProjectById($id_project, $session->id_tenant);
        $pdoTask = $modelTask->getTaskById($session->id_tenant, $id_task);
        $error = null;
        $response = null;
        $total_real_time = null;
        
        $values = $pdoTask->fetch(PDO::FETCH_ASSOC);
        if($values != null && $values != false){
            // current time
            $now = date("Y/m/d H:i:s");
            $currentDateTime = new DateTime($now);
            $timezone = new DateTimeZone($session->timezone);
            $current_date = $currentDateTime->setTimezone($timezone)->format("Y-m-d H:i:s");

            // total time (s)
            $total_progress = Utils::diffDates($current_date, $values['date_ini'], 'S');
            
            // total real time (s)
            if($values['time_paused'] != null && empty($values['time_paused']) == false){
                $total_real_progress = $total_progress - $values['time_paused'];
            }
            else
                $total_real_progress = $total_progress;

            //paused status = 3
            $status = 3;

            //pause project
            $result = $modelTask->updateTask($session->id_tenant, $id_task, $values['code_task']
                    , $values['label_task'], $values['date_ini'], null, null, $values['desc_task']
                    , $status, $values['cas_project_id_project'], $values['cas_customer_id_customer']
                    , $current_date, $values['time_paused']);

            if($result != null){
                $error = $result->errorInfo();
                $num_filas = $result->rowCount();
                if($error[0] == 00000){
                    $response[0] = "0";
                    $response[1] = "Exito!";
                    $response[2] = "filas: ".$num_filas;
                    $response[3] = $result->queryString;
                }
                else {
                    $response[0] = $error[0];
                    $response[1] = $error[2];
                    $response[2] = $result->queryString;
                }
            }
            else{
                $response[0] = "1";
                $response[1] = "Error grave al intentar actualizar el proyecto";
            }
        }
        else{
            $response[0] = "2";
            $response[1] = "Error grave al intentar encontrar el proyecto pedido (ID no existe).";
        }

        print json_encode($response);
    }
    
    public function tasksContinue()
    {
        $session = FR_Session::singleton();
        $id_task = $_REQUEST['id_task'];
        
        require_once 'models/TasksModel.php';
        require_once 'models/ProjectsModel.php';
        
//        $model = new ProjectsModel();
        $model = new TasksModel();
//        $pdoProject = $model->getProjectById($id_project, $session->id_tenant);
        $pdoModel = $model->getTaskById($session->id_tenant, $id_task);
        $error = null;
        $response = null;
        
        if($pdoModel != null){
            $values = $pdoModel->fetch(PDO::FETCH_ASSOC);
            if($values != false){
                // current time
                $now = date("Y/m/d H:i:s");
                $currentDateTime = new DateTime($now);
                $timezone = new DateTimeZone($session->timezone);
                $current_date = $currentDateTime->setTimezone($timezone)->format("Y-m-d H:i:s");

                // current progress
                $total_progress = Utils::diffDates($current_date, $values['date_ini'], 'S', false);

                // paused progress
                $paused_progress = Utils::diffDates($current_date, $values['date_pause'], 'S', false);
                if($values['time_paused'] != null)
                    $paused_progress += $values['time_paused'];

                //normal status = 1
                $status = 1;

    //            print(Utils::formatTime($total_progress));
    //            print("<br>");
    //            print(Utils::formatTime($paused_progress));

                //pause project
                $result = $model->updateTask($session->id_tenant, $id_task, $values['code_task']
                        , $values['label_task'], $values['date_ini'], null
                        , null, $values['desc_task'], $status, null, $values['cas_customer_id_customer']
                        , $values['date_pause'], $paused_progress);

                if($result != null){
                    $error = $result->errorInfo();
                    if($error[0] == 00000){
                        $response[0] = "0";
                        $response[1] = "Exito!";
                    }
                    else {
                        $response[0] = $error[0];
                        $response[1] = $error[2];
                    }
                }
                else{
                    $response[0] = "1";
                    $response[1] = "Error grave al intentar actualizar el proyecto";
                }
            }
            else{
                $errorSearch = $pdoModel->errorInfo();
                $response[0] = "2";
                $response[1] = "Error FETCH: ".print_r($values);
            }
        }
        else{
            $response[0] = "2";
            $response[1] = "Error PDO NULO";
        }

        print json_encode($response);
    }
    
    /*
     * Stop task progress
     */
    public function tasksStop()
    {
        $session = FR_Session::singleton();
        $id_task = $_REQUEST['id_task'];
        $id_project = $_REQUEST['id_project'];
//        $total_real_time = null;
        
        if($id_task != null){
            require_once 'models/TasksModel.php';
            $model = new TasksModel();

            $pdoTask = $model->getTaskById($session->id_tenant, $id_task);
            $values = $pdoTask->fetch(PDO::FETCH_ASSOC);

            // current time
            $now = date("Y/m/d H:i:s");
            $currentDateTime = new DateTime($now);
            $timezone = new DateTimeZone($session->timezone);
            $currentDateTime = $currentDateTime->setTimezone($timezone);
            $current_date = $currentDateTime->format("Y-m-d H:i:s");
            
            // total time (s)
            $total_progress = Utils::diffDates($current_date, $values['date_ini'], 'S');
            
            // total real time (s)
            if($values['time_paused'] != null && empty($values['time_paused']) == false){
                $total_progress = $total_progress - $values['time_paused'];
            }
            else{
                $total_progress = $total_progress;
            }
            
            #tiempo pausa
//            $paused_time = Utils::diffDates($stop_date, $values['date_pause'], 'S', FALSE);
//            if($values['time_paused'] != null)
//                $paused_time += $values['time_paused'];

            #tiempo total
//            $last_time = Utils::diffDates($stop_date, $values['date_ini'], 'S', FALSE);
//            $total_time = $last_time - $paused_time;
            
            //stop status
            $status = 2;
            
            #stop trabajo
            $result = $model->updateTask($session->id_tenant, $id_task, $values['code_task']
                    , $values['label_task'], $values['date_ini'], $current_date, $total_progress
                    , $values['desc_task'], $status, $values['cas_project_id_project'], $values['cas_customer_id_customer']
                    , $values['date_pause'], $values['time_paused']);

            if($result != null){
                $error = $result->errorInfo();
                $numr = $result->rowCount();

                if($error[0] == 00000 && $numr > 0){
                    #$this->projectsDt(1);
                    header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=1");
                }
                else{
                    #$this->projectsDt(10, "Ha ocurrido un error o no se lograron aplicar cambios: ".$error[2]);
                    header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=10&message='No se lograron aplicar cambios: ".$error[2]."'");
                }
            }
            else{
                #$this->projectsDt(10, "Ha ocurrido un error grave!");
                header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=10&message='Error: actualizacion fallida!");
            }
        }
        else{
            #$this->projectsDt(10, "Error, el proyecto no ha sido encontrado.");
            header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=10&message='Error: no existe trabajo!");
        }
    }

    /*
     * Remove a task
     */
    public function tasksRemove()
    {
        $session = FR_Session::singleton();
        $id_task = $_REQUEST['id_task'];
        
        // Support POST & GET
        if(filter_input(INPUT_POST, 'task_id') != ''){
            $id_task = filter_input(INPUT_POST, 'task_id');
        }
        else{
            $id_task = filter_input(INPUT_GET, 'task_id');
        }
        
        if($id_task != null){
            require_once 'models/TasksModel.php';
            $model = new TasksModel();

            $pdoTask = $model->getTaskById($session->id_tenant, $id_task);
            $values = $pdoTask->fetch(PDO::FETCH_ASSOC);
            
            $result = null;
            $status = 9; // 9 removed status

            if($values['status_task'] == 1){
                // remove in-progress task
                
                // current time
                $now = date("Y/m/d H:i:s");
                $currentDateTime = new DateTime($now);
                $timezone = new DateTimeZone($session->timezone);
                $currentDateTime = $currentDateTime->setTimezone($timezone);
                $current_date = $currentDateTime->format("Y-m-d H:i:s");

                // total time (s)
                $total_progress = Utils::diffDates($current_date, $values['date_ini'], 'S');

                // total real time (s)
                if($values['time_paused'] != null && empty($values['time_paused']) == false){
                    $total_progress = $total_progress - $values['time_paused'];
                }
                else{
                    $total_progress = $total_progress;
                }
                
                // remove
                $result = $model->updateTask($session->id_tenant, $id_task, $values['code_task']
                    , $values['label_task'], $values['date_ini'], $current_date, $total_progress
                    , $values['desc_task'], $status, $values['cas_project_id_project'], $values['cas_customer_id_customer']
                    , $values['date_pause'], $values['time_paused']);
            }
            else{
                // remove a finished task
                
                // remove
                $result = $model->updateTask($session->id_tenant, $id_task, $values['code_task']
                    , $values['label_task'], $values['date_ini'], $values['date_end'], $values['time_total']
                    , $values['desc_task'], $status, $values['cas_project_id_project'], $values['cas_customer_id_customer']
                    , $values['date_pause'], $values['time_paused']);
            }      

            if($result != null){
                $error = $result->errorInfo();
                $numr = $result->rowCount();

                if($error[0] == 00000 && $numr > 0){
                    header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=1");
                }
                else{
                    header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=10&message='No se lograron aplicar cambios: ".$error[2]."'");
                }
            }
            else{
                header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=10&message='Error: no se ha podido eliminar!");
            }
        }
        else{
            header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=10&message='Error: este trabajo ya no existe!");
        }
    }
    
    /*
     * Update task data (forced new datetime end)
     */
    public function tasksUpdate()
    {
        $session = FR_Session::singleton();
        $id_task = $session->id_task;

        if($id_task != null){
            require_once 'models/TasksModel.php';
            $model = new TasksModel();
            
            //Form vars
            $id_customer = filter_input(INPUT_POST, 'cbocustomers');
            $label_task = filter_input(INPUT_POST, 'etiqueta');
            $desc_task = filter_input(INPUT_POST, 'descripcion');
            $type = filter_input(INPUT_POST, 'materia');
            $fecha_ini = filter_input(INPUT_POST, 'fecha_ini');
            $hora_ini = filter_input(INPUT_POST, 'hora_ini');
            $total_time = filter_input(INPUT_POST, 'duration');
            $hora_end = filter_input(INPUT_POST, 'hora_end');
            
            //Get current values
            $pdoTask = $model->getTaskById($session->id_tenant, $id_task);
            $values = $pdoTask->fetch(PDO::FETCH_ASSOC);
            
            //Avoid blank values
            $label_task = empty($label_task) ? $values['label_task'] : $label_task;
            $desc_task = empty($desc_task) ? $values['desc_task'] : $desc_task;
            $type = empty($type) ? null : $type;
            $hora_ini = empty($hora_ini) ? null : $hora_ini;
            
            //Date time fix (in case of blank)
            $fecha_ini_fix = null;
            if(empty($fecha_ini)){
                $fecha_ini_fix = $values['date_ini'];
            }
            else{
                $fecha_ini_fix = $fecha_ini.' '.$hora_ini;
            }
            
            //DEBUG
            echo "duration: ".$total_time."<br>";
            echo "hora fin: ".$hora_end."<br><br>";
            
            //Choose end way
            $total_time_fix = null;
            $date_end = null;
            
            if(isset($total_time)){
                //Calculate new end date by the new total time
                if(empty($total_time)){
                    $total_time_fix = $values['time_total'];
                }
                else{
                    $total_time_fix = Utils::formatTimeSeconds($total_time);
                }
                
                $date_end = date('Y/m/d H:i:s', strtotime($fecha_ini_fix)+$total_time_fix);
            }
            elseif(isset($hora_end)){
                //Calculate new total time by the new  end date
                if(empty($hora_end)){
                    $date_end = $values['date_end'];
                    echo "<br>traje todo";
                }
                else{
                    //$date_end
                    echo "<br>viene data!";
                }
            }
            
            if(isset($total_time)){
                echo "<br>existe";
            }
            else{
                echo "<br>no existe";
            }
            
            
                        
            
            //Force new end datetime
            $date_paused = null;
            $time_paused = null;
            
            /*
            //Get update method
            $result = $model->updateTask($session->id_tenant
                    , $values['id_task']
                    , $values['code_task']
                    , $label_task
                    , $fecha_ini_fix
                    , $date_end
                    , $total_time_fix
                    , $desc_task
                    , $values['status_task']
                    , null
                    , $id_customer
                    , $date_paused
                    , $time_paused);

            if($result != null){
                $error = $result->errorInfo();
                $numr = $result->rowCount();

                if($error[0] == 00000){                   
                    header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=1");
                }
                else{
                    $url = "?controller=tasks&action=tasksDt&error_flag=10&message=";
                    header("Location: ".$this->root.$url."'Error al intentar editar: ".$error[2]."'");
                }
            }
            else{
                header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=10&message='Error: actualizacion fallida!");
            }*/
        }
        else{
            header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=10&message='Error: no existe trabajo!");
        }
    }
    
    // Build Excel Report
    public function ajaxBuildXls()
    {
        // Set debug options
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        
        if (PHP_SAPI == 'cli'){
            die('Solo ejecutable desde web browser');
        }
        
        // Include PHPExcel
        require_once 'libs/PHPExcel/Classes/PHPExcel.php';
        
        // Process parameters from $_GET
        $jresult = $this->processTasksJSON();
        $obj = json_decode($jresult);
        
        // data from source
        $data = $obj->{'aaData'};
        
        // total time from source
        $dataTotalTimeRAW = $obj->{'iTotalTime'};
        $dataTotalTime = Utils::formatTime($dataTotalTimeRAW);
        
        // Styles Arrays
        $style_content = array(
            'font' => array(
                'color' => array(
                    'rgb' => '000000'
                    ),
                'bold' => false,
                'name' => 'Arial',
                'size' => '10'
                ),
            'alignment' => array(
                'wrap' => false,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                ),
        );
        $style_subtitle = array(
            'font' => array(
                'color' => array(
                    'rgb' => '000000'
                    ),
                'bold' => true,
                'name' => 'Arial',
                'size' => '11'
                ),
            'alignment' => array(
                'wrap' => false,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                ),
        );
        $style_title = array(
            'font' => array(
                'color' => array(
                    'rgb' => '000000'
                    ),
                'bold' => true,
                'name' => 'Arial',
                'size' => '12'
                ),
            'alignment' => array(
                'wrap' => true,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
        );
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Control de Tiempos")
                                    ->setLastModifiedBy("Control de Tiempos")
                                    ->setTitle("Control de Tiempos - Reporte Excel")
                                    ->setSubject("Reporte Excel")
                                    ->setDescription("Reporte de trabajos encontrados")
                                    ->setKeywords("control tiempos")
                                    ->setCategory("trabajos");
        
        // Get month from parameters
        $requestedDay = Utils::getMonths($_GET['filDia']);
        $requestedMonth = Utils::getMonths($_GET['filMes']);
        $requestedYear = $_GET['filAnio'];
        
        // Title (first row)
        $currentDatetime = date('dmY-His');
        
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Reporte de trabajos - Período: '.$requestedMonth.', '.$requestedYear.' - Fecha exportación: '.date('d-m-Y H:i:s'))
                ->mergeCells('A1:I1')
                ->getRowDimension(1)->setRowHeight(30);
        $objPHPExcel->setActiveSheetIndex(0)
                ->getStyle('A1:I1')->applyFromArray($style_title);

        // Cols title
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A2', 'Inicio')
                ->setCellValue('B2', 'Fin')
                ->setCellValue('C2', 'Cliente')
                ->setCellValue('D2', 'Materia')
                ->setCellValue('E2', 'Gestion')
                ->setCellValue('F2', 'Descripción')
                ->setCellValue('G2', 'Responsable')
                ->setCellValue('H2', 'Tiempo')
                ->setCellValue('I2', 'Tiempo (Dec)')
                ->getStyle('A2:I2')->applyFromArray($style_subtitle);
        
        // first row (custom starting row)
        $row = 3;
        
        // last col (custom last column to export)
        $last_col = 7;
        
        // cols
        $colArray = array (
            0 => 'A',
            1 => 'B',
            2 => 'C',
            3 => 'D',
            4 => 'E',
            5 => 'F',
            6 => 'G',
            7 => 'H',
            8 => 'I',
            9 => 'J',
            10 => 'K',
            11 => 'L',
            12 => 'M'
        );
        
        // Total time (decimal) variable
        $total_decimal = 0;

        // Set content from data
        foreach ($data as $fila => $caso) {
            foreach ($caso as $col => $valor) {
                
                if($col <= $last_col){
                    if($col == 7){
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue($colArray[$col].''.$row, Utils::formatTime($valor));
                        
                        // create decimal format values for time
                        $array_time = split(":", Utils::formatTime($valor));
                        $decimal_hour = $array_time[0];
                        $decimal_minutes = $array_time[1]/60;
                        $decimal_seconds = $array_time[2]/1200;
                        
                        // get total decimal time
                        $decimal_time = round($decimal_hour+$decimal_minutes+$decimal_seconds, 2);
                        $total_decimal += $decimal_time;
                        
                        // set decimal time value to active cell
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue($colArray[$col+1].''.$row, $decimal_time);
                    }
                    else{
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue($colArray[$col].''.$row, $valor);
                    }
                }
            }

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$row.':I'.$row)->applyFromArray($style_content);
            $row++;
        }

        // Set tasks total time on last row
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($colArray[6].''.$row, 'Tiempo total')
                    ->getStyle('A'.$row.':H'.$row)->applyFromArray($style_subtitle);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($colArray[7].''.$row, $dataTotalTime)
                    ->getStyle('A'.$row.':H'.$row)->applyFromArray($style_subtitle);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($colArray[8].''.$row, $total_decimal)
                    ->getStyle('A'.$row.':I'.$row)->applyFromArray($style_subtitle);
        
        // Set autosize ON for each col
        foreach(range('A','I') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                    ->setAutoSize(true);
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Set excel filename
        $fileName = 'reporte_trabajos_'.$currentDatetime.'.xlsx';

        // cleaning
        ob_end_clean();
        ob_start();

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        // Get system (apache) full path
        $config = Config::singleton();
        $systemPath = $config->get('apachePath');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        Utils::SaveViaTempFile($objWriter, $systemPath);
        exit;
    }

    public function processTasksJSON(){
        $session = FR_Session::singleton();
        require_once 'models/TasksModel.php';
        $model = new TasksModel();

        /*
        * Building dynamic query
        */
        #$sTable = $model->getTableName();
        $sTable = "cas_task";

        $aColumns = array(
            'a.date_ini'
            , 'a.date_end'
            , 'c.label_customer'
            , 'g.label_type'
            , 'a.label_task'
            , 'a.desc_task'
            , 'e.name_user'
            , 'a.time_total'
            , 'a.id_task'
            , 'a.id_tenant'
            , 'b.id_project'
            , 'c.id_customer'
            , 'e.id_user'
            , 'f.cas_type_id_type');

        $sIndexColumn = "code_task";
        $aTotalColumns = count($aColumns);

        /******************** Paging *
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
            $sLimit = "LIMIT ".$_GET['iDisplayStart'].", ".$_GET['iDisplayLength'];
         * 
         */

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
                                        $_GET['sSortDir_'.$i].", ";
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
                $sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
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

                $sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
            }
        }

        /******************** Custom Filtering */
        if( isset($_GET['filCliente']) && $_GET['filCliente'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " c.id_customer = '".$_GET['filCliente']."' ";
        }
        if( isset($_GET['filAnio']) && $_GET['filAnio'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " YEAR(a.date_ini) = '".$_GET['filAnio']."' ";
        }
        if( isset($_GET['filMes']) && $_GET['filMes'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " MONTH(a.date_ini) = '".$_GET['filMes']."' ";
        }
        if( isset($_GET['filDia']) && $_GET['filDia'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " DAY(a.date_ini) = '".$_GET['filDia']."' ";
        }
        if( isset($_GET['filType']) && $_GET['filType'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " f.cas_type_id_type = '".$_GET['filType']."' ";
        }
        if( isset($_GET['filEstado']) && $_GET['filEstado'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " a.status_task = '".$_GET['filEstado']."' ";
        }
        if ($_GET['filEstado'] == "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }
            
            $sWhere .= " a.status_task < 9 "; # avoid deleted tasks (status = 9)
        }
        if( isset($_GET['filUser']) && $_GET['filUser'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " e.id_user = '".$_GET['filUser']."' ";
        }
        
        # TENANT
        if ( $sWhere == "" )
        {
                $sWhere = "WHERE a.id_tenant = ".$session->id_tenant;
        }
        else
        {
                $sWhere .= " AND a.id_tenant = ".$session->id_tenant;
        }
        
        # PATCH
//        unset($aColumns[5]);    // replace column by group
//        $aColumns[5] = "IFNULL(a.time_total/3600, '') AS time_total";

        /********************** Create Query */
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS 
                ".str_replace(" , ", " ", implode(", ", $aColumns))."
            FROM $sTable a
            LEFT OUTER JOIN cas_project b
            ON (a.cas_project_id_project = b.id_project
                AND 
                a.id_tenant = b.id_tenant)
            LEFT OUTER JOIN cas_customer c
            ON (a.cas_customer_id_customer = c.id_customer
                AND 
                a.id_tenant = c.id_tenant)
            LEFT OUTER JOIN cas_task_has_cas_user d
            ON a.id_task = d.cas_task_id_task
            LEFT OUTER JOIN cas_user e
            ON d.cas_user_id_user = e.id_user
            LEFT OUTER JOIN cas_task_has_cas_type f
            ON a.id_task = f.cas_task_id_task
            LEFT OUTER JOIN cas_type g
            ON f.cas_type_id_type = g.id_type
            $sWhere
            $sOrder";

        #print($sql);

        //Result needed data
        $result_data = $model->goCustomQuery($sql);
        
        $found_rows = $model->goCustomQuery("SELECT FOUND_ROWS()");
        $foundTotal = $found_rows->fetch(PDO::FETCH_NUM);
        $iFilteredTotal = $foundTotal[0];
        
        $total_rows = $model->goCustomQuery("SELECT COUNT(`".$sIndexColumn."`) FROM $sTable");

        //Found ids
        $sql_ids = "SELECT a.id_task as id_task FROM $sTable a
                    LEFT OUTER JOIN cas_project b
                    ON (a.cas_project_id_project = b.id_project
                        AND 
                        a.id_tenant = b.id_tenant)
                    LEFT OUTER JOIN cas_customer c
                    ON (a.cas_customer_id_customer = c.id_customer
                        AND 
                        a.id_tenant = c.id_tenant)
                    LEFT OUTER JOIN cas_task_has_cas_user d
                    ON a.id_task = d.cas_task_id_task
                    LEFT OUTER JOIN cas_user e
                    ON d.cas_user_id_user = e.id_user
                    LEFT OUTER JOIN cas_task_has_cas_type f
                    ON a.id_task = f.cas_task_id_task
                    LEFT OUTER JOIN cas_type g
                    ON f.cas_type_id_type = g.id_type
                    $sWhere
                    $sLimit";

        $idsPdo = $model->goCustomQuery($sql_ids);
        $ids_array = null;
        $ids_cols = "";
        
        $realTotal = $idsPdo->rowCount();
        for($k = 0; $k<$realTotal; $k++){
            $ids_row = $idsPdo->fetch(PDO::FETCH_ASSOC);
            $ids_array[$k] = $ids_row['id_task'];
            $ids_cols = $ids_cols.$ids_row['id_task'];
            
            if($k < $realTotal-1){
                $ids_cols = $ids_cols.", ";
            }
        }

        //Sum found task times
        $sql_time = "SELECT SUM(a.time_total) FROM $sTable a
                    LEFT OUTER JOIN cas_project b
                    ON (a.cas_project_id_project = b.id_project
                        AND 
                        a.id_tenant = b.id_tenant)
                    LEFT OUTER JOIN cas_customer c
                    ON (a.cas_customer_id_customer = c.id_customer
                        AND 
                        a.id_tenant = c.id_tenant)
                    LEFT OUTER JOIN cas_task_has_cas_user d
                    ON a.id_task = d.cas_task_id_task
                    LEFT OUTER JOIN cas_user e
                    ON d.cas_user_id_user = e.id_user
                    LEFT OUTER JOIN cas_task_has_cas_type f
                    ON a.id_task = f.cas_task_id_task
                    LEFT OUTER JOIN cas_type g
                    ON f.cas_type_id_type = g.id_type
                    $sWhere
                    and a.id_task in ($ids_cols)";
        
        $total_time = $model->goCustomQuery($sql_time);
        
        /*
        * Output
        */
        $iTotal = $total_rows->fetch(PDO::FETCH_NUM);
        $iTotal = $iTotal[0];
        
        $iTotalTime = $total_time->fetch(PDO::FETCH_NUM);
        $iTotalTime = $iTotalTime[0];

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array(),
            "iTotalTime" => $iTotalTime
        );

        $k = 1;
        while($aRow = $result_data->fetch(PDO::FETCH_NUM))
        {
            $row = array();

            for($i=0;$i<$aTotalColumns;$i++)
            {
                // FORCE UTF8
                #$row[] = utf8_encode($aRow[ $i ]);
                $row[] = $aRow[$i];
            }

            $output['aaData'][] = $row;

            $k++;
        }

        #echo $sql; //debug
        return json_encode($output);
    }
    
    public function getTasksName() {
        
        $session = FR_Session::singleton();

        require_once 'models/TasksModel.php';

        $modelTask = new TasksModel();
        
        
        $pdo_listado = $modelTask->getAllTasksNameByTenant($session->id_tenant);
       
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
}
