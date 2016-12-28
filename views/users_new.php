<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
//if($session->privilegio > 0):
?>

<?php require_once('js_users_new.php'); # JS ?>

</head>
<body>

    <?php require('templates/menu.tpl.php'); #banner & menu?>

    <!-- CENTRAL -->
    <div class="row">

        <!-- DEBUG -->
        <?php
        if($debugMode)
        {
            print('<div id="debugbox">');
            print_r($titulo); print('<br />');print_r($controller); print('<br />');
            print_r($action); print('<br />');print_r($action_b); print('<br />');
            print_r($profiles); print('<br />');print_r($action_b); print('<br />');
            print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <h1>
          <span class="icon-title fi-clipboard-pencil"></span><?php echo $titulo; ?>
        </h1>

        <?php
        if (isset($error_flag)){
            if(strlen($error_flag) > 0)
                echo $error_flag;
        }
        ?>

        <div class="sweetbox-pad">

          <!-- FORM -->
          <form id="moduleForm" name="form1" method="post" action="?controller=panel&amp;action=newUserAdd">
            <!-- form container -->
            <div class="row">

              <div class="row">
                <div class="medium-3 columns">
                  <label for="name_user" class="text-right middle">Nombre de Usuario</label>
                </div>
                <div class="medium-9 columns">
                  <input class="input_box" type="text" id="name_user" name="name_user" value="" />
                </div>
              </div>

              <div class="row">
                <div class="medium-3 columns">
                  <label for="cboprofiles" class="text-right middle">Perfil</label>
                </div>
                <div class="medium-9 columns">
                  <?php
                  echo "<select class='input_box' id='cboprofiles' name='cboprofiles'>\n";
                  echo "<option value='noaplica' selected='selected'>Seleccione Perfil</option>\n";
                  while($row = $profiles->fetch(PDO::FETCH_ASSOC))
                  {
                      echo "<option value='$row[id_profile]' >$row[label_profile]</option>\n";
                  }
                  echo "</select>\n";
                  ?>
                </div>
              </div>

              <div class="row">
                <div class="medium-3 columns">
                  <label for="pass_user" class="text-right middle">Contraseña</label>
                </div>
                <div class="medium-9 columns">
                  <input class="input_box" type="password" name="pass_user" id="pass_user" />
                </div>
              </div>

              <div class="row">
                <div class="medium-3 columns">
                  <label for="pass_user_cnf" class="text-right middle">Repita contraseña</label>
                </div>
                <div class="medium-9 columns">
                  <input class="input_box" type="password" name="pass_user_cnf" id="pass_user_cnf" />
                </div>
              </div>

              <div class="medium-1 medium-centered columns">
                <button name="button" type="submit" class="button">Crear</button>
              </div>

            </div>
          </form>
        </div>

    </div>
    <!-- END CENTRAL -->

<?php
//endif; #privs
endif; #session

require('templates/footer.tpl.php');
?>
</body>
