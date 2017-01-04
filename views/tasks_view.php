<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>

<?php require_once('js_tasks_view.php'); # JS ?>

</head>
<body id="dt_example" class="ex_highlight_row">

    <?php require('templates/menu.tpl.php'); #banner & menu ?>

    <!-- CENTRAL -->
    <div class="row">

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

          <h1>
            <span class="icon-title fi-clipboard-notes"></span><?php echo $titulo; ?>
          </h1>

        <?php
//        if (isset($error_flag)){
//            if(strlen($error_flag) > 0)
//                echo $error_flag;
//        }
        ?>

        <div class="sweetbox-pad">

          <form id="formModule" name="formModule" method="" action="">
            <div class="row">

              <div class="row">
                <div class="medium-3 columns">
                  <label for="resp" class="text-right middle">Responsable</label>
                </div>
                <div class="medium-9 columns">
                  <input name="resp" type="text" value="<?php echo $name_user; ?>" />
                </div>
              </div>

              <div class="row">
                <div class="medium-3 columns">
                  <label for="resp" class="text-right middle">Cliente</label>
                </div>
                <div class="medium-9 columns">
                  <input name="cliente" type="text" value="<?php echo $label_customer; ?>" />
                </div>
              </div>

              <div class="row">
                <div class="medium-3 columns">
                  <label for="resp" class="text-right middle">Materia</label>
                </div>
                <div class="medium-9 columns">
                  <input type="text" name="materia" value="<?php echo $label_type; ?>" />
                </div>
              </div>

              <div class="row">
                <div class="medium-3 columns">
                  <label for="resp" class="text-right middle">Gestion</label>
                </div>
                <div class="medium-9 columns">
                  <input type="text" name="etiqueta" value="<?php echo $label_task; ?>" />
                </div>
              </div>

              <div class="row">
                <div class="medium-3 columns">
                  <label for="resp" class="text-right middle">Descripción</label>
                </div>
                <div class="medium-9 columns">
                  <textarea name="descripcion"><?php echo $desc_task;?></textarea>
                </div>
              </div>

              <div class="row">
                <div class="medium-3 columns">
                  <label for="resp" class="text-right middle">Fecha inicio</label>
                </div>
                <div class="medium-9 columns">
                  <input class="input_box" name="fecha_ini" type="text" value="<?php echo $date_ini; ?>" />
                </div>
              </div>

              <?php
              if($action !== "edit"):
                  // Active and on time
                  if($status_task == 1 && strtotime($currentTime) >= strtotime($date_ini)): ?>

                  <div class="row">
                    <div class="medium-3 columns">
                      <label for="resp" class="text-right middle">Tiempo transcurrido</label>
                    </div>
                    <div class="medium-9 columns">
                      <input id="progress_clock" readonly="readonly" name="tiempo_progress" type="text" value="" />
                    </div>
                  </div>

                  <div class="medium-3 medium-centered columns">
                    <a id="btn_play" class="button success icon-tool icon-tool-circle" disabled="disabled"><span class="icon-tool fi-play"></span></a>
                    <a id="btn_pause" class="button icon-tool icon-tool-pause"><span class="icon-tool fi-pause"></span></a>
                    <a id="btn_stop" class="button icon-tool alert icon-tool-stop"><span class="icon-tool fi-stop"></span></a>
                  </div>

                  <?php
                  // Active and scheduled in future
                  elseif($status_task == 1 && strtotime($currentTime) < strtotime($date_ini)):?>
                  <div class="medium-3 medium-centered columns">
                      <a id="btn_play" class="button success icon-tool icon-tool-circle" disabled="disabled"><span class="icon-tool fi-play"></span></a>
                      <a id="btn_pause" class="button icon-tool icon-tool-pause" disabled="disabled"><span class="icon-tool fi-pause"></span></a>
                      <a id="btn_stop" class="button icon-tool alert icon-tool-stop" disabled="disabled"><span class="icon-tool fi-stop"></span></a>
                  </div>
                  <?php
                  // Paused
                  elseif($status_task == 3 && strtotime($currentTime) > strtotime($date_ini)):?>

                  <div class="row">
                    <div class="medium-3 columns">
                      <label for="resp" class="text-right middle">Tiempo transcurrido</label>
                    </div>
                    <div class="medium-9 columns">
                      <input id="progress_clock" readonly="readonly" name="tiempo_progress" type="text" value="" />
                    </div>
                  </div>

                    <div class="medium-3 medium-centered columns">
                      <a id="btn_play" class="button success icon-tool icon-tool-circle"><span class="icon-tool fi-play"></span></a>
                      <a id="btn_pause" class="button icon-tool icon-tool-pause" disabled="disabled"><span class="icon-tool fi-pause"></span></a>
                      <a id="btn_stop" class="button icon-tool alert icon-tool-stop"><span class="icon-tool fi-stop"></span></a>
                    </div>

                  <?php
                  // Finalized
                  else: ?>

                  <div class="row">
                    <div class="medium-3 columns">
                      <label for="resp" class="text-right middle">Fecha fin</label>
                    </div>
                    <div class="medium-9 columns">
                      <input readonly="readonly" name="fecha_fin" type="text" value="<?php echo $date_end; ?>" />
                    </div>
                  </div>

                  <div class="row">
                    <div class="medium-3 columns">
                      <label for="resp" class="text-right middle">Tiempo total</label>
                    </div>
                    <div class="medium-9 columns">
                      <input id="inptTiempoTotal" readonly="readonly" name="tiempo_total" type="text" value="" />

                      <input type="hidden" id="time_total_s" name="time_total_s" value="<?php echo $time_s; ?>" />
                      <input type="hidden" id="time_total_m" name="time_total_m" value="<?php echo $time_m; ?>" />
                      <input type="hidden" id="time_total_h" name="time_total_h" value="<?php echo $time_h; ?>" />
                    </div>
                  </div>

                  <?php endif;?>
              <?php else: ?>

                <div class="medium-1 medium-centered columns">
                  <input id="btn_save" class="time_control" type="button" value="GRABAR" />
                  <input id="btn_clean" class="time_control" type="reset" value="LIMPIAR" />
                  <input id="btn_cancel" class="time_control" type="button" value="CANCELAR" />
                </div>

              <?php endif; ?>

              <input type="hidden" name="id_task" value="<?php echo $id_task; ?>" />

            </div>
          </form>
        </div>
  </div>

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');
