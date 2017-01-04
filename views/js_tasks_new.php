<script type="text/javascript">
    // JQDialog window
    var windowSizeArray = [ "width=200,height=200","width=300,height=400,scrollbars=yes" ];

    $(document).ready(function(){
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
                    ejecutar($(this), $("#cbotypes"), $("#cbomanagements"));
                    $("#cbomanagements").val("noaplica").trigger("change");
                }
            }
          });

          $("#cbotypes").change(function(e) {
            if ($(this).val().trim() !== "") {
                console.log("cambia type");

                $("#cbomanagements").empty();

                if ($(this).val().trim() !== "noaplica") {
                    ejecutar($("#cbocustomers"), $(this), $("#cbomanagements"));
                    $("#cbomanagements").val("noaplica").trigger("change");
                }
            }
          });

        function ejecutar(cboCustomers, cbotypes, cboManagements) {

            var idCustomer = $(cboCustomers).val();
            var idType = $(cbotypes).val();

            console.log(idCustomer);
            console.log(idType);

            $.ajax({
              type: "POST",
              url: "?controller=managements&action=ajaxGetManagementsByCustomer",
              dataType: "html",
              data: { id_customer : idCustomer, id_type : idType},
              success: function(msg) {
                $(cboManagements).html(msg).attr("disabled", false);

                $('#cbomanagements').select2({
                    placeholder: {
                        id: "",
                        text: "Ingrese Gestión"},
                    allowClear:true
                });

              },
              error: function(jqXHR, textStatus, errorThrown) {
                $(cboCustomers).next('img').remove();
                alert("Error al ejecutar =&gt; " + textStatus + " - " + errorThrown);
              }
            });
        }

        // JQDialog Submit - Add new customer
        $("#modalNuevoCliente form").submit(function(){
            var name = $("#dlgSbm_name_customer").val();
            var desc = $("#dlgSbm_desc_customer").val();
            if(name === '')
            {
                alert("Ingrese título del cliente");
            }
            else
            {
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
                            alert("Cliente agregado!");
                        }
                        else
                            alert("Error: "+response[1]);
                    }
                    else{
                        alert("Ha ocurrido un error! (nulo)");
                    }
                    //$("#modalNuevoCliente").dialog("close");
                    $('#modalNuevoCliente').foundation('close');
                }).fail(function(){
                    alert("Ha ocurrido un error!");
                });
            }

            return false;
	});


        // JQDialog Submit - Add new type
        $("#modalNuevaMateria form").submit(function(){
            var customer = $("#cbocustomers").val();
            var label_type = $("#dlgSbm_name_type").val();
            if(label_type === '')
            {
                alert("Ingrese nombre de la materia");
            }
            else
            {
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
                            alert("Materia agregada!");
                        }
                        else
                            alert("Error: "+response[1]);
                    }
                    else{
                        alert("Ha ocurrido un error! (nulo)");
                    }
                    $("#modalNuevaMateria").foundation("close");
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
        $("#modalNuevaGestion form").submit(function(){
            var label_management = $("#dlgSbm_name_management").val();
            if(label_management === '')
            {
                alert("Ingrese nombre de la gestión");
            }
            else
            {
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
                            alert("Gestión agregada!");
                        }
                        else
                            alert("Error: "+response[1]);
                    }
                    else{
                        alert("Ha ocurrido un error! (nulo)");
                    }
                    $("#modalNuevaGestion").foundation("close");
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
        $('#btn_play').addClass('disabled');

        $('#formModule').submit();
    }

    // JQDialog new customer
    // $(function() {
    //     $( "#dialog:ui-dialog" ).dialog( "destroy" );
    //
    //     var name = $( "#name" ),
    //         desc = $( "#desc" ),
    //         allFields = $( [] ).add( name ).add( desc ),
    //         tips = $( ".validateTips" );
    //
    //     function updateTips( t ) {
    //             tips
    //                     .text( t )
    //                     .addClass( "ui-state-highlight" );
    //             setTimeout(function() {
    //                     tips.removeClass( "ui-state-highlight", 1500 );
    //             }, 500 );
    //     }
    //
    //     function checkLength( o, n, min, max ) {
    //             if ( o.val().length > max || o.val().length < min ) {
    //                     o.addClass( "ui-state-error" );
    //                     updateTips( "Length of " + n + " must be between " +
    //                             min + " and " + max + "." );
    //                     return false;
    //             } else {
    //                     return true;
    //             }
    //     }
    //
    //     function checkRegexp( o, regexp, n ) {
    //             if ( !( regexp.test( o.val() ) ) ) {
    //                     o.addClass( "ui-state-error" );
    //                     updateTips( n );
    //                     return false;
    //             } else {
    //                     return true;
    //             }
    //     }
    //
    //     $( "#dialog-new-customer" ).dialog({
    //             autoOpen: false,
    //             height: 300,
    //             width: 350,
    //             modal: true
    //     });
    //
    //     $( "#create-customer" ).click(function() {
    //         $( "#dialog-new-customer" ).dialog( "open" );
    //     });
    // });


    // JQDialog new type
    // $(function() {
    //     $( "#dialog:ui-dialog" ).dialog( "destroy" );
    //
    //     var label_type = $( "#label_type" )
    //         allFields = $( [] ).add(label_type),
    //         tips = $( ".validateTips" );
    //
    //     function updateTips( t ) {
    //             tips
    //                     .text( t )
    //                     .addClass( "ui-state-highlight" );
    //             setTimeout(function() {
    //                     tips.removeClass( "ui-state-highlight", 1500 );
    //             }, 500 );
    //     }
    //
    //     function checkLength( o, n, min, max ) {
    //             if ( o.val().length > max || o.val().length < min ) {
    //                     o.addClass( "ui-state-error" );
    //                     updateTips( "Length of " + n + " must be between " +
    //                             min + " and " + max + "." );
    //                     return false;
    //             } else {
    //                     return true;
    //             }
    //     }
    //
    //     function checkRegexp( o, regexp, n ) {
    //             if ( !( regexp.test( o.val() ) ) ) {
    //                     o.addClass( "ui-state-error" );
    //                     updateTips( n );
    //                     return false;
    //             } else {
    //                     return true;
    //             }
    //     }
    //
    //     $( "#dialog-new-type" ).dialog({
    //             autoOpen: false,
    //             height: 300,
    //             width: 350,
    //             modal: true
    //     });
    //
    //     $( "#dialog-error-add-type" ).dialog({
    //             autoOpen: false,
    //             height: 300,
    //             width: 350,
    //             modal: true
    //     });
    //
    //     $( "#dialog-new-management" ).dialog({
    //             autoOpen: false,
    //             height: 300,
    //             width: 350,
    //             modal: true
    //     });
    //
    //     $( "#dialog-error-add-management" ).dialog({
    //             autoOpen: false,
    //             height: 300,
    //             width: 350,
    //             modal: true
    //     });
    //
    //     $( "#create-type" ).click(function() {
    //         $( "#dialog-new-type" ).dialog( "open" );
    //     });
    //
    //     $( "#create-management" ).click(function() {
    //         if ($("#cbocustomers option:selected").text() !== "Sin Cliente")
    //         {
    //             $( "#dialog-new-management" ).dialog( "open" );
    //         }
    //         else {
    //             $( "#dialog-error-add-management" ).dialog( "open" );;
    //
    //         }
    //     });
    // });

</script>
