<?php
require('templates/header_index.tpl.php');

if(isset($_GET['errorCode'])){
    $error_code = $_GET['errorCode'];
}
?>

<!-- AGREGAR JS & CSS AQUI -->
<script type="text/javascript" charset="utf-8">
$(document).ready(function(){
    $("input#txtuser").focus();
});
</script>

</head>

<body>

    <noscript>
    <div>
        <h4>¡Espera un momento!</h4>
        <p>La página que estás viendo requiere JavaScript.
        Si lo has deshabilitado intencionalmente, por favor vuelve a activarlo o comunicate con soporte.</p>
    </div>
    </noscript>

    <!-- CABECERA -->
	<!-- <div class="top-bar"></div> -->
    <!-- END CABECERA -->

    <!-- CENTRAL -->
    <div class="row">

      <!-- <p class="submenu">
          Versi&oacute;n <?php #echo $constants->getSysVersion(); ?>
      </p> -->

      <div class="medium-6 medium-centered large-4 large-centered columns">
        <h2 class="text-center pushed-up"><a href="<?php echo $_SERVER['PHP_SELF'];?>">Control</a></h2>
        <p class="text-center">
            Versi&oacute;n <?php echo $constants->getSysVersion(); ?>
        </p>
      </div>

      <?php if(isset($error) && $error == 1){
          echo "<div id='errorbox_failure' class='callout warning' data-closable>\n";
            echo "Usuario o contraseña inválido!\n";
            echo "<button class='close-button' aria-label='Dismiss alert' type='button' data-close>\n";
              echo "<span aria-hidden='true'>&times;</span>\n";
            echo "</button>\n";
          echo "</div>\n";
      }
      ?>

      <div class="sweetbox-centered medium-6 medium-centered large-4 large-centered columns">

          <form id="form1" name="form1" method="post" action="?controller=Users&action=logIn">
            <h4 class="text-center">Inicia sesión</h4>

            <label>Usuario
              <input name="txtusername" type="text" class="bien" id="txtuser" size="50" />
            </label>

            <label>Contraseña
              <input name="txtpassword" type="password" class="bien" id="txtpass" size="50" />
            </label>

            <button type="submit" name="button" class="button expanded">Entrar</button>
          </form>

      </div>
    </div>
    <!-- END CENTRAL -->

<!-- FOOTER -->
<?php require('templates/footer_index.tpl.php'); ?>
<!-- END FOOTER -->
