<?php
/**
 * User
 *
 * stock infos in session
 *
 */
class User {

   public static $params = array();

   public static $_instance = null;

   public static function getInstance() {
      if(self::$_instance == null) {
         self::$_instance = new self();
      }
      return self::$_instance;
   }

   public function __construct() {
      session_start();
   }

   public static function isConnected() {
      return self::get('id');
   }

   /**
    * Auth the user
    * @param string $login
    * @param string $password
    * @return array
    */
   public static function auth($login, $password) {
      $self = self::getInstance();
      $DB = new Database();
      return $DB->auth($login, $password);
   }

   /**
    * get geometric info for user
    * @return array
    */
   public static function getGeom() {
      $self = self::getInstance();
      $DB = new Database();
      return $DB->getGeom(User::get('id'));
   }

   /**
    * destroy session
    */
   public static function logout() {
      session_destroy();
   }

   public static function log() {
//not implemented
   }

   /**
    * Set value in session
    * @param string $k
    * @param string $v
    */
   public static function set($k, $v) {
      $_SESSION[$k] = $v;
   }

   /**
    * get value from session
    * @param string $k
    * @return string $v
    */
   public static function get($k) {
      if(isset($_SESSION[$k])) {
         return $_SESSION[$k];
      } else {
         return false;
      }
   }

}