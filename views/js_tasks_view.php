<script type="text/javascript">
$(document).ready(function(){
    requested_action = "<?php echo $action;?>";
    //console.log(requested_action);

    if(requested_action === "view"){
        $('form input[type=text]').attr('readonly', true);
        $('form textarea').attr('readonly', true);
    }

    $("#btn_play").click(function (){
        iniTrabajo();
    });

    $("#btn_pause").click(function (){
        pausaTrabajo();
    });

    $("#btn_stop").click(function (event){
        event.preventDefault();
        $("#formModule").attr("action", "?controller=tasks&action=tasksStop");
        $("#formModule").attr("method", "post");
        $('#formModule').submit();
    });

    var total_db = <?php if($time_total == null): echo 0; else: echo $time_total; endif; ?>;

    if(total_db > 0){
        var tiempo_array = secondsToTime(total_db);
        var tiempo_string = tiempo_array['h']+':'+tiempo_array['m']+':'+tiempo_array['s'];

        $("#inptTiempoTotal").val(tiempo_string);
    }

    var total_progress = <?php if($total_progress == null): echo 0; else: echo $total_progress; endif;?>;
    var status = <?php echo $status_task; ?>;

    // Set timer
    //active
    if(status === 1){
        //console.log("continued");
        //console.log("$pasued_date: <?php echo $paused_date;?>");
        //console.log("$time_paused: <?php echo $time_paused;?>");

        var tiempo_array = secondsToTime(total_progress);
        var tiempo_string = tiempo_array['h']+':'+tiempo_array['m']+':'+tiempo_array['s'];
        customClock(tiempo_string);
    }
    //paused
    else if(status === 3){
        //console.log("paused");
        //console.log("$pasued_date: <?php echo $paused_date;?>");
        //console.log("$time_paused: <?php echo $time_paused;?>");

        var paused_seconds = <?php if($paused_date == null): echo 0; else: echo $paused_date; endif;?>;
        //console.log(paused_seconds);

        var paused_array = secondsToTime(paused_seconds);
        var paused_string = paused_array['h']+':'+paused_array['m']+':'+paused_array['s'];
        $('#progress_clock').val(paused_string);
    }
});

function iniTrabajo(){
    $('#btn_play').attr('disabled', 'disabled');
    $('#btn_pause').removeAttr('disabled');

    var id_task = "<?php echo $id_task;?>";

    $.ajax({
        type: "POST",
        url: "?controller=tasks&action=tasksContinue",
        data: {id_task:id_task},
        cache: false,
        dataType: "json"
    }).done(function(response){
        if(response !== null){
            //console.log(response);
            if(response[0] === "0"){
                $('#btn_pause').removeAttr('disabled');
                $('#btn_play').attr('disabled', 'disabled');

                //console.log("Trabajo activado!");
                current_time = $('#progress_clock').val();
                customClock(current_time);
            }
            else{
                alert("sql error: "+response[1]);
            }
        }
        else{
            alert("response null");
        }
    }).fail(function(jqXHR, textStatus){
        //console.log(textStatus);
        alert("ajax error: "+textStatus);
    });
}

function pausaTrabajo(){
    var id_task = "<?php echo $id_task;?>";

    $.ajax({
        type: "POST",
        url: "?controller=tasks&action=tasksPause",
        data: {id_task:id_task},
        cache: false,
        dataType: "json"
    }).done(function(response){
        if(response !== null){
            //console.log(response);
            if(response[0] === "0"){
//                $("#flash").hide();
                $('#btn_play').removeAttr('disabled');
                $('#btn_pause').attr('disabled', 'disabled');

                //console.log("task paused!");
                current_time = $('#progress_clock').val();
                clearTimeout(timeout);
            }
            else{
                alert("sql error");
            }
        }
        else{
            //console.log(response);
            alert("response null");
        }
    }).fail(function(jqXHR, textStatus){
        //console.log(textStatus);
        alert("ajax error: "+textStatus);
    });
}

function updateTask(){
    var id_task = "<?php echo $id_task;?>";

    $.ajax({
        type: "POST",
        url: "?controller=tasks&action=tasksPause",
        data: {id_task:id_task},
        cache: false,
        dataType: "json"
    }).done(function(response){
        if(response !== null){
            //console.log(response);
            if(response[0] === "0"){
//                $("#flash").hide();
                $('#btn_play').removeAttr('disabled');
                $('#btn_pause').attr('disabled', 'disabled');

                //console.log("task paused!");
                current_time = $('#progress_clock').val();
                clearTimeout(timeout);
            }
            else{
                alert("sql error");
            }
        }
        else{
            //console.log(response);
            alert("response null");
        }
    }).fail(function(jqXHR, textStatus){
        //console.log(textStatus);
        alert("ajax error: "+textStatus);
    });
}
</script>
