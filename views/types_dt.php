<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>

<!-- AGREGAR JS & CSS AQUI -->
<style type="text/css" title="currentStyle">
    #new_type_label {
        /*-webkit-box-sizing: border-box; /* webkit */
        /*-moz-box-sizing: border-box; /* firefox */
        /*box-sizing: border-box; /* css3 */

        /*border: 1px solid #aaa;
        border-radius: 4px;

        line-height: 26px;
        vertical-align: middle;*/
    }
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables-control.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/utils.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery.jeditable.js"></script>

<?php require_once('js_types_dt.php'); # JS ?>

</head>
<body id="dt_example" class="ex_highlight_row">

<?php
    // require('templates/dialogs.tpl.php');
    require('templates/menu.tpl.php'); #banner & menu
?>
    <!-- CENTRAL -->
    <div class="row">

        <!-- DEBUG -->
        <?php
        if($debugMode)
        {
            print('<div id="debugbox">');
            print("tenant: ".$session->id_tenant.", user: ".$session->id_user."<br/>");
            print_r($titulo); print('<br />');
            print_r($listado); print('<br />');
            print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
//            print_r($arrayDates);print('<br />');
            #print_r($permiso_editar); print('<br />');
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <h1>
            <span class="icon-title fi-pricetag-multiple"></span><?php echo $titulo; ?>
        </h1>

        <!-- <input
            type="text"
            id="new_type_label"
            name="new_type_label"
            style="margin-left: 20%;"
            placeholder="Nueva materia..." />
        <select
            class="js-example-responsive hidden-element"
            style="width:20%"
            id="cbocustomers"
            name="cbocustomers">
            <?php
            // while($row = $listadoClientes->fetch(PDO::FETCH_ASSOC))
            // {
            //     echo "<option value='$row[id_customer]'>$row[label_customer]</option>\n";
            // }
            ?>
        </select>
        &nbsp;
        <input type="button" id="create-type" style="width:22px;height:22px;display:inline;" class="ui-icon ui-icon-circle-plus" /> -->

        <!--<div class="new-type" >
            Nueva Materia
            <input type="text" name="new_type_label" />
            <button id="create-type">Crear</button>
            <br />
        </div>-->

        <?php
        if (isset($error_flag)){
            if(strlen($error_flag) > 0){
                echo $error_flag;
            }
        }

        ?>
        
        <?php require('modal_types_dt.php'); ?>

        <!-- CUSTOM FILTROS -->
        <!-- END CUSTOM FILTROS -->

        <!-- DATATABLE -->
        <div id="dynamic">
            <form id="dt_form" method="POST" action="<?php echo "?controller=types&amp;action=ajaxTypesDt";?>">
                <table class="display" id="table">
                    <thead>
                        <tr class="headers">
                            <th>ID</th>
                            <th>CODIGO MATERIA  </th>
                            <th>TENANT</th>
                            <th>Materia</th>
                            <th>IDCLIENTE</th>
                            <th>CLIENTE</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty">Cargando...</td>
                        </tr>
                    </tbody>
                </table>
                <input id="action_type" type="hidden" name="action_type" value="" />
                <input id="type_id" type="hidden" name="type_id" value="" />
            </form>
        </div>

        <div id="footer" class="headers" style="color:#ffffff;">
            <p style="text-align:right;">&nbsp;</p>
        </div>

    </div>
    <!-- END CENTRAL -->

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');
