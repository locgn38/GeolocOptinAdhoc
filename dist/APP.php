<?php
/**
 * Singleton for application
 *
 * init config, request routes
 *
 * @author
 *
 *
 */
class APP {

   public static $_instance = null;

   private $_loader = null;

   private $_request = null;

   public $config = array();
   public $_user = null;

   const WEBROOT = __DIR__;

   /**
    * Get singleton
    * @return APP
    */
   static public function getInstance() {
      if (self::$_instance == null) {
         self::$_instance = new self();
      }
      return self::$_instance;
   }

   public function __construct() {
      require_once 'src/Loader.php';
      $this->_loader = new Loader();
      $this->_request = new Request();
      $this->config = require_once self::WEBROOT . "/config/config.php";
      $this->_renderer = new Renderer($this->_request);
      $this->_user = User::getInstance();
   }

   /**
    * Define renderer
    */
   static public function init() {
      $self = self::getInstance();
      $viewPath = $self->_request->dispatch();
      $self->_renderer->render($viewPath);
   }

   /**
    * Call view
    */
   static public function run() {
      $self = self::getInstance();
      $self->_renderer->render();
   }

   /**
    * get instance of user (session values)
    * @return User
    */
   static public function getUser() {
      $self = self::getInstance();
      return $self->_user;
   }


}