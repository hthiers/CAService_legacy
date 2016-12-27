<script type="text/javascript">
function submitToForm(){
    $('#action_type').val("view");

    return false;
}

$(document).ready(function() {
    var oTable = $('#users_table').dataTable({
        //Initial server side params
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": <?php echo "'?controller=panel&action=ajaxUsersDt'";?>,
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

        "aoColumnDefs": [
            { "mDataProp": null, "aTargets": [-1] },
            { "bVisible": false, "aTargets": [0,1,2] },
            {
                "fnRender": function ( oObj ) {
                    var string = "<a class=\'icon-action fi-page-edit\' href='?controller=panel&action=editUserForm&user_id="+oObj.aData[0]+"'></a>"

                    return string;
                },
                "aTargets": [-1]
            }
        ],

        "sPaginationType": "full_numbers",
        "aaSorting": [[0, "asc"]]
    });

});

function editUser(user){
    console.log(user);
    var urlAction = "<?php echo "?controller=panel&action=editUserForm";?>";

    $('#dt_form').attr('action', urlAction);
    $('#dt_form').attr('method', 'POST');
    $('#user_id').val(user);

    $("#dt_form").submit();
}

</script>
