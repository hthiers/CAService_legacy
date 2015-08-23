<?php
class PanelController extends ControllerBase 
{
    /*******************************************************************************
    * Contexto de Usuarios
    *******************************************************************************/

    /**
     * Show users dt
     * @param type $error_flag
     * @param type $message 
     */
//    public function usersDt($error_flag = 0, $message = "")
//    {
//        $session = FR_Session::singleton();
//        
//        #support global messages
//        if(isset($_GET['error_flag']))
//            $error_flag = $_GET['error_flag'];
//        if(isset($_GET['message']))
//            $message = $_GET['message'];
//        
//        //incluye el modelo que corresponde
//        require_once 'models/UsersModel.php';
//        
//        //Se crea una instancia del "modelo"
//        $model = new UsersModel();
//        
//        //Se pide al modelo todos los usuarios
//        $listado = $model->getAllUserAccountByTenant($session->id_tenant);
//        
//        $data['listado'] = $listado;
//    }
    
    /*
     * Show new user form 
     */
    public function newUserForm()
    {
        $session = FR_Session::singleton();
        
        //incluye el modelo que corresponde
        require_once 'models/ProfilesModel.php';
        
        $data['titulo'] = "Nuevo Usuario";
        
        //Se crea una instancia del "modelo"
        $model = new ProfilesModel();
        
        $pdoProfiles = $model->getAllProfiles();
        $data['profiles'] = $pdoProfiles;
        
        $this->view->show("users_new.php", $data);
    }
    
    public function newUserAdd()
    {
        //Incluye el modelo que corresponde
        require_once 'models/UsersModel.php'; 
        
        $session = FR_Session::singleton();
        $model = new UsersModel();
        $profile_user = null; //Daclaración de variable antes de capturar el valor de un combobox
        
        $name_user = filter_input(INPUT_POST, 'name_user');
        $profile_user = filter_input(INPUT_POST, 'cboprofiles');
        
        $password = filter_input(INPUT_POST, 'pass_user');
        //$result_last_code = $model->getLastCodeUser();
        //$values_last_code = $result_last_code->fetch(PDO::FETCH_ASSOC);

        //$code_user = $values_last_code["code"] + 1;
        $code_user = Utils::guidv4();
        
        $result = $model->addNewUser($session->id_tenant, $code_user, $name_user, $profile_user, $password);
        $error = $result->errorInfo();
        $rows_n = $result->rowCount();
        
        if($error[0] == 00000 && $rows_n > 0){
            #$this->projectsDt(1);
            header("Location: ".$this->root."?controller=Panel&action=usersDt&error_flag=1");
        }
        elseif($error[0] == 00000 && $rows_n < 1){
            #$this->projectsDt(10, "Ha ocurrido un error grave!");
            header("Location: ".$this->root."?controller=Panel&action=usersDt&error_flag=10&message='Ha ocurrido un error grave'");
        }
        else{
            #$this->projectsDt(10, "Ha ocurrido un error: ".$error[2]);
            header("Location: ".$this->root."?controller=Panel&action=usersDt&error_flag=10&message='Ha ocurrido un error: ".$error[2]."'");
        }
        
    }
    
    public function ajaxUsersDt()
    {
        $session = FR_Session::singleton();
        
        //Incluye el modelo que corresponde
        require_once 'models/UsersModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new UsersModel();

        /*
        * Build up dynamic query
        */
        $sTable = $model->getTableName();

        $aColumns = array('u.id_user'
                    , 'u.code_user'
                    , 'u.id_tenant'
                    , 'u.name_user'
                    , 'p.label_profile');
        
        $sIndexColumn = "id_user";

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

        /******************** Custom Filtering */
//        if( isset($_GET['filResponsable']) && $_GET['filResponsable'] != "")
//        {
//            if ( $sWhere == "" )
//            {
//                    $sWhere = "WHERE ";
//            }
//            else
//            {
//                    $sWhere .= " AND ";
//            }
//
//            $sWhere .= " A.TIPO LIKE '%".mysql_real_escape_string($_GET['filTipo'])."%' ";
//        }

        /********************** Create Query */
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS 
                ".str_replace(" , ", " ", implode(", ", $aColumns))."
            FROM $sTable u
            INNER JOIN cas_profile p ON ( u.id_profile = p.id_profile ) 
            INNER JOIN cas_tenant b
            ON (u.id_tenant = b.id_tenant
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
            }

            $output['aaData'][] = $row;

            $k++;
        }

        echo json_encode( $output );
    }
    
    public function editUserForm() 
    {
        require_once 'models/UsersModel.php';
        require_once 'models/ProfilesModel.php';
        require_once 'vo/UserVO.php';
        
        #support global messages
        if(isset($_GET['error_flag']))
            $error_flag = $_GET['error_flag'];
        if(isset($_GET['message']))
            $message = $_GET['message'];
        
        $user = new UserVO();
        //Asegurar que session esta abierta
        $session = FR_Session::singleton();
        $id = filter_input(INPUT_GET, 'user_id');
        $user_model = new UsersModel();
        $profile_model = new ProfilesModel();
        $user->setIdUser($id);
        $pdoProfiles = $profile_model->getAllProfiles();
        $pdoUser = $user_model->getUserById($user);
        
        $data['title'] = "Editar Usuario";
        $data['profiles'] = $pdoProfiles;
        $data['user'] = $pdoUser;
        $data['message'] = $message;
        //$data['error_flag'] =  $error_flag;
        /*
        if($error_flag == 1)
        {
            $data['error_flag'] = $this->errorMessage->getError(1);
        }
        else{
            $data['error_flag'] = $this->errorMessage->getError($error_flag,$message);
        }
        */
        $data['error_flag'] = $this->errorMessage->getError($error_flag,$message);

        
        $this->view->show("users_edit.php", $data);
    }
    
    public function editUserFormSession() 
    {
        require_once 'models/UsersModel.php';
        require_once 'models/ProfilesModel.php';
        require_once 'vo/UserVO.php';

        #support global messages
        if(isset($_GET['error_flag']))
            $error_flag = $_GET['error_flag'];
        if(isset($_GET['message']))
            $message = $_GET['message'];
        
        $user = new UserVO();
        //Asegurar que session esta abierta
        $session = FR_Session::singleton();
        
        $user_model = new UsersModel();
        $profile_model = new ProfilesModel();
        $user->setIdUser($session->id_user);
        $pdoProfiles = $profile_model->getAllProfiles();
        $pdoUser = $user_model->getUserById($user);
        
        $data['title'] = "Editar Usuario";
        $data['profiles'] = $pdoProfiles;
        $data['user'] = $pdoUser;
        $data['message'] = $message;

        $data['error_flag'] = $this->errorMessage->getError($error_flag,$message);
        
        $this->view->show("users_edit.php", $data);
    }
    
    public function userEdit()
    {
        //Incluye el modelo que corresponde
        require_once 'models/UsersModel.php'; 
        require_once 'vo/UserVO.php';
        
        $session = FR_Session::singleton();
        $model = new UsersModel();
        $user = new UserVO();
        $id_user = filter_input(INPUT_POST, 'id_user');
        $user->setIdUser($id_user);
        $pdoUser = $model->getUserById($user);
        $dataUser = $pdoUser->fetch(PDO::FETCH_ASSOC);
        $user->setCodeUser($dataUser['code_user']);
        $user->setIdTenant($dataUser['id_tenant']);
        $user->setNameUser(filter_input(INPUT_POST, 'name_user'));
        $user->setIdProfile(filter_input(INPUT_POST, 'cboprofiles')) ;
        //$user->setPasswordUser($dataUser[password_user]);
        $user->setPasswordUser('');
        //$pass1 = filter_input(INPUT_TYPE, 'pass_user_1');
        //$pass2 = filter_input(INPUT_TYPE, 'pass_user_2');
        $pass1 = $_POST['pass_user_1'];
        $pass2 = $_POST['pass_user_2'];
        //if(isset($_POST['pass_user_1']) && isset($_POST['pass_user_2']))
        if($pass1 !='' && $pass2 !='')
        {
            if($pass1 == $pass2)
            {
                $user->setPasswordUser($pass1);
                $validacion = $this->validarDatosUsuario($user, 'normal');
            }
            else
            {
                $user->setPasswordUser('');
                $validacion = $this->validarDatosUsuario($user, 'distintos');
            }
        }
        else if ($pass1 =='' && $pass2 =='')
        {
            $user->setPasswordUser($dataUser['password_user']);
            $validacion = $this->validarDatosUsuario($user, 'md5');
        }

        if($validacion["estado"] == true )
        {
            if($pass1 == $user->getPasswordUser()){
                $user->setPasswordUser(md5($user->getPasswordUser()));
            }
            else {
                $user->setPasswordUser($dataUser[password_user]);
            }
            
            $result = $model->editUser($user);
            $error = $result->errorInfo();
            $rows_n = $result->rowCount();

            if($error[0] == 00000 && $rows_n > 0){
                #$this->projectsDt(1);
                header("Location: ".$this->root."?controller=Panel&action=usersDt&error_flag=1");
            }
            elseif($error[0] == 00000 && $rows_n < 1){
                #$this->projectsDt(10, "Ha ocurrido un error grave!");
                header("Location: ".$this->root."?controller=Panel&action=usersDt&error_flag=10&message='Ha ocurrido un error grave'");
            }
            else{
                #$this->projectsDt(10, "Ha ocurrido un error: ".$error[2]);
                header("Location: ".$this->root."?controller=Panel&action=usersDt&error_flag=10&message='Ha ocurrido un error: ".$error[2]." ___ ".$id_user."'");
            }
        }
        else {
            //$pdoUser = $model->getUserById($user);
            header("Location: ".$this->root."?controller=Panel&action=editUserForm&error_flag=10&user_id=".$id_user."&message='Ha ocurrido un error: ".$validacion['error']."'");
        }
        
    }
    
    public function usersDt()
    {
        $session = FR_Session::singleton();
        
        #support global messages
        if(isset($_GET['error_flag']))
            $error_flag = $_GET['error_flag'];
        if(isset($_GET['message']))
            $message = $_GET['message'];
        
        //Incluye el modelo que corresponde
        require_once 'models/UsersModel.php';
        require_once 'vo/UserVO.php';
        //Creamos una instancia de nuestro "modelo"
        $model = new UsersModel();
        $user = new UserVO();
        
        $user->setIdTenant($session->id_tenant);
        //Le pedimos al modelo todos los items
        $listado = $model->getAllUsers($user);

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
        $data['titulo'] = "Usuarios";

        //Controller
        $data['controller'] = "panel";
        $data['action'] = "editUserForm";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag,$message);

        //Finalmente presentamos nuestra plantilla
        $this->view->show("users_dt.php", $data);
    }
    
    public function removeUserAction()
    {
        
    }
    
    public function validarDatosUsuario(UserVO $user, $tipoPass)
    {
        $mensaje['estado'] = true;
        $mensaje['largoNombre'] = strlen($user->getNameUser());
        $mensaje['largoPass'] = strlen($user->getPasswordUser());

        // si $tipoPass es normal, el password no es md5
        // si $tipoPass es md5, el pass tiene formato md5

        if(strlen($user->getNameUser()) < 5 )
        {
            $mensaje['error'] = "El nombre de usuario debe tener más de 4 letras";
            $mensaje['estado'] = false;
        }

        if($tipoPass == 'normal')
        {
            if(strlen($user->getPasswordUser()) < 5 )
            {
                $mensaje['error'] = "La contraseña  debe tener más de 4 letras";
                $mensaje['estado'] = false;
            }
        }
        else if($tipoPass == 'distintos')
        {
            $mensaje['error'] = "Los Campos de la contraseña deben ser iguales";
            $mensaje['estado'] = false;
        }
        
        return $mensaje;
    }
}
