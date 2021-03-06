<script type="text/javascript">
    // JQDialog window

    function ejecutar(cboCustomers, cbotypes, cboManagements) {

        var idCustomer = $(cboCustomers).val();
        var idType = $(cbotypes).val();
        //console.log(idCustomer);
        //console.log(idType);

        if(idCustomer !== null) {
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
                      text: "Ingrese Gestión"}
              });
            },
            error: function(jqXHR, textStatus, errorThrown) {
              $(cboCustomers).next('img').remove();
              alert("Error al ejecutar ajaxGetManagementsByCustomer(): " + textStatus + " - " + errorThrown);
            }
          });
        }
    }

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
              if(data.length > 0) {
                $.each(data , function( index, obj ) {
                    $.each(obj, function( key, value ) {
                        tareas.push(value);
                    });
                });
                $("#gestion").autocomplete({
                    source: tareas
                });
              }
              else {
                console.log("no hay tareas...");
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              alert("Error al ejecutar getTasksName(): " + textStatus + " - " + errorThrown);
            }
      });

      $("#cbocustomers").change(function(e) {
          if ($(this).val().trim() !== "") {
              //console.log("cambia customer");

              $("#cbomanagements").empty();

              if ($(this).val().trim() !== "noaplica") {
                  ejecutar($(this), $("#cbotypes"), $("#cbomanagements"));
                  $("#cbomanagements").val("noaplica").trigger("change");
              }
          }
        });

        $("#cbotypes").change(function(e) {
          if ($(this).val().trim() !== "") {
              //console.log("cambia type");

              $("#cbomanagements").empty();

              if ($(this).val().trim() !== "noaplica") {
                  ejecutar($("#cbocustomers"), $(this), $("#cbomanagements"));
                  $("#cbomanagements").val("noaplica").trigger("change");
              }
          }
        });

        // primer carga (no se espera evento change)
        ejecutar($("#cbocustomers"), $("#cbotypes"), $("#cbomanagements"));

        $("#cbomanagements").val("noaplica").trigger("change");

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
                            //alert("Cliente agregado!");
                            showMessage('#modalMensaje', 'Nuevo Cliente', 'Se ha creado el nuevo cliente exitosamente');
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
              //var id_type = $('cbotypes').val();
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
                          //console.log(response);
                          if(response[0] !== "0"){
                              $("#cbotypes").append('<option value="'+response[0]+'" selected="selected">'+response[1]+'</option>');
                              showMessage('#modalMensaje', 'Nueva Materia', 'Se ha creado la nueva materia exitosamente');
                          }
                          else {
                            //console.log("1.- - 0: " + response[0] + " - 1: " + response[1] + " - 2: " + response[2] + " - 3: " + response[3]);
                            if(response[3] == "Error") {
                              $("#cbotypes").val(response[1]).change();
                              showMessage('#modalMensaje', 'Nueva Materia', "La materia ya existe! Ha sido seleccionada.");
                            }

                          }
                      }
                      else{
                          console.log(" === null 0: " + response[0] + " - 1: " + response[1]);
                          alert("Ha ocurrido un error! (nulo)");
                      }
                      $("#modalNuevaMateria").foundation("close");
                  }).fail(function(response){
                      console.log("fail - 0: " + response[0] + " - 1: " + response[1]);
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
                                showMessage('#modalMensaje', 'Nueva Gestión', 'Se ha agregado la nueva gestión');
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

          $('.close_message').click(function () {
            $('#modalMensaje').foundation('close');
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

        /* select2 */
        $('#cbocustomers').select2();

        $('#cbotypes').select2();

        $('#cbomanagements').select2();

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

    function showMessage(modal, titulo, detalle) {
        //console.log(modal + '  ' + titulo);
        $('#title_message').text(titulo);
        //console.log(modal + '  ' + detalle);
        $('#detail_message').text(detalle);

        $(modal).foundation('open');
    }


</script>
