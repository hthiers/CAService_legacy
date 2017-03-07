<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>

<!-- AGREGAR JS & CSS AQUI -->
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables-control.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/utils.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery.jeditable.js"></script>

<?php require_once('js_customers_dt.php'); # JS ?>

</head>
<body id="dt_example" class="ex_highlight_row">

<?php
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
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <h1>
            <span class="icon-title fi-book-bookmark"></span><?php echo $titulo; ?>
        </h1>

        <?php
        if (isset($error_flag)){
            if(strlen($error_flag) > 0){
                echo $error_flag;
            }
        }
        ?>

        <!-- CUSTOM FILTROS -->
        <!-- END CUSTOM FILTROS -->

        <!-- DATATABLE -->
        <div id="dynamic">
            <form id="dt_form" method="POST" action="<?php echo "?controller=".$controller."&amp;action=".$action;?>">
                <table class="display" id="example">
                    <thead>
                        <tr class="headers">
                            <th>ID</th>
                            <th>NUMERO</th>
                            <th>TENANT</th>
                            <th>Nombre cliente</th>
                            <th>Descripción cliente</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty">Cargando...</td>
                        </tr>
                    </tbody>
                </table>
                <input id="action_type" type="hidden" name="action_type" value="" />
            </form>
        </div>

        <div id="footer" class="headers" style="color:#ffffff;">
            <p style="text-align:right;">&nbsp;</p>
        </div>

        <div class="spacer"></div>

    </div>
    <!-- END CENTRAL -->

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>
