<?php
/**
 * HTML for JQuery Dialogs template
 * @author Hernan Thiers
 */
?>
<!-- Confirm action -->
<div id="dialog-confirm" title="Confirmar acci&oacute;n">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 2px 10px 0;"></span>¿Esta seguro que quiere eliminar este trabajo?</p>
</div>
<!-- END Confirm action -->

<!-- Dialog remove task -->
<div id="dialog-remove" title="Confirmar acci&oacute;n">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 2px 10px 0;"></span>¿Seguro que quieres eliminarlo?</p>
</div>
<!-- END Dialog remove task -->

<!-- New Customer -->
<div id="dialog-new-customer" title="Nuevo Cliente">
    <form action="?controller=customers&amp;action=ajaxCustomersAdd" method="POST">
        <fieldset style="padding:0; border:0; margin-top:25px;">
            <label for="name">T&iacute;tulo Cliente</label>
            <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="dlgSbm_name_customer" id="dlgSbm_name_customer" class="text ui-widget-content ui-corner-all" />
            <label for="email">Descripci&oacute;n</label>
            <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="dlgSbm_desc_customer" id="dlgSbm_desc_customer" class="text ui-widget-content ui-corner-all" />
            
            <input class="dlgSbmCstr" type="submit" value="GUARDAR" style="width:80px;height:40px;font-family:inherit;font-size:12px;" />
        </fieldset>
    </form>
</div>
<!-- END New Customer -->

<!-- New Type -->
<div id="dialog-new-type" title="Nueva Materia">
    <form action="?controller=types&amp;action=ajaxTypesAdd" method="POST">
        <fieldset style="padding:0; border:0; margin-top:25px;">
            <label for="label_type">Nombre Materia</label>
            <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="dlgSbm_name_type" id="dlgSbm_name_type" class="text ui-widget-content ui-corner-all" />            
            <input class="dlgSbmCstr_type" type="submit" value="GUARDAR" style="width:80px;height:40px;font-family:inherit;font-size:12px;" />
        </fieldset>
    </form>
</div>
<!-- END New Type -->

<!-- Can't Add Type -->
<div id="dialog-error-add-type" title="Nueva Materia">
        <fieldset style="padding:0; border:0; margin-top:25px;">
            <label>Primero debe seleccionar un cliente</label>
            <br />
            <input class="dlgSbmErr_type" type="submit" value="VOLVER" style="width:80px;height:40px;font-family:inherit;font-size:12px;" />
        </fieldset>
</div>
<!-- END Can't Add Type -->

<!-- New Management -->
<div id="dialog-new-management" title="Nueva Gestión">
    <form action="?controller=managements&amp;action=ajaxManagementsAdd" method="POST">
        <fieldset style="padding:0; border:0; margin-top:25px;">
            <label for="label_management">Nombre Gestión</label>
            <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="dlgSbm_name_management" id="dlgSbm_name_management" class="text ui-widget-content ui-corner-all" />            
            <input class="dlgSbmCstr_management" type="submit" value="GUARDAR" style="width:80px;height:40px;font-family:inherit;font-size:12px;" />
        </fieldset>
    </form>
</div>
<!-- END New Management -->

<!-- Can't Add Management -->
<div id="dialog-error-add-management" title="Nueva Gestión">
        <fieldset style="padding:0; border:0; margin-top:25px;">
            <label>Primero debe seleccionar un cliente</label>
            <br />
            <input class="dlgSbmErr_management" type="submit" value="VOLVER" style="width:80px;height:40px;font-family:inherit;font-size:12px;" />
        </fieldset>
</div>
<!-- END Can't Add Management -->


<!-- KEEP DIALOGS CLOSED -->
<script type="text/javascript" language="javascript">
    $("#dialog-confirm").dialog({ autoOpen: false});
    $("#dialog-remove").dialog({ autoOpen: false});
    $("#dialog-new-customer").dialog({ autoOpen: false});
    $("#dialog-new-type").dialog({ autoOpen: false});
    $("#dialog-error-add-type").dialog({ autoOpen: false});
    $("#dialog-new-management").dialog({ autoOpen: false});
    $("#dialog-error-add-management").dialog({ autoOpen: false});
</script>