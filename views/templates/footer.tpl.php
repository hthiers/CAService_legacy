<?php
#session
if($session->id_tenant != null && $session->id_user != null):

#privs
#if($session->privilegio > 0):
?>
<div id="info">
  <p class="Estilo1">
      Control tiempos de trabajo - v<?php echo $constants->getSysVersion(); ?>
  </p>
  <p class="Estilo1">
       Gomez & Riesco
  </p>
</div>

<script type="text/javascript" language="javascript" src="views/lib/vendor/what-input.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/vendor/foundation.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/app.js"></script>

</body>
</html>
<?php
//else:
//	echo '<script language="JavaScript">alert("Usted No Posee Privilegios Suficientes "); document.location = "'.$rootPath.'"</script>';
//endif; #privileges
else:
    session_destroy();
    echo '<script language="JavaScript">alert("Debe Identificarse"); document.location = "'.$rootPath.'"</script>';
endif; #session