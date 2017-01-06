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
            { "name": "filDia", "value": $('#cboDia').val() },
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

function removeTask(task){
    var urlAction = "<?php echo "?controller=".$controller."&action=tasksRemove";?>";

    /*
    $( "#dialog-remove" ).dialog({
            height: 200,
            width: 350,
            modal: true,
            buttons: {
            "Eliminar": function() {
                console.log("borrando: #"+task);

                $('#dt_form').attr('action', urlAction);
                $('#dt_form').attr('method', 'POST');
                $('#task_id').val(task);

                $( this ).dialog( "close" );
                $("#dt_form").submit();
            },
            "Cancelar": function() {
              $( this ).dialog( "close" );
            }
          }
    });

    $("#dialog-remove")
            .data('task_id', task)
            .dialog("open");
    */
    $('#task_id').val(task);
   $('#modalEliminar').foundation('open');
}

function hideErrorBox(){
    $("#errorbox_success").fadeToggle( "slow", "linear" );
    $("#errorbox_failure").fadeToggle( "slow", "linear" );
}

$(document).ready(function() {

    var s_id_user = null;
    var s_id_profile = null;

    //Hide errorbox
    setTimeout(function() {
        hideErrorBox();
    }, 2000);

    var oTable = $('#example').dataTable({
        //Initial server side params
        "bProcessing": true,
        "bServerSide": true,
        "bAutoWidth": false,
        "sAjaxSource": '?controller=tasks&action=ajaxTasksDt',
        "fnServerData": function ( sSource, aoData, fnDrawCallback ){
            $.getJSON(sSource, aoData, function(json) {
                var total_seconds = json.iTotalTime;
                var total_time = secondsToTime(total_seconds);
                var total_records = json.iTotalDisplayRecords;
                s_id_user = json.iIdUser;
                s_id_profile = json.iIdProfile;

                var strFooter = "<span class='fi-list icon-indicator'></span> "+total_records+" ";
                strFooter += "<span class='fi-clock icon-indicator'></span> "+total_time['h']+":"+total_time['m']+":"+total_time['s'];


                $("#footer p").html(strFooter);

                fnDrawCallback(json);
            });
        },

        "sDom": 'T<"top"lpfi>rt<"clear">',

        "oLanguage": {
            "sInfo": "_TOTAL_ registros",
            "sInfoEmpty": "0 registros",
            "sInfoFiltered": "(de _MAX_ registros)",
            "sLengthMenu": "_MENU_ por p&aacute;gina",
            "sZeroRecords": "No hay registros",
            "sInfo": "_START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 registros",
            "sSearch": "Buscar",
            "sProcessing": "",
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
                { "name": "filDia", "value": $('#cboDia').val() },
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
                    var dt_tools = "";

                    if(s_id_profile > 1){
                        //mostrar opciones solo para tareas propias

                        if(s_id_user === oObj.aData[11]){
                            if(oObj.aData[1] === null || oObj.aData[1] === ""){
                                dt_tools = dt_tools+"<a href=\'javascript:void(0)\' id=\'btn_view\' class=\'icon-action fi-page-edit\' name='"+oObj.aData[7]+"' onclick='viewTask("+oObj.aData[7]+")'></a> &nbsp;";
                            }

                            dt_tools = dt_tools+"<a href=\'javascript:void(0)\' id=\'tool_remove\' class=\'icon-action fi-trash\' name='"+oObj.aData[7]+"' onclick='removeTask("+oObj.aData[7]+")'></a>";
                        }
                    }
                    else{
                        //mostrar opciones para las tareas de todos los usuarios

                        if(oObj.aData[1] === null || oObj.aData[1] === ""){
                            dt_tools = dt_tools+"<a href=\'javascript:void(0)\' id=\'btn_view\' class=\'icon-action fi-page-edit\' name='"+oObj.aData[7]+"' onclick='viewTask("+oObj.aData[7]+")'></a> &nbsp;";
                        }

                        dt_tools = dt_tools+"<a href=\'javascript:void(0)\' id=\'tool_remove\' class=\'icon-action fi-trash\' name='"+oObj.aData[7]+"' onclick='removeTask("+oObj.aData[7]+")'></a>";
                    }

                    return dt_tools;
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

    // listeners de filtros para dataTable
    $('#cboAnio').change(function() { oTable.fnDraw(); } );
    $('#cboCliente').change(function() { oTable.fnDraw(); } );
    $('#cboMes').change(function() { oTable.fnDraw(); } );

    $('#cboDia').change(function() { oTable.fnDraw(); } );

    $('#cboType').change(function() { oTable.fnDraw(); } );
    $('#cboEstado').change(function() { oTable.fnDraw(); } );
    $('#cboUser').change(function() { oTable.fnDraw(); } );

    getLastDay('cboMes', 'cboAnio', 'cboDia');


    $('#cboCliente').select2({
        placeholder: {
            id: "",
            text: "Todos"},

        allowClear:true,
        theme: "classic"
    });
    $('#cboType').select2({
        placeholder: {
            id: "",
            text: "Todos"},

        allowClear:true,
        theme: "classic"
    });
    $('#cboUser').select2({
        allowClear:true,
        theme: "classic"
    });
    $('#cboAnio').select2({
        allowClear:true,
        theme: "classic"
    });
    $('#cboMes').select2({
        allowClear:true,
        theme: "classic"
    });
    $('#cboDia').select2({
        allowClear:true,
        theme: "classic"
    });
    $('#cboEstado').select2({
        allowClear:true,
        theme: "classic"
    });
    
    $('#confirmarEliminar').click(function() {
        
        var urlAction = "<?php echo "?controller=tasks&action=tasksRemove";?>";
        
        //console.log("borrando: #"+$('#task_id').val());

        $('#dt_form').attr('action', urlAction);
        $('#dt_form').attr('method', 'POST');
        //$('#task_id').val(task);

        //$( this ).dialog( "close" );
        $("#dt_form").submit();
    });
    
    $('#cancelarEliminar').click(function() {
        
        $('#modalEliminar').foundation('close');
    });
});

</script>
