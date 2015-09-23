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
<script type="text/javascript" language="javascript" src="views/lib/jquery.jeditable.js"></script>
<script type="text/javascript">
function submitToForm(){
    $('#action_type').val("view");

    return false;
}
    
$(document).ready(function() {
    var oTable = $('#table').dataTable({
        //Initial server side params
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": <?php echo "'?controller=types&action=ajaxTypesDt'";?>,
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
            { "bVisible": false, "aTargets": [0,1,2] }
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
        "aaSorting": [[0, "asc"]],

        "fnDrawCallback": function () {
            $('#table tbody td').editable( '?controller=types&action=ajaxTypesUpdate', {
                
                "callback": function( sValue, y ) {
                    console.log("valor: "+ sValue);
                    /* Redraw the table from the new data on the server */
                    //oTable.fnDraw();
                    var aPos = oTable.fnGetPosition( this );
                    oTable.fnUpdate( sValue, aPos[0], aPos[1] );
                },
                "submitdata": function ( value, settings ) {
                    return {
                        "row_id": this.parentNode.getAttribute('id'),
                        "column": oTable.fnGetPosition( this )[2]
                    };
                },
                "height": "14px"
            } );
        }
    });
    
    $("#create-type").click(function() {
    console.log("Hola");
    });

});


</script>

</head>
<body id="dt_example" class="ex_highlight_row">

<?php
    require('templates/dialogs.tpl.php');
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

        <p class="titulos-form" style="float:left;"><?php echo $titulo; ?></p>
        <input type="text" name="new_type_label" style="margin-left: 30%; margin-top: 10px;" />
        <button id="create-type">Añadir</button>
        
        <!--<div class="new-type" >
            Nueva Materia
            <input type="text" name="new_type_label" />
            <button id="create-type">Crear</button>
            <br />
        </div>-->
        
        <?php 
        if (isset($error_flag)){
            if(strlen($error_flag) > 0)
                echo $error_flag;
        }
            
        ?>

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
                            <th>MATERIA</th>
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
                        <td><input id="type_id" type="hidden" name="type_id" value="" /></td>
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