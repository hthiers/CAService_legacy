<?php
/**
 * HTML for JQuery Dialogs template
 * @author Hernan Thiers
 */
?>
<!-- Confirm action -->
<div id="dialog-confirm" title="Confirmar acci&oacute;n" style="visibility: hidden;">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 2px 10px 0;"></span>Este usuario ser&aacute; eliminado para siempre. &iquest;Desea seguir?</p>
</div>
<!-- END Confirm action -->

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

<!-- New Project -->
<div id="dialog-new-project" title="Nuevo Proyecto">
    <form action="?controller=projects&amp;action=ajaxProjectsAdd" method="POST">
        <fieldset style="padding:0; border:0; margin-top:25px;">
            <label for="name">Nombre Proyecto</label>
            <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="dlgSbm_name_project" id="dlgSbm_name_project" class="text ui-widget-content ui-corner-all" value="" />
            <label for="desc">Descripci&oacute;n</label>
            <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="dlgSbm_desc_project" id="dlgSbm_desc_project" class="text ui-widget-content ui-corner-all" value="" />
            
            <input class="dlgSbmCstr" type="submit" value="GUARDAR" />
        </fieldset>
    </form>
</div>
<!-- END New Project -->

<!-- Project view -->
<div id="dialog-project" title="Proyecto #xx asdfg">
    <form>
        <fieldset style="padding:0; border:0; margin-top:25px;">
        <label for="name">Nombre</label>
        <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
        <label for="email">Encargado</label>
        <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" />
    </fieldset>
    </form>
</div>
<!-- END Project view -->

<!-- New Task -->
<div id="dialog-projectTask" title="Nueva Tarea">
    <form action="#" method="POST">
        <fieldset style="padding:0; border:0; margin-top:25px;">
            <label for="name">Etiqueta</label>
            <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="dlgSbm_name_task" id="dlgSbm_name_task" class="text ui-widget-content ui-corner-all" />
            <label for="email">Descripci&oacute;n</label>
            <input style="margin-bottom:12px; width:95%; padding: .4em;" type="text" name="dlgSbm_desc_task" id="dlgSbm_desc_task" class="text ui-widget-content ui-corner-all" />
            
            <input class="dlgSbmCstr" type="submit" value="GUARDAR" style="font-family: Verdana; font-size: 15px; padding: 2px;" />
        </fieldset>
    </form>
</div>
<!-- END New Customer -->


<!-- KEEP DIALOGS CLOSED -->
<script type="text/javascript" language="javascript">
    $("#dialog-confirm").dialog({ autoOpen: false});
    $("#dialog-new-customer").dialog({ autoOpen: false});
    $("#dialog-new-type").dialog({ autoOpen: false});
    $("#dialog-project").dialog({ autoOpen: false});
    $("#dialog-projectTask").dialog({ autoOpen: false});
    $("#dialog-new-project").dialog({ autoOpen: false});
</script>