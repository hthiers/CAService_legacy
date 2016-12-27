<script type="text/javascript">
function submitToForm(){
    $('#action_type').val("view");

    return false;
}

$(document).ready(function() {
    var oTable = $('#example').dataTable({
        //Initial server side params
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": <?php echo "'?controller=customers&action=ajaxCustomersDt'";?>,
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
            "sProcessing": "",
            "oPaginate": {
                "sFirst": "Primera",
                "sNext": "Siguiente",
                "sPrevious": "Anterior",
                "sLast": "&Uacute;ltima"
            }
        },
        "aoColumnDefs": [
            { "bVisible": false, "aTargets": [0,1,2] }
        ],

        "sPaginationType": "full_numbers",
        "aaSorting": [[0, "asc"]],

        "fnDrawCallback": function () {
            $('#example tbody td').editable( '?controller=customers&action=ajaxCustomersUpdate', {

                "callback": function( sValue, y ) {
                    console.log("valor: "+ sValue);

                    var aPos = oTable.fnGetPosition( this );
                    oTable.fnUpdate( sValue, aPos[0], aPos[1] );
                },
                "submitdata": function ( value, settings ) {
                    return {
                        "row_id": this.parentNode.getAttribute('id'),
                        "column": oTable.fnGetPosition( this )[2]
                    };
                },
                "placeholder" : "",
                "height": "14px"
            } );
        }
    });
});
</script>
