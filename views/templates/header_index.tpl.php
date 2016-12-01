<?php
#System vars
$config = Config::singleton();
$constants = Constants::singleton();
$rootPath = $config->get('rootPath');
$debugMode = $config->get('debug');
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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    
    <title>Control tiempos de trabajo - v<?php echo $constants->getSysVersion(); ?></title>
    
    <style type="text/css" title="currentStyle">
		@import "views/css/estilo.css";
		@import "views/css/texto.css";
                @import "views/css/reset-min.css";
                @import "views/css/menu2.css";
	</style>
        
    <script type="text/javascript" language="javascript" src="views/lib/jquery.js"></script>
