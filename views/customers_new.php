<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>

<?php require_once('js_customers_new.php'); # JS ?>

</head>
<body>

<?php
require('templates/menu.tpl.php'); #banner & menu
?>
    <!-- CENTRAL -->
    <div class="row">

        <!-- DEBUG -->
        <?php
        if($debugMode)
        {
            print('<div id="debugbox">');
            print("tenant: ".$session->id_tenant.", user: ".$session->id_user."<br/>");
            print_r($titulo); print('<br />');
            #print_r($permiso_editar); print('<br />');
            print('</div>');
        }
        ?>
        <!-- END DEBUG -->

        <h1>
          <span class="icon-title fi-clipboard-pencil"></span><?php echo $titulo; ?>
        </h1>

        <div class="sweetbox-pad">

          <!-- FORM -->
          <form id="moduleForm" name="form1" method="post"  action="?controller=customers&amp;action=customersAdd">
            <!-- form container -->
            <div class="row">

              <div class="row">
                <div class="medium-3 columns">
                  <label for="customer_name" class="text-right middle">Nombre cliente</label>
                </div>
                <div class="medium-9 columns">
                  <input class="input_box" name="customer_name" type="text" id="customer_name" value="" />
                </div>
              </div>

              <div class="row">
                <div class="medium-3 columns">
                  <label for="customer_name" class="text-right middle">Descripción cliente</label>
                </div>
                <div class="medium-9 columns">
                  <input class="input_box" name="customer_detail" type="text" id="customer_detail" value="" />
                </div>
              </div>

              <div class="medium-1 medium-centered columns">
                <button name="button" type="submit" class="button">Guardar</button>
              </div>

            </div>
          </form>
        </div>

    </div>
    <!-- END CENTRAL -->

<?php
#endif; #privs
endif; #session
require('templates/footer.tpl.php');
?>
