<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>

<!-- AGREGAR JS & CSS AQUI -->
<style type="text/css" title="currentStyle">
    @import "views/css/datatable.css";
    table.dataTable, table.filtres {
        width: 800px;
    }
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/utils.js"></script>
<script type="text/javascript">
function submitToForm(){
    $('#action_type').val("view");

    return false;
}
    
$(document).ready(function() {
    var oTable = $('#users_table').dataTable({
        //Initial server side params
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": <?php echo "'?controller=panel&action=ajaxUsersDt'";?>,
        "fnServerData": function ( sSource, aoData, fnDrawCallback ){
            $.ajax({
                "dataType": 'json', 
                "type": "GET", 
                "url": sSource, 
                "data": aoData, 
                "success": fnDrawCallback
            });
        },
        
        "sDom": '<"top"lpf>rt<"clear">',
        
        "oLanguage": {
            "sInfo": "_TOTAL_ registros",
            "sInfoEmpty": "0 registros",
            "sInfoFiltered": "(de _MAX_ registros)",
            "sLengthMenu": "_MENU_ por p&aacute;gina",
            "sZeroRecords": "No hay registros",
            "sInfo": "_START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 registros",
            "sSearch": "Buscar",
            "oPaginate": {
                "sFirst": "Primera",
                "sNext": "Siguiente",
                "sPrevious": "Anterior",
                "sLast": "&Uacute;ltima"
            }
        },
        
        //Custom filters params
//        "fnServerParams": function ( aoData ){
//            aoData.push(
//                { "name": "filCliente", "value": $('#cboCliente').val() },
//                { "name": "filMes", "value": $('#cboMes').val() },
//                { "name": "filEstado", "value": $('#cboEstado').val() }
//            );
//        },
        
        "aoColumnDefs": [
            { "mDataProp": null, "aTargets": [-1] },
            { "bVisible": false, "aTargets": [0,1,2] },
            {
                "fnRender": function ( oObj ) {
                    //var string = '<button id=\"button\" class=\"input\" name=\"id_project\" onclick=\"submitToForm()\" value="'+oObj.aData[0]+'">EDITAR</button>';
                    var string = "<a class=\'btn_edit\' href='?controller=panel&action=editUserForm&user_id="+oObj.aData[0]+"'>EDITAR</button>";
                    
                    return string;
                },
                "aTargets": [-1]
            }
//            {
//                "fnRender": function ( oObj ) {
//                    if(oObj.aData[5] != null){
//                        var seconds = oObj.aData[5];
//                        var total = secondsToTime(seconds);
//
//                        return total['h']+':'+total['m']+':'+total['s'];
//                    }
//                    else{
//                        return '';
//                    }
//                },
//                "aTargets": [5]
//            },
        ],
        
        "sPaginationType": "full_numbers",
        "aaSorting": [[0, "asc"]]
    });
    
//    $('#cboCliente').change(function() { oTable.fnDraw(); } );
//    $('#cboMes').change(function() { oTable.fnDraw(); } );
//    $('#cboEstado').change(function() { oTable.fnDraw(); } );
//    
//    // form submition handling
//    $('#dt_form').submit( function() {
//        var sData = oTable.$('input').serialize();
//        var actionType = $('#action_type').val();
//        var urlAction = "";
//        
//        if(actionType == 'edit_form'){
//            urlAction = "<?php #echo "?controller=".$controller."&amp;action=".$action;?>";
//            $('#action_type').val("");
//            
//            return true;
//        }
//    });
    
});

function editUser(user){
    console.log(user);
    var urlAction = "<?php echo "?controller=panel&action=editUserForm";?>";
    
    $('#dt_form').attr('action', urlAction);
    $('#dt_form').attr('method', 'POST');
    $('#user_id').val(user);
    
    $("#dt_form").submit();
}


</script>

</head>
<body id="dt_example" class="ex_highlight_row">

<?php
require('templates/menu.tpl.php'); #banner & menu
?>
    <!-- CENTRAL -->
    <div id="central">
    <div id="contenido">

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

        <p class="titulos-form"><?php echo $titulo; ?></p>

        
        <?php 
        if (isset($error_flag)){
            if(strlen($error_flag) > 0)
                echo $error_flag;
        }
        else
            echo "aaaa";
        ?>

        <!-- CUSTOM FILTROS -->
        <!-- END CUSTOM FILTROS -->

        <!-- DATATABLE -->
        <div id="dynamic">
            <form id="dt_form" method="POST" action="<?php echo "?controller=panel&amp;action=ajaxUsersDt";?>">
                <table class="display" id="users_table">
                    <thead>
                        <tr class="headers">
                            <th>ID</th>
                            <th>CODIGO USUARIO</th>
                            <th>TENANT</th>
                            <th>NOMBRE USUARIO</th>
                            <th>PERFIL</th>
                            <th>OPCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty">Loading data from server</td>
                        </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td><input id="action_type" type="hidden" name="action_type" value="" /></td>
                    </tr>
                </table>
                <table style="float:left"> <!-- style float solo para perderlo -->
                    <tr>
                        <td><input id="user_id" type="hidden" name="user_id" value="" /></td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="spacer"></div>

    </div>
    </div>
    <!-- END CENTRAL -->

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>