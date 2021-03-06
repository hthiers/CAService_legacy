<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Content-Type: text/html; charset=UTF-8");

//general vars
$session = FR_Session::singleton();
$constants = Constants::singleton();

#system vars for view level
$config = Config::singleton();
$rootPath = $config->get('rootPath');
$debugMode = $config->get('debug');

#session vars
if($session->id_tenant != null && $session->id_user != null):

$navegador = $_SERVER['HTTP_USER_AGENT'];
$navegador = substr($navegador,25,8);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="cache-control" content="no-store" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />

<title>Control tiempos de trabajo - v<?php echo $constants->getSysVersion(); ?></title>

<!--<link rel="stylesheet" href="views/css/custom-theme-blue/jquery-ui-1.8.23.custom.css"></link>-->
<link rel="stylesheet" href="views/css/estilo.css"></link>
<link rel="stylesheet" href="views/css/reset-min.css"></link>
<link rel="stylesheet" href="views/css/datatable.css"></link>
<link rel="stylesheet" href="views/css/select2.css"></link>
<link rel="stylesheet" href="views/css/foundation.css"></link>
<link rel="stylesheet" href="views/css/jquery-ui-datepicker-custom.css"></link>
<link rel="stylesheet" href="views/css/jquery-ui-timepicker-custom.css"></link>
<link rel="stylesheet" href="views/css/icons/foundation-icons.css"></link>
<link rel="stylesheet" href="views/css/app.css"></link>

<script type="text/javascript" language="javascript" src="views/lib/jquery.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery.validate.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery.validate.messages.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery-ui.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/utils.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/jquery.timepicker.min.js"></script>
<script type="text/javascript" language="javascript" src="views/lib/select2.js"></script>

<?php
endif; #session
