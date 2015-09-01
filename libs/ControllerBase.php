<?php
/**
 * Base Controller
 */
abstract class ControllerBase {

    protected $view;
    protected $root;
    protected $utils;
    protected $errorMessage;
    protected $timezone;
    protected $constants;

    function __construct()
    {
        $this->view = new View();
        $this->utils = new Utils();
        $this->errorMessage = new ErrorMessage();
        $this->constants = new Constants();

        $config = Config::singleton();
        $this->root = $config->get('rootPath');
        $this->timezone = $config->get('timezone');
    }
}
?>