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

TableTools.BUTTONS.download = {
    "sAction": "text",
    "sTag": "default",
    "sFieldBoundary": "",
    "sFieldSeperator": "\t",
    "sNewLine": "<br>",
    "sToolTip": "",
    "sButtonClass": "DTTT_button_text",
    "sButtonClassHover": "DTTT_button_text_hover",
    "sButtonText": "Download",
    "mColumns": "all",
    "bHeader": true,
    "bFooter": true,
    "sDiv": "",
    "fnMouseover": null,
    "fnMouseout": null,
    "fnClick": function( nButton, oConfig ) {
        var oParams = this.s.dt.oApi._fnAjaxParameters( this.s.dt );

        oParams.push(
            { "name": "filAnio", "value": $('#cboAnio').val() },
            { "name": "filCliente", "value": $('#cboCliente').val() },
            { "name": "filMes", "value": $('#cboMes').val() },
            { "name": "filType", "value": $('#cboType').val() },
            { "name": "filEstado", "value": $('#cboEstado').val() },
            { "name": "filUser", "value": $('#cboUser').val() }
        );

//        console.log(oParams);

        /* Create an IFrame to do the request */
        var nIFrame = document.createElement('iframe');
        nIFrame.setAttribute( 'id', 'RemotingIFrame' );
        nIFrame.style.border='0px';
        nIFrame.style.width='0px';
        nIFrame.style.height='0px';
        nIFrame.src = oConfig.sUrl+"&"+$.param(oParams);
        document.body.appendChild( nIFrame );
    },
    "fnSelect": null,
    "fnComplete": null,
    "fnInit": null
};

/*
* Getting needed value from dt row
 */
function fnFormatDetails (oTable, nTr){
    var aData = oTable.fnGetData( nTr );
    return aData[6];
}

function viewTask(task){
    console.log(task);

    var urlAction = "<?php echo "?controller=".$controller."&action=tasksView";?>";
    
    $('#dt_form').attr('action', urlAction);
    $('#dt_form').attr('method', 'POST');
    $('#task_id').val(task);
    
    $("#dt_form").submit();
}

function editTask(task){
    console.log(task);
    
    var urlAction = "<?php echo "?controller=".$controller."&action=tasksEditForm";?>";
    
    $('#dt_form').attr('action', urlAction);
    $('#dt_form').attr('method', 'POST');
    $('#task_id').val(task);
    
    $("#dt_form").submit();
}
    
$(document).ready(function() {
    var oTable = $('#example').dataTable({
        //Initial server side params
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": '?controller=tasks&action=ajaxTasksDt',
        "fnServerData": function ( sSource, aoData, fnDrawCallback ){
            $.getJSON(sSource, aoData, function(json) {
                var total_seconds = json.iTotalTime;
                var total_time = secondsToTime(total_seconds);
                
                $("#footer p").text('Tiempo total: '+total_time['h']+':'+total_time['m']+':'+total_time['s']);
                
                fnDrawCallback(json);
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
                    "sExtends": "download",
                    "sButtonText": "Excel",
                    "sUrl": "?controller=tasks&action=ajaxBuildXls"
                }
            ]
        },
        
        //Custom filters params
        "fnServerParams": function ( aoData ){
            aoData.push(
		{ "name": "filAnio", "value": $('#cboAnio').val() },                
		{ "name": "filCliente", "value": $('#cboCliente').val() },
                { "name": "filMes", "value": $('#cboMes').val() },
                { "name": "filType", "value": $('#cboType').val() },
                { "name": "filEstado", "value": $('#cboEstado').val() },
                { "name": "filUser", "value": $('#cboUser').val() }
            );
        },
        
        "aoColumnDefs": [
            {
                "sClass": "td_options", "aTargets": [-1]
            },
            {
                "sWidth": "10%", "aTargets": [0,1,4,5,6,-1]
            },
            {
                "sWidth": "20%", "aTargets": [2]
            },
            { "mDataProp": null, "aTargets": [-1] },
            { "bVisible": false, "aTargets": [7,8,9,10,11] },
            {
                "fnRender": function ( oObj ) {
                    if(oObj.aData[0] !== null){
                        var db_date = oObj.aData[0];
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
                        var string_date = formatDateTimeString(db_date);
                        
                        return string_date;
                    }
                    else{
                        return '';
                    }
                },
                "aTargets": [1]
            },
            {
                "fnRender": function ( oObj ) {
                    if(oObj.aData[6] !== null){
                        var seconds = oObj.aData[6];
                        var total = secondsToTime(seconds);

                        return total['h']+':'+total['m']+':'+total['s'];
                    }
                    else{
                        return '';
                    }
                },
                "aTargets": [6]
            },
            {
                "fnRender": function ( oObj ) {
                    //console.log(oObj.aData[1]);
                
                    if(oObj.aData[1] === null || oObj.aData[1] === ""){
                        return "<input type='button' id=\'btn_view\' class=\'input\' name='"+oObj.aData[7]+"' onclick='viewTask("+oObj.aData[7]+")' value='VER' />";
                    }
                    else if(oObj.aData[1] !== null && oObj.aData[1] !== ""){
//                        console.log(oObj);
                        return "<input type='button' id=\'btn_edit\' class=\'input\' name='"+oObj.aData[7]+"' onclick='editTask("+oObj.aData[7]+")' value='EDITAR' />";
                    }
                    else {
                        return '';
                    }
                },
                "aTargets": [-1]
            }
        ],
        
        "sPaginationType": "full_numbers",
        "aaSorting": [[0, "asc"]]
    });
     
    ahora = new Date();
    ahoraDay = ahora.getDate();
    ahoraMonth = ahora.getMonth();
    ahoraYear = ahora.getYear();

    // año
    var dteNow = new Date();
    var intYear = dteNow.getFullYear();
    /*
    $('#cboAnio')
         .append($("<option selected></option>")
         .attr("value", "")
         .text(intYear));
    $('#cboAnio')
         .append($("<option></option>")
         .attr("value",intYear-1)
         .text(intYear-1)); 
    */

    // listeners de filtros para dataTable
    $('#cboAnio').change(function() { oTable.fnDraw(); } );
    $('#cboCliente').change(function() { oTable.fnDraw(); } );
    $('#cboMes').change(function() { oTable.fnDraw(); } );
    $('#cboType').change(function() { oTable.fnDraw(); } );
    $('#cboEstado').change(function() { oTable.fnDraw(); } );
    $('#cboUser').change(function() { oTable.fnDraw(); } );
});
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
            print_r($types);print('<br />');
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
        ?>
         <!--CUSTOM FILTROS--> 
        
        <div id="dt_filtres" style="float:left;margin-top:10px;">
	    <label style="float:none;">Año: </label>
            <select id="cboAnio">
                <?php
                echo "<option selected value=".date('Y').">". date('Y') ."</option>";
                echo "<option value=".date('Y',strtotime('-1 year')).">". date('Y',strtotime('-1 year')) ."</option>";
                ?>
            </select>

            <label style="float:none;">Mes: </label>
            <select id="cboMes">
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
            
            <label style="float:none;">Estado: </label>
            <select id="cboEstado">
                <?php
                echo "<option selected value=''>Todos</option>";
                echo "<option value=1>En curso</option>";
                echo "<option value=2>Terminado</option>";
                ?>
            </select>
            
            <label style="float:none;">Cliente: </label>
            <select id="cboCliente">
                <?php
                echo "<option selected value=''>Todos</option>";
                for ($i=0; $i<sizeof($clientes); $i++){
                        echo "<option value=".$clientes[$i][0].">". $clientes[$i][3] . "</option>";
                }
                ?>
            </select>
            
            <label style="float:none;">Materia: </label>
            <select id="cboType">
                <?php
                echo "<option selected value=''>Todas</option>";
                for ($i=0; $i<sizeof($types); $i++){
                        echo "<option value=".$types[$i][0].">". $types[$i][2] . "</option>";
                }
                ?>
            </select>
            
            <label style="float:none;">Responsable: </label>
            <select id="cboUser">
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
        </div>
        <!--END CUSTOM FILTROS--> 
         
        <!-- DATATABLE -->
        <div id="dynamic">
            <form id="dt_form" method="" action="">
                <table class="display" id="example">
                    <thead>
                        <tr class="headers">
                            <th>INICIO</th>
                            <th>FIN</th>
                            <th>CLIENTE</th>
                            <th>GESTION</th>
                            <th>MATERIA</th>
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
                            <td colspan="11" class="dataTables_empty">Procesando...</td>
                        </tr>
                    </tbody>
                </table>
                <table style="float:left"> <!-- style float solo para perderlo -->
                    <tr>
                        <td><input id="task_id" type="hidden" name="task_id" value="" /></td>
                    </tr>
                </table>
            </form>
        </div>

        <div id="footer" class="headers" style="color:#ffffff;padding:3px;">
            <p style="text-align:right;">Tiempo Total: </p>
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
