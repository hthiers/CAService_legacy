<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>

<!-- Funciones JS -->
<?php require_once('js_tasks_new.php'); # JS ?>

</head>
<body>

    <?php #require('templates/dialogs.tpl.php'); #session & header ?>
    <?php require('templates/menu.tpl.php'); #banner & menu ?>

    <!-- Content -->
    <div class="row">

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

        <h1>
          <span class="icon-title fi-clipboard-pencil"></span><?php echo $titulo; ?>
        </h1>

        <?php
        if (isset($error_flag)){
            if(strlen($error_flag) > 0){
                echo $error_flag;
            }
        }
        ?>

        <div class="sweetbox-pad">

            <!-- Nueva tarea-->
            <form id="formModule" name="formModule" method="post" action="?controller=tasks&amp;action=tasksAdd">
              <!-- form container -->
              <div class="row">

                <!-- seccion nueva tarea -->
                <div class="medium-6 columns">
                  <div class="row">
                    <div class="medium-3 columns">
                      <label for="resp" class="text-right middle">Responsable</label>
                    </div>
                    <div class="medium-9 columns">
                      <input
                        readonly="readonly"
                        id="resp"
                        name="resp"
                        type="text"
                        value="<?php echo $name_user; ?>"
                      />
                    </div>
                  </div>

                  <div class="row">
                    <div class="medium-3 columns">
                      <label for="cbocustomers" class="text-right middle">Cliente</label>
                    </div>
                    <div class="medium-9 columns">
                      <?php
                      echo "<select class='input_box' id='cbocustomers' name='cbocustomers'>\n";
                      echo "<option value='noaplica' selected='selected'>Sin Cliente</option>\n";
                      while($row = $pdoCustomer->fetch(PDO::FETCH_ASSOC))
                      {
                          echo "<option value='$row[id_customer]'>$row[label_customer]</option>\n";
                      }
                      echo "</select>\n";
                      ?>

                      <a id="create-customer" href="#"><span class="fi-plus icon-tool pushed"></span></a>
                    </div>
                  </div>

                  <div class="row">
                    <div class="medium-3 columns">
                      <label for="cbotypes" class="text-right middle">Materia</label>
                    </div>
                    <div class="medium-9 columns">
                      <?php
                      echo "<select class='input_box' id='cbotypes' name='cbotypes'>\n";
                      echo "<option value='noaplica' selected='selected'>Sin Materia</option>\n";

                      while($row = $pdoTypes->fetch(PDO::FETCH_ASSOC))
                      {
                          echo "<option value='$row[id_type]'>$row[label_type]</option>\n";
                      }

                      echo "</select>\n";
                      ?>

                      <a id="create-type" href="#"><span class="fi-plus icon-tool pushed"></span></a>
                    </div>
                  </div>

                  <div class="row">
                    <div class="medium-3 columns">
                      <label for="cbotypes" class="text-right middle">Gestión</label>
                    </div>
                    <div class="medium-9 columns">
                      <?php
                        echo "<select class='input_box' id='cbomanagements' name='cbomanagements'>\n";
                        echo "</select>\n";
                        ?>

                        <a id="create-management" href="#"><span class="fi-plus icon-tool pushed"></span></a>
                    </div>
                  </div>

                  <div class="row">
                    <div class="medium-3 columns">
                      <label for="cbotypes" class="text-right middle">Descripción</label>
                    </div>
                    <div class="medium-9 columns">
                      <textarea rows="5" class="input_box" name="descripcion"></textarea>
                    </div>
                  </div>

                </div>
                <!-- /Seccion nueva tarea -->

                <!-- Seccion tarea pasada -->
                <div class="medium-6 columns">

                  <div class="row">
                    <div class="medium-6 columns">
                      ¿Trabajo ya realizado?
                    </div>
                    <div class="medium-6 columns">
                      <div class="switch">
                        <input class="switch-input" id="chk_past" type="checkbox" name="exampleSwitch" />
                        <label class="switch-paddle" for="chk_past">
                            <span class="show-for-sr">¿Trabajo ya realizado?</span>
                            <span class="switch-active" aria-hidden="true">Si</span>
                            <span class="switch-inactive" aria-hidden="true">No</span>
                        </label>
                      </div>
                    </div>
                  </div>

                  <div class="row hdn_row">
                    <div class="medium-3 columns">
                      <label for="cbotypes" class="text-right middle">Fecha inicio</label>
                    </div>
                    <div class="medium-9 columns">
                      <div id="datepicker" class="datepicker"></div>
                    </div>
                  </div>
                  <div class="row hdn_row">
                    <div class="medium-3 columns">
                      <label for="cbotypes" class="text-right middle">Hora inicio</label>
                    </div>
                    <div class="medium-9 columns">
                      <input id="hora_ini" name="hora_ini" type="text" value="" />
                    </div>
                  </div>
                  <div class="row hdn_row">
                    <div class="medium-3 columns">
                      <label for="cbotypes" class="text-right middle">Duración</label>
                    </div>
                    <div class="medium-9 columns">
                      <input id="duration" name="duration" type="text" value="" />
                    </div>
                  </div>

                </div>
                <!-- /Seccion tarea pasada -->

                <input id="hdnPicker" type="hidden" name="fecha" value="" />
                <input id="hdnUser" type="hidden" name="id_user" value="<?php echo $id_user; ?>" />

                <div class="medium-1 medium-centered columns">
                  <a id="btn_play" class="button success icon-tool-circle"><span class="icon-tool fi-play-circle"></span></a>
                </div>

              </div>
              <!-- /form container -->
            </form>
            <!-- /Nueva tarea-->

        </div>
    </div>
    <!-- /Content -->

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');
