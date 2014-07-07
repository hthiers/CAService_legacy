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
<style type="text/css" title="currentStyle">
    table.dataTable, table.filtres {
        width: 100%;
    }
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery-tableTools.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/utils.js"></script>
<script type="text/javascript">
function stopTask(){
//    sin efecto....
//    console.log("boton terminar ok");
//    console.log("boton val: "+$(this).attr("value"));

    return false;
}
    
$(document).ready(function() {
    var oTable = $('#example').dataTable({
        //Initial server side params
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": '?controller=tasks&action=ajaxTasksDt',
        "fnServerData": function ( sSource, aoData, fnDrawCallback ){
            $.ajax({
                "dataType": 'json', 
                "type": "GET", 
                "url": sSource, 
                "data": aoData, 
                "success": fnDrawCallback
            });
        },
        
        "sDom": 'T<"top"lpf>rt<"clear">',
        
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
                
        "oTableTools": {
            "sSwfPath": "views/media/swf/copy_csv_xls_pdf.swf",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "mColumns": [0,1,2,3,4,5],
                    "sFileName": "Control de Trabajos.xls"
                },
                {
                    "sExtends": "pdf",
                    "mColumns": [0,1,2,3,4,5]
                },
//                {
//                    "sExtends": "copy",
//                    "mColumns": "visible",
//                    "fnClick": function ( nButton, oConfig, flash ) {
//                        console.log(this.fnGetTableData(oConfig));
//                        
//                        var exp_data = this.fnGetTableData(oConfig);
//                        var array_data = exp_data.split();
//                        
//                        this.fnSetText( flash, this.fnGetTableData(oConfig) );
//                    }
//                }
            ]
        },
        
        //Custom filters params
        "fnServerParams": function ( aoData ){
            aoData.push(
                { "name": "filCliente", "value": $('#cboCliente').val() },
                { "name": "filMes", "value": $('#cboMes').val() }
//                { "name": "filEstado", "value": $('#cboEstado').val() }
            );
        },
        
        "aoColumnDefs": [
            {
                "sClass": "td_options", "aTargets": [-1]
            },
            {
                "sWidth": "10%", "aTargets": [0,1,4,5,-1]
            },
            {
                "sWidth": "20%", "aTargets": [2]
            },
            { "mDataProp": null, "aTargets": [-1] },
            { "bVisible": false, "aTargets": [6,7,8,9,10] },
            {
                "fnRender": function ( oObj ) {
                    if(oObj.aData[0] !== null){
                        var db_date = oObj.aData[0];
//                        var date = new Date(db_date);
//                        //console.log("fecha: "+date);
//                        
//                        var day = date.getDate();
//                        var month = date.getMonth()+1;
//                        var year = date.getFullYear();
//                        var hours = date.getHours();
//                        var minutes = date.getMinutes();
//                        var seconds = date.getSeconds();
//                        var string_date = day+"/"+month+"/"+year+" "+hours+":"+minutes+":"+seconds;

                        var string_date = formatDateTimeString(db_date);
                        
                        return string_date;
                    }
                    else{
                        return '';
                    }
                },
                "aTargets": [0]
            },
            {
                "fnRender": function ( oObj ) {
                    if(oObj.aData[1] !== null){
                        var db_date = oObj.aData[1];
//                        var date = new Date(db_date);
//                        //console.log("fecha: "+date);
//                        
//                        var day = date.getDate();
//                        var month = date.getMonth()+1;
//                        var year = date.getFullYear();
//                        var hours = date.getHours();
//                        var minutes = date.getMinutes();
//                        var seconds = date.getSeconds();
//                        var string_date = day+"/"+month+"/"+year+" "+hours+":"+minutes+":"+seconds;

                        var string_date = formatDateTimeString(db_date);
                        
                        return string_date;
                    }
                    else{
                        return '';
                    }
                },
                "aTargets": [1]
            },
//            {
//                "fnRender": function ( oObj ) {
//                    if(oObj.aData[5] !== null){
//                        var db_date = oObj.aData[5];
//                        var date = new Date(db_date);
//                        //console.log("tiempo: "+date);
//                        
//                        var hours = date.getHours();
//                        var minutes = date.getMinutes();
//                        var string_time = hours+":"+minutes;
//                        
//                        return string_time;
//                    }
//                    else{
//                        return '';
//                    }
//                },
//                "aTargets": [2]
//            },
//            {
//                "fnRender": function ( oObj ) {
//                    if(oObj.aData[0] !== null){
//                        var data_string = oObj.aData[0];
//                        //var date = new Date(db_date);
//                        //console.log("tiempo: "+date);
//                        
//                        //var hours = date.getHours();
//                        //var minutes = date.getMinutes();
//                        //var string_time = hours+":"+minutes;
//                        
//                        return data_string;
//                    }
//                    else{
//                        return '';
//                    }
//                },
//                "aTargets": [3]
//            },
            {
                "fnRender": function ( oObj ) {
                    if(oObj.aData[5] !== null){
                        var seconds = oObj.aData[5];
                        var total = secondsToTime(seconds);

                        return total['h']+':'+total['m']+':'+total['s'];
                    }
                    else{
                        return '';
                    }
                },
                "aTargets": [5]
            },
            {
                "fnRender": function ( oObj ) {
//                    console.log(oObj);
                
                    if(oObj.aData[1] === null || oObj.aData[1] === ""){
                        return '<button id=\"button\" class=\"input\" name=\"id_task\" onclick=\"stopTask()\" value="'+oObj.aData[6]+'">VER</button>';
                    }
                    else{
//                        console.log(oObj);
                        return '';
                    }
                },
                "aTargets": [-1]
            },
        ],
        
        "sPaginationType": "full_numbers",
        "aaSorting": [[0, "asc"]]
    });
    
    $('#cboCliente').change(function() { oTable.fnDraw(); } );
    $('#cboMes').change(function() { oTable.fnDraw(); } );
//    $('#cboEstado').change(function() { oTable.fnDraw(); } );
    
    // form submition handling
    $('#dt_form').submit( function() {
        var sData = oTable.$('input').serialize();
        var actionType = $('#action_type').val();
        var urlAction = "";
        
        if(actionType == 'edit_form'){
            urlAction = "<?php echo "?controller=".$controller."&amp;action=".$action;?>";
            $('#action_type').val("");
            
            return true;
        }
    });
    
    ahora = new Date();
    ahoraDay = ahora.getDate();
    ahoraMonth = ahora.getMonth();
    ahoraYear = ahora.getYear();
    
//    console.log("mes: "+ahoraMonth);
});

/*
* Getting needed value from dt row
 */
function fnFormatDetails (oTable, nTr){
    var aData = oTable.fnGetData( nTr );
    return aData[6];
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
            print_r($arrayDates);print('<br />');
            print_r($clientes);print('<br />');
            #print_r($permiso_editar); print('<br />');
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <p class="titulos-form"><?php echo $titulo; ?></p>

        <!--
        <p style="font-size: 12px; color: #999;">
            Nota: Esta pantalla permitir&iacute;a gestionar todos los registros existentes en el sistema, en principio, solo para el usuario en sesi&oacute;n. 
            Una barra azul en la cabecera de la p&aacute;gina muestra diferentes opciones de men&uacute;. En este caso solo funcionan como v&iacute;nculos 
            el item de "TRABAJOS" y "NUEVO TRABAJO".
            <br />
            Sobre la tabla de abajo se encuentran los filtros de informaciÃ³n en la tabla.
            Un bot&oacute;n de exportar permitir&iacute;a crear un documento Excel con todos los trabajos en vista.
            Una columna de opciones permitir&iacute;a ejecutar ciertas acciones sobre un trabajo, en este caso se encuentra un v&iacute;nculo "ver"
            para abrir un registro.
            Haciendo clic en las cabeceras de la tabla es posible cambiar el orden por columna.
        </p>
        -->
        
        <?php 
        if (isset($error_flag)){
            if(strlen($error_flag) > 0)
                echo $error_flag;
        }
        ?>
         <!--CUSTOM FILTROS--> 
        
        <div id="dt_filtres" style="float:left;margin-top:10px;">
            <label style="float:none;">Mes: </label>
            <select id="cboMes">
                <?php
                for ($i=0; $i<sizeof($arrayDates); $i++){
                    if($i == date("m"))
                        echo "<option selected value='$i'>". $arrayDates[$i] . "</option>";
                    else
                        echo "<option value='$i'>". $arrayDates[$i] . "</option>"; 
                    
                }
                ?>
            </select>
            
            <label style="float:none;">Cliente: </label>
            <select id="cboCliente">
                <?php
                echo "<option value=''></option>";
                for ($i=0; $i<sizeof($clientes); $i++){
                        echo "<option value=".$clientes[$i][0].">". $clientes[$i][3] . "</option>";
                }
                ?>
            </select>
        </div>
        
         <!--END CUSTOM FILTROS--> 
        
        <!-- DATATABLE -->
        <div id="dynamic">
            <form id="dt_form" method="POST" action="<?php echo "?controller=".$controller."&amp;action=".$action;?>">
                <table class="display" id="example">
                    <thead>
                        <tr class="headers">
                            <th>INICIO</th>
                            <th>FIN</th>
                            <th>CLIENTE</th>
                            <th>GESTION</th>
                            <th>RESPONSABLE</th>
                            <!--<th>PROYECTO</th>-->
<!--                            <th>INICIO_ORIGEN</th>
                            <th>FIN_ORIGEN</th>-->
                            <th>TIEMPO</th>
                            <th>ID TASK</th>
                            <th>ID TENANT</th>
                            <th>ID PROJECT</th>
                            <th>ID CUSTOMER</th>
                            <th>ID USER</th>
                            <th>OPCIONES</th>
                            <!--<th>OTRA COL</th>-->
                            <!--<th>OTRA COL 2</th>-->
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