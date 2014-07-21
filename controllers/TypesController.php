<?php
class TypesController extends ControllerBase
{
    /*******************************************************************************
    * TYPES
    *******************************************************************************/

    public function ajaxTypesAdd()
    {   
        $session = FR_Session::singleton();

        if(isset($_POST['label_type']) && $_POST['label_type'] != ""):
            $label_type = $_POST['label_type'];
            #$detail_customer = $_POST['desc'];
            #$code_customer = rand(1, 100);
            #$code_customer = "c".$code_customer;

            //Incluye el modelo que corresponde
            require_once 'models/TypesModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new TypesModel();

            $result = $model->getLastType($session->id_tenant);
            $values = $result->fetch(PDO::FETCH_ASSOC);
            $code_type = $values['code_type'];
            $new_code_type = (int)$code_type + 1;
            $new_type[] = null;

            //Le pedimos al modelo todos los items
            $resultPdo = $model->addNewType(null, $new_code_type, $session->id_tenant, $label_type);

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
}
?>
