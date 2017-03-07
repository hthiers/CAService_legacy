<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
//if($session->privilegio > 0):
?>

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
            print_r($title); print('<br />');print_r($controller); print('<br />');
            print_r($profiles); print('<br />');print_r($action_b); print('<br />');
            print_r($user); print('<br />');print_r($action_b); print('<br />');
            print(htmlspecialchars($error_flag, ENT_QUOTES)); print('<br />');
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <h1>
          <span class="icon-title fi-clipboard-pencil"></span><?php echo $title; ?>
        </h1>

        <?php
        if (isset($error_flag)){
            if(strlen($error_flag) > 0){
                echo $error_flag;
            }
        }
        ?>

        <?php $data_user = $user->fetch(PDO::FETCH_ASSOC); ?>


        <div class="sweetbox-pad">

            <!-- FORM -->
            <form id="moduleForm" name="form1" method="post" action="?controller=panel&amp;action=userEdit">

              <div class="row">

                <div class="row">
                  <div class="medium-3 columns">
                    <label for="name_user" class="text-right middle">Nombre de Usuario</label>
                  </div>
                  <div class="medium-9 columns">
                    <input type="text" id="name_user" name="name_user" value="<?php echo $data_user['name_user'];?>" />
                  </div>
                </div>

                <?php if($session->id_profile == 1): ?>
                <div class="row">
                  <div class="medium-3 columns">
                    <label for="cboprofiles" class="text-right middle">Perfil</label>
                  </div>
                  <div class="medium-9 columns">
                    <?php
                    echo "<select id='cboprofiles' name='cboprofiles'>\n";
                    while($row = $profiles->fetch(PDO::FETCH_ASSOC))
                    {
                        if($row['id_profile'] == $data_user['id_profile'])
                        {
                            echo "<option value='$row[id_profile]' selected>$row[label_profile]</option>\n";
                        }
                        else
                        {
                            echo "<option value='$row[id_profile]' >$row[label_profile]</option>\n";
                        }
                    }
                    echo "</select>\n";
                    ?>
                  </div>
                </div>
                <?php endif; ?>

                <div class="row">
                  <div class="medium-3 columns">
                    <label for="pass_user_1" class="text-right middle">Ingrese nueva contraseña</label>
                  </div>
                  <div class="medium-9 columns">
                    <input type="password" name="pass_user_1" id="pass_user_1" />
                  </div>
                </div>

                <div class="row">
                  <div class="medium-3 columns">
                    <label for="pass_user_2" class="text-right middle">Repita contraseña</label>
                  </div>
                  <div class="medium-9 columns">
                    <input type="password" name="pass_user_2" id="pass_user_2" />
                  </div>
                </div>


                <div class="medium-1 medium-centered columns">
                  <button name="button" type="submit" class="button">Editar</button>
                </div>

                <input type="hidden" name="id_user" value="<?php echo $data_user['id_user']; ?>" />
                <input type="hidden" name="original_name_user" value="<?php echo $data_user['name_user']; ?>" />

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
