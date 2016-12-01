<!-- NO SCRIPT WARNING -->
<noscript>
<div>
    <h4>¡Espera un momento!</h4>
    <p>La página que estás viendo requiere JavaScript activado.
    Si lo has deshabilitado intencionalmente, por favor vuelve a activarlo o comunicate con soporte.</p>
</div>
</noscript>

<?php
$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
?>

<div class="top-bar">
    <div class="top-bar-title">
        <li class="menu-text">Control v<?php echo $constants->getSysVersion(); ?></li>
    </div>
    <div class="top-bar-left">
        <?php
        include 'libs/Menu.php';
        $menu = new Menu();
        $menu->loadMainMenu($session);
        ?>
    </div>
    <div class="top-bar-right">
        <ul class="menu">
            <li>
                <a href="?controller=Panel&amp;action=editUserForm&user_id=<?php echo $session->id_user;?>">
                    <span class="fi-torso-business"></span>
                    <?php echo $session->name_user;?>
                </a>
            </li>
            <li>
                <a href="?controller=users&amp;action=logOut">
                    <span class="fi-power"></span>
                </a>
            </li>
        </ul>
    </div>
</div>