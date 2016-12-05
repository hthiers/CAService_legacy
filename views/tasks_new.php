<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>

<!-- AGREGAR JS & CSS AQUI -->
<link rel="stylesheet" href="views/css/select2.css">
<style type="text/css" title="currentStyle">
    .table_left {
        margin-bottom: 10px;
    }
    .table_right {
        margin-left: 120px;
    }
    td.middle {
        padding-bottom: 15px;
        text-align: left;
    }
    input.input_box, textarea.input_box, select.input_box{
        border: 1px solid #989898;
        border-radius: 4px
    }
    #dt_filtres table {
        float: left;
    }
    #dt_filtres input, #dt_filtres textarea, #dt_filtres select {
        margin-left: 5px;
        width: 155px;
        height: 20px;
    }
    #dt_filtres input.time_control {
        width: 80px;
        height: 30px;
    }
    #dt_filtres input.time_status {
        margin-top: 10px;
        height: 30px;
        width: 250px;
    }
    #dt_filtres textarea{
        width: 300px;
        height: 100px;
    }
    #dt_filtres td {
        text-align: left;
    }
    #dt_filtres {
        padding: 10px;
        /*height: 200px;*/
    }
    #btn_stop {
        border: 1px solid #989898;
        border-radius: 4px;
        background-color: orangered;
    }
    #btn_stop:active {
        background-color: brown;
    }
    #datepicker {
        margin-left: 5px;
    }
    .select2-container, .select2-drop, .select2-search, .select2-search input {
        width: 100%;
    }
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/utils.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery.timepicker.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/select2.js"></script>
<script type="text/javascript">
    // JQDialog window
    var windowSizeArray = [ "width=200,height=200","width=300,height=400,scrollbars=yes" ];
    
    $(document).ready(function(){
        //var myDate = new Date();
        //var displayDate = myDate.getFullYear() + '/' + (myDate.getMonth()+1) + '/' + (myDate.getDate());
        //var outStr = myDate.getHours()+':'+myDate.getMinutes()
        
        // Btn play
        $("#btn_play").click(function (event){
            iniTrabajo();
        });
        
        var tareas = new Array();
        
        $.ajax({
              type: "POST",
              url: "?controller=tasks&action=getTasksName",
              dataType: "json",
              success: function(data) {
                //tareas = data;
                //tareas = $.parseJSON(data);
                $.each(data , function( index, obj ) {
                    $.each(obj, function( key, value ) {
                        tareas.push(value);
                    });
                });
                $("#gestion").autocomplete({
                    source: tareas
                });
              },
              error: function(jqXHR, textStatus, errorThrown) {
                
                alert("Error al ejecutar =&gt; " + textStatus + " - " + errorThrown);
              }
        });
        
        $("#cbocustomers").change(function(e) {
            if ($(this).val().trim() !== "") {
                console.log("cambia customer");
                
                $("#cbomanagements").empty();
                
                if ($(this).val().trim() !== "noaplica") {
                    ejecutar($(this), $("#cbomanagements"));
                    $("#cbomanagements").val("noaplica").trigger("change");
                }
            }
          });
       
        function ejecutar(obj1, obj2) {
        
            var idCustomer = $(obj1).val();

            console.log(idCustomer);

            $.ajax({
              type: "POST",
              url: "?controller=managements&action=ajaxGetManagementsByCustomer",
              dataType: "html",
              data: { id_customer : idCustomer},
              success: function(msg) {
                $(obj2).html(msg).attr("disabled", false);
                
                $('#cbomanagements').select2({
                    placeholder: {
                        id: "",
                        text: "Ingrese Gestión"},
                    allowClear:true
                });
                
              },
              error: function(jqXHR, textStatus, errorThrown) {
                $(obj1).next('img').remove();
                alert("Error al ejecutar =&gt; " + textStatus + " - " + errorThrown);
              }
            });
        }
                
        // JQDialog Submit - Add new customer
        $(".dlgSbmCstr").click(function(){
            var name = $("#dlgSbm_name_customer").val();
            var desc = $("#dlgSbm_desc_customer").val();
            //var dataString = 'name='+ name + '&desc=' + desc;
            if(name === '')
            {
                alert("Ingrese título del cliente");
            }
            else
            {
                //$("#flash").show();
                //$("#flash").fadeIn(400).html('<img src="ajax-loader.gif" align="absmiddle"> loading.....');
                $.ajax({
                    type: "POST",
                    url: "?controller=customers&action=ajaxCustomersAdd",
                    data: {name:name, desc:desc},
                    cache: false,
                    dataType: "json"
                }).done(function(response){
                    if(response !== null){
                        if(response[0] !== 0){
                            $("#cbocustomers").append('<option value="'+response[0]+'" selected="selected">'+response[1]+'</option>');       
                            //$("#flash").hide();
                            alert("Cliente agregado!");
                        }
                        else
                            alert("Error: "+response[1]);
                    }
                    else{
                        alert("Ha ocurrido un error! (nulo)");
                    }
                    $("#dialog-new-customer").dialog("close");
                }).fail(function(){
                    alert("Ha ocurrido un error!");
                });
            }

            return false;
	});


        // JQDialog Submit - Add new type
        $(".dlgSbmCstr_type").click(function(){
            var customer = $("#cbocustomers").val();
            var label_type = $("#dlgSbm_name_type").val();
            //var dataString = 'name='+ name + '&desc=' + desc;
            if(label_type === '')
            {
                alert("Ingrese nombre de la materia");
            }
            else
            {
                //$("#flash").show();
                //$("#flash").fadeIn(400).html('<img src="ajax-loader.gif" align="absmiddle"> loading.....');
                $.ajax({
                    type: "POST",
                    url: "?controller=types&action=ajaxTypesAddWithCustomer",
                    data: {label_type:label_type, id_customer: customer},
                    cache: false,
                    dataType: "json"
                }).done(function(response){
                    if(response !== null){
                        if(response[0] !== 0){
                            $("#cbotypes").append('<option value="'+response[0]+'" selected="selected">'+response[1]+'</option>');       
                            //$("#flash").hide();
                            alert("Materia agregada!");
                        }
                        else
                            alert("Error: "+response[1]);
                    }
                    else{
                        alert("Ha ocurrido un error! (nulo)");
                    }
                    $("#dialog-new-type").dialog("close");
                }).fail(function(){
                    alert("Ha ocurrido un error!");
                });
            }

            return false;
	});
        
        $(".dlgSbmErr_type").click(function(){
            $("#dialog-error-add-type").dialog("close");
	});
        
        // JQDialog Submit - Add new type
        $(".dlgSbmCstr_management").click(function(){
            var label_management = $("#dlgSbm_name_management").val();
            //var dataString = 'name='+ name + '&desc=' + desc;
            if(label_management === '')
            {
                alert("Ingrese nombre de la gestión");
            }
            else
            {
                //$("#flash").show();
                //$("#flash").fadeIn(400).html('<img src="ajax-loader.gif" align="absmiddle"> loading.....');
                $.ajax({
                    type: "POST",
                    url: "?controller=managements&action=ajaxManagementsAdd",
                    data: {label_management:label_management},
                    cache: false,
                    dataType: "json"
                }).done(function(response){
                    if(response !== null){
                        if(response[0] !== 0){
                            $("#cbomanagements").append('<option value="'+response[0]+'" selected="selected">'+response[1]+'</option>');       
                            //$("#flash").hide();
                            alert("Gestión agregada!");
                        }
                        else
                            alert("Error: "+response[1]);
                    }
                    else{
                        alert("Ha ocurrido un error! (nulo)");
                    }
                    $("#dialog-new-management").dialog("close");
                }).fail(function(){
                    alert("Ha ocurrido un error!");
                });
            }

            return false;
	});
        
        $(".dlgSbmErr_management").click(function(){
            $("#dialog-error-add-management").dialog("close");
	});
        
        var date_ini = "<?php echo $current_date; ?>";
        $("#hdnPicker").val(date_ini);
        
        //set timepicker for init time field
        var task_time = "<?php echo $current_time; ?>";
        $("#hora_ini").val(task_time);

//        $('#hora_ini').val(task_time.substring(0,5));
        $('#hora_ini').timepicker({
            'step': 15,
            'scrollDefault': task_time,
            'timeFormat': 'H:i'
        });

        //set duration picker
        $('#duration').val('00:15:00');
        $('#duration').timepicker({
            'step': 15,
            'minTime': '00:15:00',
            'scrollDefault': '00:15:00',
            'timeFormat': 'H:i:s'
        });
        
        //hide fields for past jobs
        $(".hdn_row").hide();
        
        //show hidden fields by checkbox
        $("#chk_past").on("click", function(){
            if($("#chk_past").prop("checked")){
                $(".hdn_row").show();
            }
            else{
                $(".hdn_row").hide();
            }
        });
        
        $('#cbocustomers').select2({
            placeholder: {
                id: "",
                text: "Sin Cliente"},
            allowClear:true
        
        });
        
        $('#cbomanagements').select2({
            placeholder: {
                id: "",
                text: "Ingrese Gestión"},
            allowClear:true
        
        });
        
        $('#cbotypes').select2({
            placeholder: {
                id: "",
                text: "Sin Materia"},
            allowClear:true
        });
        
    });
    
    // JQDatepicker
    $(function() {
        $.datepicker.regional['es'] = {
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sábado'],
            dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa']};
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $( "#datepicker" ).datepicker({
            firstDay: 1,
            dateFormat: "yy/mm/dd",
            maxDate: "0D",
            onSelect: function(date, picker){
                $("#hdnPicker").val(date);
            }
        });
    });
    
    // Func submit new project
    function iniTrabajo(){
        $('.input_box').attr('readonly', true);
        $('#datepicker').datepicker().datepicker('disable');
        $("#gestion").val($("#cbomanagements option:selected").text());
        $('#btn_play').attr('disabled', 'disabled');
        
        $('#formModule').submit();
    }
    
    // JQDialog new customer
    $(function() {
        $( "#dialog:ui-dialog" ).dialog( "destroy" );

        var name = $( "#name" ),
            desc = $( "#desc" ),
            allFields = $( [] ).add( name ).add( desc ),
            tips = $( ".validateTips" );

        function updateTips( t ) {
                tips
                        .text( t )
                        .addClass( "ui-state-highlight" );
                setTimeout(function() {
                        tips.removeClass( "ui-state-highlight", 1500 );
                }, 500 );
        }

        function checkLength( o, n, min, max ) {
                if ( o.val().length > max || o.val().length < min ) {
                        o.addClass( "ui-state-error" );
                        updateTips( "Length of " + n + " must be between " +
                                min + " and " + max + "." );
                        return false;
                } else {
                        return true;
                }
        }

        function checkRegexp( o, regexp, n ) {
                if ( !( regexp.test( o.val() ) ) ) {
                        o.addClass( "ui-state-error" );
                        updateTips( n );
                        return false;
                } else {
                        return true;
                }
        }

        $( "#dialog-new-customer" ).dialog({
                autoOpen: false,
                height: 300,
                width: 350,
                modal: true
        });

        $( "#create-customer" ).click(function() {
            $( "#dialog-new-customer" ).dialog( "open" );
        });
    });
    
    
    // JQDialog new type
    $(function() {
        $( "#dialog:ui-dialog" ).dialog( "destroy" );

        var label_type = $( "#label_type" )
            allFields = $( [] ).add(label_type),
            tips = $( ".validateTips" );

        function updateTips( t ) {
                tips
                        .text( t )
                        .addClass( "ui-state-highlight" );
                setTimeout(function() {
                        tips.removeClass( "ui-state-highlight", 1500 );
                }, 500 );
        }

        function checkLength( o, n, min, max ) {
                if ( o.val().length > max || o.val().length < min ) {
                        o.addClass( "ui-state-error" );
                        updateTips( "Length of " + n + " must be between " +
                                min + " and " + max + "." );
                        return false;
                } else {
                        return true;
                }
        }

        function checkRegexp( o, regexp, n ) {
                if ( !( regexp.test( o.val() ) ) ) {
                        o.addClass( "ui-state-error" );
                        updateTips( n );
                        return false;
                } else {
                        return true;
                }
        }

        $( "#dialog-new-type" ).dialog({
                autoOpen: false,
                height: 300,
                width: 350,
                modal: true
        });
        
        $( "#dialog-error-add-type" ).dialog({
                autoOpen: false,
                height: 300,
                width: 350,
                modal: true
        });
        
        $( "#dialog-new-management" ).dialog({
                autoOpen: false,
                height: 300,
                width: 350,
                modal: true
        });
        
        $( "#dialog-error-add-management" ).dialog({
                autoOpen: false,
                height: 300,
                width: 350,
                modal: true
        });

        $( "#create-type" ).click(function() {
            $( "#dialog-new-type" ).dialog( "open" );
        });
        
        $( "#create-management" ).click(function() {
            if ($("#cbocustomers option:selected").text() !== "Sin Cliente") 
            {   
                $( "#dialog-new-management" ).dialog( "open" );
            }
            else {
                $( "#dialog-error-add-management" ).dialog( "open" );;
               
            }
        });
    });
    
    
</script>

</head>
<body id="dt_example" class="ex_highlight_row">

    <?php require('templates/dialogs.tpl.php'); #session & header ?>
    <?php require('templates/menu.tpl.php'); #banner & menu ?>
    
    <!-- CENTRAL -->
    <div id="central">
    <div id="contenido">

        <!-- DEBUG -->
        <?php 
        if($debugMode)
        {
            print('<div id="debugbox">');
            print_r($titulo); print('<br />');
            print($current_date); print('<br />');
            print($current_time); print('<br />');
            
            if(isset($error)){
                print($error); 
                print('<br />');
            }
            
            print('tenant: ');
            print($session->id_tenant);print('<br />');
            print_r($pdoProject);print('<br />');

            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <p class="titulos-form"><?php echo $titulo; ?></p>

        <!--
        <p style="font-size: 12px; color: #999;">
            Nota: Esta pantalla permitir&iacute;a crear un
            nuevo registro de trabajo que tras hacer clic en el boton "INICIO" bajo el control de tiempo, guardar&iacute;a la fecha y la hora en
            que fue creado. Se puede notar que tras presionar el boton de inicio se bloquean los campos anteriores.
            El bot&oacute;n de "PAUSA" permite ignorar el tiempo durante el cual el registro permanece en pausa. Para terminar
            el trabajo habr&iacute;a que presionar el boton "TERMINAR" de color rojo, registrando el momento en que finalizó la tarea.
            El campo "responsable" corresponde al usuario en sesi&oacute;n sin posibilidad de alterar este valor.
        </p>
        -->
        
        <?php 
        if (isset($error_flag)){
            if(strlen($error_flag) > 0){
                echo $error_flag;
            }
        }
        ?>

        <div id="dt_filtres">
            <form id="formModule" name="formModule" method="post" action="?controller=tasks&amp;action=tasksAdd">
                <div id="trabajo_info" style="float: left;width:100%;">
                    <table class="table_left">
                        <tr>
                            <td class="middle">Responsable</td>
                            <td class="middle"><input readonly="readonly" class="input_box" name="resp" type="text" value="<?php echo $name_user; ?>" /></td>
                        </tr>
<!--                        <tr>
                            <td class="middle">Proyecto</td>
                            <td class="middle">
                                <?php/*
                                echo "<select class='input_box' id='cboprojects' name='cboprojects'>\n";
                                echo "<option value='noaplica' selected='selected'>Sin Proyecto</option>\n";
                                while($row = $pdoProject->fetch(PDO::FETCH_ASSOC))
                                {
                                    echo "<option value='$row[id_project]'>$row[label_project]</option>\n";
                                }
                                echo "</select>\n";
                                */?>
                                &nbsp;
                                <a id="create-project" href="#">Nuevo</a>
                            </td>
                        </tr>-->
                        <tr>
                            <td class="middle">Cliente</td>
                            <td class="middle">
                                <?php
                                echo "<select class='input_box' id='cbocustomers' name='cbocustomers'>\n";
                                echo "<option value='noaplica' selected='selected'>Sin Cliente</option>\n";
                                while($row = $pdoCustomer->fetch(PDO::FETCH_ASSOC))
                                {
                                    echo "<option value='$row[id_customer]'>$row[label_customer]</option>\n";
                                }
                                echo "</select>\n";
                                ?>
                                &nbsp;
                                <a id="create-customer" href="#">Nuevo Cliente</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="middle">Materia</td>
                            <td class="middle">
                                <?php
                                echo "<select class='input_box' id='cbotypes' name='cbotypes'>\n";
                                echo "<option value='noaplica' selected='selected'>Sin Materia</option>\n";
                                
                                while($row = $pdoTypes->fetch(PDO::FETCH_ASSOC))
                                {
                                    echo "<option value='$row[id_type]'>$row[label_type]</option>\n";
                                }
                                
                                echo "</select>\n";
                                ?>
                                &nbsp;
                                <a id="create-type" href="#">Nueva Materia</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="middle">Gestión</td>
                            <td class="middle">
                                <?php
                                echo "<select class='input_box' id='cbomanagements' name='cbomanagements'>\n";
                                echo "</select>\n";
                                ?>
                                &nbsp;
                                <a id="create-management" href="#">Nueva Gestión</a>
                            </td>
                        </tr>
                        <tr  class="hidden-tr">
                            <td class="middle">Nombre tarea</td>
                            <td class="middle">
                                <input type="hidden" class="input_box" name="etiqueta" id="gestion"/>
                            </td>
                        </tr>
                        <tr>
                            <td class="middle">Descripci&oacute;n</td>
                            <td class="middle">
                                <textarea class="input_box" name="descripcion"></textarea>
                            </td>
                        </tr>
                        
                    </table>
                    <table class="table_right">
                        <tr>
                            <td class="middle" colspan="2">¿Trabajo ya realizado? <input style="width: 20px;" id="chk_past" class="input_box" name="chk_past" type="checkbox" /></td>
                            <td class="middle"></td>
                        </tr>
                        <tr class="hdn_row">
                            <td class="middle">Fecha inicio</td>
                            <td class="middle"><div id="datepicker"></div></td>
                        </tr>
                        <tr class="hdn_row">
                            <td class="middle">Hora inicio</td>
                            <td class="middle"><input id="hora_ini" class="input_box" name="hora_ini" type="text" value="" /></td>
                        </tr>
                        <tr class="hdn_row">
                            <td class="middle">Duración</td>
                            <td class="middle"><input id="duration" class="input_box" name="duration" type="text" value="" /></td>
                        </tr>
                    </table>
                </div>
                <table id="trabajo_timing" style="float: none; width: 100%; border-top: 1px solid #CCC;">
                    <tr>
                        <td colspan="2" style="text-align: center;">Control de tiempo 
                            <br /><br />
                            <input id="btn_play" class="time_control" type="button" value="INICIO" />
                        </td>
                    </tr>                    
                </table>
                <div style="clear: both;">
                    <input id="hdnPicker" type="hidden" name="fecha" value="" />
                    <input id="hdnUser" type="hidden" name="id_user" value="<?php echo $id_user; ?>" />
                </div>
            </form>
        </div>

    </div>
    </div>
    <!-- END CENTRAL -->

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');