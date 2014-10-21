<?php
require('templates/header.tpl.php'); #session & header

#session
if($session->id_tenant != null && $session->id_user != null):

#privs
//if($session->privilegio > 0):
?>

<!-- JS & CSS -->
<style type="text/css" title="currentStyle">
    .table_left {}
    .table_right {
        margin-left: 70px;
    }
    td.middle {
        padding-bottom: 15px;
        text-align: left;
    }
    input.input_box, textarea.input_box, select.input_box{
        border: 1px solid #989898;
        border-radius: 4px
    }
    #dt_filtres table {
        /*float: left;*/
    }
    #dt_filtres input, #dt_filtres textarea, #dt_filtres select {
        margin-left: 5px;
        width: 155px;
        height: 20px;
    }
    #dt_filtres input.time_control {
        width: 80px;
        height: 30px;
    }formModule
    #dt_filtres textarea{
        width: 300px;
        height: 100px;
    }
    #dt_filtres td {
        text-align: left;
    }
    #dt_filtres {
        padding: 10px;
        /*height: 200px;*/
    }
</style>
<!-- END JS & CSS -->

</head>
<body>
    
    <?php require('templates/menu.tpl.php'); #banner & menu?>
    
    <!-- CENTRAL -->
    <div id="central">
    <div id="contenido">
        
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
        
        <p class="titulos-form"><?php echo $titulo; ?></p>
        
        <!-- FORM -->
        <div id="dt_filtres">
            <form id="moduleForm" name="form1" method="post" action="?controller=panel&amp;action=newUserAdd">
                <table class="table_left">
                    <tr>
                        <td>Nombre de Usuario</td>
                        <td><input class="input_box" type="text" id="name_user" name="name_user" value="<?php echo $name_user;?>" /></td>
                    </tr>
                    <tr>
                        <td>Perfil</td>
                        <td>
                            <?php
                            echo "<select class='input_box' id='cboprofiles' name='cboprofiles'>\n";
                            echo "<option value='noaplica' selected='selected'>Seleccione Perfil</option>\n";
                            while($row = $profiles->fetch(PDO::FETCH_ASSOC))
                            {
                                echo "<option value='$row[id_profile]' >$row[label_profile]</option>\n";
                            }
                            echo "</select>\n";
                            ?>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td>Contrase&ntilde;a</td>
                        <td><input class="input_box" type="password" name="password_nuevo_a" id="pass_user" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <input class="time_control" type="reset" value="CANCELAR" value="CANCELAR" />
                            &nbsp;&nbsp;
                            <input class="time_control" type="submit" value="CREAR" />
                        </td>
                    </tr>
                </table>
            </form>
            <div class="spacer"></div>
        </div>

    </div>
    </div>
    <!-- END CENTRAL -->

<?php
//endif; #privs
endif; #session

require('templates/footer.tpl.php');
?>
</body>