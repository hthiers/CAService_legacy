<?php
/**
 * Pre Compiled SQL Query Object
 */
class SPDO extends PDO 
{
    private static $instance = null;
    
    #Support for charset set prior to PHP 5.3.6
    private static $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    ); 

    public function __construct() 
    {
        $opts = array(
            "charset" => "utf8"
        );
        
        $config = Config::singleton();
        parent::__construct('mysql:host=' . $config->get('dbhost') . ';dbname=' . $config->get('dbname'), $config->get('dbuser'), $config->get('dbpass'), self::$options);
        #parent::__construct('mysql:host=' . $config->get('dbhost') . ';dbname=' . $config->get('dbname'), $config->get('dbuser'), $config->get('dbpass'), $opts);
    }

    public static function singleton() 
    {
        if( self::$instance == null ) 
        {
            self::$instance = new self();
        }
        
        self::$instance->exec("set names utf8");
        
        return self::$instance;
    }
}
?>