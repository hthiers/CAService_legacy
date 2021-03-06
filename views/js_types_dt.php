<script type="text/javascript">

var oTable = null;

function submitToForm(){
    $('#action_type').val("view");

    return false;
}

function removeType(type){
    var urlAction = "<?php echo "?controller=types&action=typesRemove";?>";

    /*
    $( "#dialog-remove" ).dialog({
            height: 200,
            width: 350,
            modal: true,
            buttons: {
            "Eliminar": function() {
                console.log("borrando: #"+type);

                $('#dt_form').attr('action', urlAction);
                $('#dt_form').attr('method', 'POST');
                $('#type_id').val(type);

                $( this ).dialog( "close" );
                $("#dt_form").submit();
            },
            "Cancelar": function() {
              $( this ).dialog( "close" );
            }
          }
    });

    $("#dialog-remove")
            .data('type_id', type)
            .dialog("open");
    */
   $('#type_id').val(type);
   $('#modalEliminarMateriaAdevertencia').foundation('open');
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

                alert("Error al ejecutar getCustomersByTenant - ; " + textStatus + " - " + errorThrown);
              }
        });

    oTable = $('#table').dataTable({
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
          { "bVisible": false, "aTargets": [0,1,2,4,5] },
          { "sWidth": "40%", "aTargets": [3] },
          { "sWidth": "40%", "aTargets": [5] },
          { "sWidth": "20%", "aTargets": [-1] },
          {
              "fnRender": function ( oObj ) {
                  var dt_tools = "";
                  dt_tools = dt_tools+"<a id=\'tool_remove\' class=\'icon-action fi-trash\' title=\'Borrar\' name='"+oObj.aData[0]+"' onclick='removeType("+oObj.aData[0]+")' />";

                  return dt_tools;
              },
              "aTargets": [-1]
          }
        ],

        "sPaginationType": "full_numbers",
        "aaSorting": [[3, "asc"]],

        "fnDrawCallback": function () {
            $('#table tbody td.td_editable').editable( '?controller=types&action=ajaxTypesUpdate', {

                "callback": function( sValue, y ) {
                    //console.log("valor: "+ sValue);
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

                "placeholder" : ""
            } );

        }
    });

    $('#cbocustomers').select2({
       allowClear:true,
       theme: "classic"
   });

    // boton nueva materia
    $("#create-type").click(function() {

        guardarMateria();
    });

    //Add Class to search field
    //$(".dataTables_filter input").addClass("search_input");

    $('#confirmarEliminarMateria').click(function() {

        var urlAction = "<?php echo "?controller=types&action=typesRemove";?>";

        //console.log("borrando: #"+$('#type_id').val());

        $('#dt_form').attr('action', urlAction);
        $('#dt_form').attr('method', 'POST');
        //$('#type_id').val(idType);

        $("#dt_form").submit();
    });

    $('#cancelarEliminarMateria').click(function() {

        $('#modalEliminarMateriaAdevertencia').foundation('close');
    });

});


function guardarMateria() {
    var label = $('#new_type_label').val();
    //var customer = $('#cbocustomers').val();

    //alert("cliente: "+customer);
    $.ajax(
            {
                type: "POST",
                //url: "?controller=types&action=ajaxTypesAddWithCustomer",
                //data: { label_type: label, id_customer: customer },
                url: "?controller=types&action=ajaxTypesAdd",
                data: { label_type: label },
                cache: false,
                //contentType: "application/json; charset=utf-8",
                dataType: "json"
            }).done(function(response){

        if(response !== null){
            //console.log(response);
            oTable.fnDraw();
        }
        else{
            //console.log("response null");
        }
        }).fail(function(jqXHR, textStatus){
            //console.log(textStatus);
    });
}

function showMessage(modal, titulo, detalle) {
        //console.log(modal + '  ' + titulo);
        $('#title_message').text(titulo);
        //console.log(modal + '  ' + detalle);
        $('#detail_message').text(detalle);

        $(modal).foundation('open');
    }


</script>
