<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>

<!-- AGREGAR JS & CSS AQUI -->
<link rel="stylesheet" href="views/css/dataTables.tableTools.min.css">
<link rel="stylesheet" href="views/css/select2.css">
<style type="text/css" title="currentStyle">
    .select2-container--default .select2-selection--single {
        font-size: 12px;
    }

    .select2-results .select2-result-label {
        font-size: 12px;
        padding: 1px 0 2px;
    }

    .select2-container, .select2-drop, .select2-search, .select2-search input {
        width: 100%;
    }
</style>
<!--<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min.js"></script>-->
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables-control.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery-tableTools.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/utils.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/select2.js"></script>

<?php require_once('tasks_lib.php'); # JS ?>

</head>

<body id="dt_example" class="ex_highlight_row">

    <?php #require('templates/dialogs.tpl.php'); #banner & menu ?>
    <?php require('templates/menu.tpl.php'); #banner & menu ?>

    <!-- CENTRAL -->
    <div class="row container">

        <!-- DEBUG -->
        <?php
        if($debugMode)
        {
            print('<div class="row" id="debugbox">');
            print("tenant: ".$session->id_tenant.", user: ".$session->id_user."<br/>");
            print_r($titulo); print('<br />');
            print_r($listado); print('<br />');
            print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
            print_r($arrayDates);print('<br />');
            print_r($clientes);print('<br />');
            print_r($types);print('<br />');
            #print_r($permiso_editar); print('<br />');
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <h1>
            <span class="icon-title fi-list-bullet"></span><?php echo $titulo; ?>
        </h1>

        <?php
        if (isset($error_flag)){
            if(strlen($error_flag) > 0){
                echo $error_flag;
            }
        }
        ?>

         <!--CUSTOM FILTROS-->
        <div class="toolbar">
            <table>
                <thead>
                    <tr>
                        <th>Año</th>
                        <th>Mes</th>
                        <th>D&iacute;a</th>
                        <th>Estado</th>
                        <th>Cliente</th>
                        <th>Materia</th>
                        <th>Responsable</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select id="cboAnio" onChange="getLastDay('cboMes', 'cboAnio', 'cboDia')">
                                <?php
                                echo "<option selected value=".date('Y').">". date('Y') ."</option>";
                                echo "<option value=".date('Y',strtotime('-1 year')).">". date('Y',strtotime('-1 year')) ."</option>";
                                ?>
                            </select>
                        </td>
                        <td style="width:15%">
                            <select
                                id="cboMes"
                                onChange="getLastDay('cboMes', 'cboAnio', 'cboDia')"
                                class="js-example-responsive">

                                <?php
                                for ($i=0; $i<=sizeof($arrayDates); $i++){
                                    if($i == date("m")){
                                        echo "<option selected value='$i'>". $arrayDates[$i] . "</option>";
                                    }
                                    else {
                                        echo "<option value='$i'>". $arrayDates[$i] . "</option>";
                                    }

                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <select id="cboDia">
                                <option value="">Todos</option>
                            </select>
                        </td>
                        <td style="width:5%">
                            <select id="cboEstado">
                                <?php
                                echo "<option selected value=''>Todos</option>";
                                echo "<option value=1>En curso</option>";
                                echo "<option value=2>Terminado</option>";
                                ?>
                            </select>
                        </td>
                        <td style="width:30%">
                            <select
                                id="cboCliente"
                                class="js-example-responsive">
                                <?php
                                echo "<option selected value=''>Todos</option>";
                                for ($i=0; $i<sizeof($clientes); $i++){
                                        echo "<option value=".$clientes[$i][0].">". $clientes[$i][3] . "</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <td style="width:30%">
                            <select
                                id="cboType"
                                class="js-example-responsive">
                                <?php
                                echo "<option selected value=''>Todas</option>";
                                for ($i=0; $i<sizeof($types); $i++){
                                        echo "<option value=".$types[$i][0].">". $types[$i][2] . "</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <td style="width:20%">
                            <select
                                id="cboUser"
                                class="js-example-responsive">
                                <?php
                                echo "<option value=''>Todos</option>";

                                for ($i=0; $i<sizeof($users); $i++){

                                        //check if item is current user
                                        $selected = "";
                                        if($users[$i][0] == $session->id_user){
                                            $selected = "selected";
                                        }

                                        echo "<option ".$selected." value=".$users[$i][0].">". $users[$i][3] . "</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
        <!--END CUSTOM FILTROS-->

        <!-- DATATABLE -->
        <div id="dynamic">
            <form id="dt_form" method="" action="">
                <table class="display" id="example">
                    <thead>
                        <tr class="headers">
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Cliente</th>
                            <th>Materia</th>
                            <th>Gestion</th>
                            <th>Responsable</th>
                            <th>Tiempo</th>
                            <th>ID TASK</th>
                            <th>ID TENANT</th>
                            <th>ID PROJECT</th>
                            <th>ID CUSTOMER</th>
                            <th>ID USER</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty">Procesando...</td>
                        </tr>
                    </tbody>
                </table>
                <input id="task_id" type="hidden" name="task_id" value="" />
            </form>
        </div>

        <div id="footer" class="headers" style="color:#ffffff;">
            <p style="text-align:right;">Tiempo Total: </p>
        </div>

        <div class="spacer"></div>

    </div>
    <!-- END CENTRAL -->

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');
