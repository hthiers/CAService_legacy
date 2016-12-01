<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>

<!-- AGREGAR JS & CSS AQUI -->
<link rel="stylesheet" href="views/css/datatable.css">
<link rel="stylesheet" href="views/css/dataTables.tableTools.min.css">
<link rel="stylesheet" href="views/css/select2.css">
<style type="text/css" title="currentStyle">
    table.dataTable, table.filtres {
        width: 500px;
    }
    #central { width: 60%;}
    #new_management_label {
        -webkit-box-sizing: border-box; /* webkit */
        -moz-box-sizing: border-box; /* firefox */
        box-sizing: border-box; /* css3 */
        
        border: 1px solid #aaa;
        border-radius: 4px;
        
        line-height: 26px;
        vertical-align: middle;
    }
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/utils.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery.jeditable.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/select2.js"></script>
<script type="text/javascript">

var oTable = null;
    
function submitToForm(){
    $('#action_management').val("view");

    return false;
}

function removeManagement(management){
    var urlAction = "<?php echo "?controller=managements&action=managementsRemove";?>";
    
    $( "#dialog-remove" ).dialog({
            height: 200,
            width: 350,
            modal: true,
            buttons: {
            "Eliminar": function() {
                console.log("borrando: #"+management);
                
                $('#dt_form').attr('action', urlAction);
                $('#dt_form').attr('method', 'POST');
                $('#management_id').val(management);

                $( this ).dialog( "close" );
                $("#dt_form").submit();
            },
            "Cancelar": function() {
              $( this ).dialog( "close" );
            }
          }
    });
    
    $("#dialog-remove")
            .data('management_id', management)
            .dialog("open");
}
    
function hideErrorBox(){
    $("#errorbox_success").fadeToggle( "slow", "linear" );
    $("#errorbox_failure").fadeToggle( "slow", "linear" );
}
    
$(document).ready(function() {
    //Hide errorbox
    setTimeout(function() {
        hideErrorBox();
    }, 2000);
    
    var options = "";
    
    /*
    $('.editcustomer_select').select2({
        placeholder: {
            id: "",
            text: ""},
            allowClear:true
        });
    */
    
    $.ajax({
              type: "POST",
              url: "?controller=customers&action=getCustomersByTenant",
              dataType: "json",
              success: function(data) {
                //tareas = data;
                //tareas = $.parseJSON(data);
                
                $.each(data , function( index, obj ) {
                    $.each(obj, function( key, value ) {
                        
                        if(key == 'id_customer') {
                            options += '<option value="'+value+'">';
                            //console.log(value);
                        }
                        
                        if(key == 'label_customer') {
                            options += value +'</option>';
                            //console.log(value);
                        }
                    });
                });
                
              },
              error: function(jqXHR, textStatus, errorThrown) {
                
                alert("Error al ejecutar =&gt; " + textStatus + " - " + errorThrown);
              }
        });
    
    oTable = $('#table').dataTable({
        //Initial server side params
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": <?php echo "'?controller=managements&action=ajaxManagementsDt'";?>,
        "fnServerData": function ( sSource, aoData, fnDrawCallback ){
            $.ajax({
                "dataType": 'json', 
                "type": "GET", 
                "url": sSource, 
                "data": aoData, 
                "success": fnDrawCallback
            });
        },
        
        "sDom": '<"top"lpfi>rt<"clear">',
        
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
        
        "aoColumnDefs": [
            { "sClass": "td_options", "aTargets": [-1] },
            { "sClass": "td_editable", "aTargets": [3] },
            { "sClass": "editcustomer_select", "aTargets": [ 5 ] },
            { "mDataProp": null, "aTargets": [-1] },
            { "bVisible": false, "aTargets": [0,1,2,4] },
            { "sWidth": "40%", "aTargets": [3] },
            { "sWidth": "40%", "aTargets": [5] },
            { "sWidth": "20%", "aTargets": [-1] },
            {
                "fnRender": function ( oObj ) {                    
                    var dt_tools = "";                
                    dt_tools = dt_tools+"<input style=\'width:22px;height:22px;display:inline;\' type='button' id=\'tool_remove\' class=\'ui-icon ui-icon-trash\' title=\'Borrar\' name='"+oObj.aData[0]+"' onclick='removeManagement("+oObj.aData[0]+")' value='' />";

                    return dt_tools;
                },
                "aTargets": [-1]
            }
        ],
        
        "sPaginationType": "full_numbers",
        "aaSorting": [[3, "asc"]],

        "fnDrawCallback": function () {
            $('#table tbody td:.td_editable').editable( '?controller=managements&action=ajaxManagementsUpdate', {
                
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
                        "column": oTable.fnGetPosition( this )[2],
                    };
                },
                
                "placeholder" : "",
                "height": "14px"
            } );
            
            $('#table tbody td:.editcustomer_select').editable( '?controller=managements&action=ajaxUpdateManagement', {
                "callback"      : function( sValue, y ) {
                        var aPos = oTable.fnGetPosition(this);
                        oTable.fnDraw();
                        //oTable.fnUpdate( sValue, aPos[0], aPos[1] );
                },
                "submitdata"   : function (value, settings) {
                        var aPos = oTable.fnGetPosition( this );    
                        var aData = oTable.fnSettings().aoData[ aPos[0] ]._aData;
                        return {idManagement: aData[0], column: 3, newvalue: aData[5]}; //take idData from first column
                },
                indicator : "Saving...",
                tooltip   : "Click to change...",
                loaddata  : function(value, settings) {
                        var aPos = oTable.fnGetPosition( this );    
                        var aData = oTable.fnSettings().aoData[ aPos[0] ]._aData;
                        return {current: value}
                },
                loadurl   : "?controller=customers&action=getCustomersByTenantJSON",
                type      : "select",
                submit    : "OK",
                height    : "14px"
            });
            
            
        }
    });
    
    $('#cbocustomers').select2({
       allowClear:true,
       theme: "classic"
   });
    
    // boton nueva gestion
    $("#create-management").click(function() {
    
        guardarGestion();
    });
    
    //Add Class to search field
    //$(".dataTables_filter input").addClass("search_input");
    
});


function guardarGestion() {
    var label = $('#new_management_label').val();
    var customer = $('#cbocustomers').val();
    
    //alert("cliente: "+customer);
    $.ajax(
            {
                type: "POST",
                url: "?controller=managements&action=ajaxManagementsAddWithCustomer",
                data: { label_management: label, id_customer: customer },
                cache: false,
                //contentType: "application/json; charset=utf-8",
                dataType: "json"
            }).done(function(response){
                
        if(response !== null){
            console.log(response);
            oTable.fnDraw();
        }
        else{
            console.log("response null");
        }
        }).fail(function(jqXHR, textStatus){
            console.log(textStatus);
    });
}


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
        
        <input 
            type="text" 
            id="new_management_label" 
            name="new_management_label" 
            style="margin-left: 20%;"
            placeholder="Nueva gestión..." />
        <select 
            class="js-example-responsive" 
            style="width:20%" 
            id="cbocustomers" 
            name="cbocustomers">
            <?php
            while($row = $listadoClientes->fetch(PDO::FETCH_ASSOC))
            {
                echo "<option value='$row[id_customer]'>$row[label_customer]</option>\n";
            }
            ?>
        </select>
        &nbsp;
        <input type="button" id="create-management" style="width:22px;height:22px;display:inline;" class="ui-icon ui-icon-circle-plus" />
        
        <!--<div class="new-management" >
            Nueva Gestion
            <input type="text" name="new_management_label" />
            <button id="create-management">Crear</button>
            <br />
        </div>-->
        
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
            <form id="dt_form" method="POST" action="<?php echo "?controller=managements&amp;action=ajaxManagementsDt";?>">
                <table class="display" id="table">
                    <thead>
                        <tr class="headers">
                            <th>ID</th>
                            <th>CODIGO GESTION  </th>
                            <th>TENANT</th>
                            <th>GESTION</th>
                            <th>IDCLIENTE</th>
                            <th>CLIENTE</th>
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
                        <td><input id="action_management" type="hidden" name="action_management" value="" /></td>
                    </tr>
                </table>
                <table style="float:left"> <!-- style float solo para perderlo -->
                    <tr>
                        <td><input id="management_id" type="hidden" name="management_id" value="" /></td>
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