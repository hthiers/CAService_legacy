<?php 
/**
 * Utilities Class 
 */
class Utils
{
    function __construct() 
    {
    }
    
    /**
     * Avoid SQL Injection
     * @param string $string
     * @return string 
     */
    public static function cleanQuery($string)
    {
        if(get_magic_quotes_gpc())  // prevents duplicate backslashes
        {
            $string = stripslashes($string);
        }
        if (phpversion() >= '4.3.0')
        {
            $string = mysql_real_escape_string($string);
        }
        else
        {
            $string = mysql_escape_string($string);
        }
        return $string;
    }

    /**
    * Devuelve la diferencia entre 2 fechas según los parámetros ingresados
    * @author Gerber Pacheco
    * @param string $fecha_principal Fecha Principal o Mayor
    * @param string $fecha_secundaria Fecha Secundaria o Menor
    * @param string $obtener Tipo de resultado a obtener, puede ser SEGUNDOS 'S', MINUTOS 'M', HORAS 'H', DIAS 'D', SEMANAS 'W'
    * @param boolean $redondear TRUE retorna el valor entero, FALSE retorna con decimales
    * @return int Diferencia entre fechas
    */
    public static function diffDates($fecha_principal, $fecha_secundaria, $obtener = 'S', $redondear = false){
        $f0 = strtotime($fecha_principal);
        $f1 = strtotime($fecha_secundaria);
        
        if ($f0 < $f1) { $tmp = $f1; $f1 = $f0; $f0 = $tmp; }
            $resultado = ($f0 - $f1);

        switch ($obtener) {
            default: break;
            case "M"   :   $resultado = $resultado / 60;   break;
            case "H"     :   $resultado = $resultado / 60 / 60;   break;
            case "D"      :   $resultado = $resultado / 60 / 60 / 24;   break;
            case "W"   :   $resultado = $resultado / 60 / 60 / 24 / 7;   break;
        }
        
        if($redondear){
            $resultado = round($resultado);
        }

        return $resultado;
    }
    
    public static function sumDates($fecha_principal, $fecha_secundaria, $obtener = 'S', $redondear = false){
        $f0 = strtotime($fecha_principal);
        $f1 = strtotime($fecha_secundaria);
        
        $resultado = ($f0 + $f1);

        switch ($obtener) {
            default: break;
            case "M"   :   $resultado = $resultado / 60;   break;
            case "H"     :   $resultado = $resultado / 60 / 60;   break;
            case "D"      :   $resultado = $resultado / 60 / 60 / 24;   break;
            case "W"   :   $resultado = $resultado / 60 / 60 / 24 / 7;   break;
        }
        
        if($redondear){
            $resultado = round($resultado);
        }

        return $resultado;
    }
    
    public static function formatTimeSeconds($time){
        $hours = substr($time, 0, 2);
        $minutes = substr($time, 3, 2);
        $seconds = substr($time, 6, 2);
        
        $time_seconds = $hours*3600 + $minutes*60 + $seconds;
        
        return $time_seconds;
    }
    
    /**
     * Get hh:mm:ss format time from seconds
     * @param int $seconds
     * @return string time
     */
    public static function formatTime($seconds){
        $time = "";
        // avoid dummi values
        if($seconds != "n/a"){
            $hours = floor($seconds / 3600);
            $mins = floor(($seconds - ($hours*3600)) / 60);
            $secs = floor(($seconds - ($hours*3600)) % 60);

            if($hours < 10){
                $hours = "0".$hours;
            }
            if($mins < 10){
                $mins = "0".$mins;
            }
            if($secs < 10){
                $secs = "0".$secs;
            }
            
            $time = $hours.":".$mins.":".$secs;
        }
        else{
            $time = "n/a";
        }

            return $time;           
    }
    
    /**
     * Get months or month of year by number of month (null for all)
     * @param int $num_month
     * @return string array
     */
    public static function getMonths($num_month = null){
        $monthsArray = array(
            1 => 'Enero'
            , 2 => 'Febrero'
            , 3 => 'Marzo'
            , 4 => 'Abril'
            , 5 => 'Mayo'
            , 6 => 'Junio'
            , 7 => 'Julio'
            , 8 => 'Agosto'
            , 9 => 'Septiembre'
            , 10 => 'Octubre'
            , 11 => 'Noviembre'
            , 12 => 'Diciembre');
        
        if($num_month == null){
            return $monthsArray;
        }
        else{
            return $monthsArray[$num_month];
        }
    }
    
    /**
     * Save a PHPExcel generated file to system temp folder prior to export
     * @param String $objWriter
     * @param String $path
     */
    public static function SaveViaTempFile($objWriter, $path){
        $filePath = ''.$path.'' . rand(0, getrandmax()) . rand(0, getrandmax()) . ".tmp";
        $objWriter->save($filePath);
        readfile($filePath);
        unlink($filePath);
    }
    
    /**
    *
    * Generate 128 bits of random data
    * @see http://tools.ietf.org/html/rfc4122#section-4.4
    * @return string
    *
    */
   public static function guidv4()
   {
       $data = openssl_random_pseudo_bytes( 16 );
       $data[6] = chr( ord( $data[6] ) & 0x0f | 0x40 ); // set version to 0100
       $data[8] = chr( ord( $data[8] ) & 0x3f | 0x80 ); // set bits 6-7 to 10

       return vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $data ), 4 ) );
   }
}