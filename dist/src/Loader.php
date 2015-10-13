<?php
/**
 * Autoload class
 *
 */
class Loader {

   public static $loaded = array();

   public function __construct() {
      spl_autoload_register(function ($sClass) {
         Loader::load($sClass);
      });
   }

   /**
    * Define here the pattern for require class
    * @param string $sClass
    * @throws Exception
    */
   static public function load($sClass) {
      if(!in_array($sClass, self::$loaded)) {
         if(preg_match("#^thecallr.*$#i", $sClass)) {
            $path = APP::WEBROOT . "/js/thecallR/src/" . $sClass . ".php";
         } else {
            $path = APP::WEBROOT . "/src/" . $sClass . ".php";
         }
         if(file_exists($path)) {
            self::$loaded[] = $sClass;
            include_once $path;
         } else {
            throw new Exception("la classe appelée n'a pas été trouvée au chemin: " . $path);
         }
      } else {
         //already included
      }
   }
}
