<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>

<!-- AGREGAR JS & CSS AQUI -->
<style type="text/css" title="currentStyle">
    .table_left {}
    .table_right {
        margin-left: 70px;
    }
    td.middle {
        padding-bottom: 15px;
        text-align: left;
    }
    input.input_box, textarea.input_box{
        border: 1px solid #989898;
        border-radius: 4px
    }
    #dt_filtres table {
        float: left;
    }
    #dt_filtres input, #dt_filtres textarea {
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
</style>
<script type="text/javascript" language="javascript" src="views/lib/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/utils.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery.timepicker.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#btn_save").click(function (){
        
        var urlAction = "<?php echo "?controller=".$controller."&action=tasksUpdate";?>";
    
        $('#formModule').attr('action', urlAction);
        $('#formModule').attr('method', 'POST');
        $("#formModule").submit();
    });

    $("#btn_cancel").click(function (){
        parent.history.back();
        return false;
    });

    var total_db = <?php if($time_total == null): echo 0; else: echo $time_total; endif; ?>;

    if(total_db > 0){
        var tiempo_array = secondsToTime(total_db);
        var tiempo_string = tiempo_array['h']+':'+tiempo_array['m']+':'+tiempo_array['s'];

        $("#inptTiempoTotal").val(tiempo_string);
    }
    
    //set timepicker for init time
    var date_ini = "<?php echo $date_ini;?>";
    var task_time = formatDateTimeStringTime(date_ini);
        
    $('#hora_ini').val(task_time.substring(0,5));
    $('#hora_ini').timepicker({
        'step': 15,
        'scrollDefault': task_time,
        'timeFormat': 'H:i'
    });
    
    //set duration picker
    var current_duration = "<?php echo $time_total;?>";
    current_duration = formatTime(current_duration);
    
    $('#duration').val(current_duration);
    $('#duration').timepicker({
        'step': 15,
        'minTime': '00:15:00',
        'timeFormat': 'H:i:s'
    });
    
    //set timepicker for end time
    $('#hora_end').attr("disabled", "disabled");
    var date_end = "<?php echo $date_end;?>";
    var task_time_end = formatDateTimeStringTime(date_end);
        
    $('#hora_end').val(task_time_end.substring(0,5));
    $('#hora_end').val(task_time_end.substring(0,5));
    $('#hora_end').timepicker({
        'step': 15,
        'scrollDefault': task_time_end,
        'timeFormat': 'H:i'
    });
    
    //change between duration & end time
    $('.opt_duration').click(function(){
        $('#duration').removeAttr("disabled");
        $('#hora_end').attr("disabled", "disabled");
    });
    $('.opt_hora').click(function(){
        $('#duration').attr("disabled", "disabled");
        $('#hora_end').removeAttr("disabled");
    });
});

// JQDatepicker
$(function() {
    var date_ini = "<?php echo $date_ini;?>";
    var task_date = formatDateTimeStringNoTime(date_ini, 'us');

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
        defaultDate: task_date,
        onSelect: function(date, picker){
            $("#hdnPicker").val(date);
        }
    });
});

function updateTask(){
    var id_task = "<?php echo $id_task;?>";

    $.ajax({
        type: "POST",
        url: "?controller=tasks&action=tasksEdit",
        data: {id_task:id_task},
        cache: false,
        dataType: "json"
    }).done(function(response){
        if(response !== null){
            console.log(response);
            if(response[0] === "0"){
//                $("#flash").hide();
                $('#btn_play').removeAttr('disabled');
                $('#btn_pause').attr('disabled', 'disabled');
                
                console.log("task paused!");
                current_time = $('#progress_clock').val();
                clearTimeout(timeout);
            }
            else{
                alert("sql error");
            }
        }
        else{
            console.log(response);
            alert("response null");
        }
    }).fail(function(jqXHR, textStatus){
        console.log(textStatus);
        alert("ajax error: "+textStatus);
    });
}
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
            
            print("tenant: ".$session->id_tenant.", user: ".$session->id_user."<br/>");
            print($titulo); print('<br />');
            print_r($pdo); print('<br />');
                                  
            print(strtotime($date_ini));print('<br />');
            print(strtotime($currentTime));print('<br />');
            print($total_progress);print('<br />');
            print("paused_date: ".$paused_date);print('<br />');
            
            print('<br />'); print("system: ".$system_message); print('<br />');
            
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <?php #if(isset($pdo)): $values = $pdo->fetch(PDO::FETCH_ASSOC); ?>
        
        <p class="titulos-form"><?php echo $titulo; ?></p>

        <!--
        <p style="font-size: 12px; color: #999;">
            Nota: Esta pantalla permitir&iacute;a revisar un trabajo existente que seg&uacute;n su estado activo o finalizado, podr&iacute;a ser
            pausado o terminado. En este caso aparece un trabajo activo con los campos bloqueados y los botones de pausa y termino disponibles.
        </p>
        -->

        <?php 
//        if (isset($error_flag)){
//            if(strlen($error_flag) > 0)
//                echo $error_flag;
//        }
        ?>

        <div id="dt_filtres">
            <div>
                <form id="formModule" name="formModule" method="" action="">
                    <table class="table_left">
                        <tr>
                            <td class="middle">Responsable</td>
                            <td class="middle"><input class="input_box" name="resp" type="text" readonly value="<?php echo $name_user; ?>" /></td>
                        </tr>
                        <tr>
                            <td class="middle">Cliente</td>
                            <td class="middle">
                                <?php
                                echo "<select class='input_box' id='cbocustomers' name='cbocustomers'>\n";
                                echo "<option value='noaplica'>Sin Cliente</option>\n";
                                while($row = $pdoCustomer->fetch(PDO::FETCH_ASSOC))
                                {
                                    if($row['id_customer'] == $id_customer){
                                        echo "<option selected value='$row[id_customer]'>$row[label_customer]</option>\n";
                                    }
                                    else{
                                        echo "<option value='$row[id_customer]'>$row[label_customer]</option>\n";
                                    }
                                }
                                echo "</select>\n";
                                ?>
                                &nbsp;
                                <!--<a id="create-customer" href="#">Nuevo Cliente</a>-->
                            </td>
                        </tr>
                        <tr>
                            <td class="middle">Gestion</td>
                            <td class="middle">
                                <input type="text" class="input_box" name="etiqueta" value="<?php echo $label_task; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td class="middle">Descripción</td>
                            <td class="middle">
                                <textarea class="input_box" name="descripcion"><?php echo $desc_task;?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="middle">Materia</td>
                            <td class="middle">
                                <?php
                                echo "<select class='input_box' id='cbotypes' name='cbotypes'>\n";
                                echo "<option value='noaplica'>Sin Materia</option>\n";
                                while($row = $pdoTypes->fetch(PDO::FETCH_ASSOC))
                                {
                                    if($row[label_type] == $label_type){
                                        echo "<option selected value='$row[id_type]'>$row[label_type]</option>\n";
                                    }
                                    else{
                                        echo "<option value='$row[id_type]'>$row[label_type]</option>\n";
                                    }
                                }
                                echo "</select>\n";
                                ?>
                                &nbsp;
                                <!--<a id="create-type" href="#">Nueva Materia</a>-->
                            </td>
                            </td>
                        </tr>
                    </table>
                    <table class="table_right">
                       <tr>
                            <td class="middle">Fecha inicio</td>
                            <td class="middle"><div id="datepicker"></div></td>
                        </tr>
                        <tr>
                            <td class="middle">Hora inicio</td>
                            <td class="middle"><input id="hora_ini" class="input_box" name="hora_ini" type="text" value="" /></td>
                        </tr>
                        <tr>
                            <td class="middle">Duración</td>
                            <td class="middle">
                                <input id="duration" class="input_box" name="duration" type="text" value="" />
                                <input style="width: auto;" type="radio" class="opt_duration" name="end_option" value="1" checked="checked" />
                            </td>
                        </tr>
                        <tr>
                            <td class="middle">Hora fin</td>
                            <td class="middle">
                                <input id="hora_end" class="input_box" name="hora_end" type="text" value="" />
                                <input style="width: auto;" type="radio" class="opt_hora" name="end_option" value="2" />
                            </td>
                        </tr>
                    </table>
                    
                    <table id="trabajo_timing" style="float: none; width: 100%; border-top: 1px solid #CCC;">
                        <tr>
                            <td colspan="2" style="text-align: center;"> 
                                <br />
                                <input id="btn_save" class="time_control" type="button" value="GRABAR" />
                                <input id="btn_clean" class="time_control" type="reset" value="LIMPIAR" />
                                <input id="btn_cancel" class="time_control" type="button" value="CANCELAR" />
                            </td>
                        </tr>
                    </table>
                    
                    <div style="clear: both;">
                        <input type="hidden" name="id_task" value="<?php echo $id_task; ?>" />
                        <input type="hidden" id="hdnPicker" name="fecha_ini" value="" />
                    </div>
                </form>
            </div>
            <div id="project-tasks-box" style="margin-top:10px">
                <!--
                <table style="float:none;width:100%;border-top:1px solid #ccc;">
                    <tr>
                        <td colspan="6" style="text-align: center;">Lista de tareas asignadas</td>
                    </tr>
                    
                </table>
                -->
            </div>
            <?php
            #else:
            #    echo "<h4>Ha ocurrido un error grave</h4>";
            #endif;
            ?>
        </div>

    </div>
    </div>
    <!-- END CENTRAL -->

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');