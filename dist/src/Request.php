<?php
/**
 * Request
 * control request url && params
 *
 */
class Request {

   const PARAM_NOT_NULL = "NOT_NULL";

   const PARAM_ENCRYPTED = "ENCRYPTED";

   const PARAM_TYPE_INTEGER = "TYPE_INTEGER";

   const PARAM_TYPE_TPH = "TYPE_TPH";

   public $server = array();

   public $get = array();

   public $post = array();

   private $requestUri = "";

   public $viewPath = "";

   public function __construct() {
      $this->server = $_SERVER;
      $this->get = $_GET;
      $this->post = $_POST;
      if (preg_match("#(.*)\\?.*#", $this->server['REQUEST_URI'], $matches)) {
         $this->requestUri = $matches[1];
      } else {
         $this->requestUri = $this->server['REQUEST_URI'];
      }
   }

   /**
    * set the view path from the requesturi
    * @return string
    */
   public function dispatch() {
      // routage
      switch ($this->requestUri) {
         case "/pos":
         case "/trace":
            $this->viewPath = APP::WEBROOT . $this->requestUri . ".php";
            break;
         case "/log":
            $this->viewPath = APP::WEBROOT . "/log/index.php";
            Break;
         case "/js/restpostgis/v1/ws_geo_attributequery.php":
         case "/log/index.php":
         case "/log/ajaxLogin.php":
         case "/log/home.php":
         case "/log/logout.php":
         case "/sms/envoi_sms.php":
         case "/traitement/success.php":
            $this->viewPath = APP::WEBROOT . $this->requestUri;
            break;
         default:
            if (! User::isConnected()) {
               $this->viewPath = APP::WEBROOT . "/log/index.php";
            } else {
               $this->viewPath = APP::WEBROOT . "/APIE.php";
            }
            break;
      }
      return $this->viewPath;

   }

   /**
    * Control on get or post message
    *
    * add here the type control you want in the switch case
    *
    * @param string $type
    * @param string $key
    * @param array $aFiltre
    * @throws Exception
    * @return string
    */
   public function waitParam($type, $key, $aFiltre) {
      if (strtolower($type) == "post") {
         $gp = $this->post;
      } else {
         $gp = $this->get;
      }
      if (isset($gp[$key])) {
         foreach ($aFiltre as $filtre) {
            switch ($filtre) {
               case self::PARAM_NOT_NULL:
                  if (is_null($gp[$key]) || ($gp[$key] == "")) {
                     throw new Exception("le paramètre " . $key . " ne doit pas être null");
                  }
                  break;
               case self::PARAM_ENCRYPTED:
                  $gp[$key] = md5($gp[$key]);
                  break;
               case self::PARAM_TYPE_INTEGER:
                  if (! is_numeric($gp[$key])) {
                     throw new Exception("le paramètre " . $key . " doit être de type entier");
                  }
                  break;
               case self::PARAM_TYPE_TPH:
                  if (! preg_match("##", $gp[$key])) {
                     throw new Exception("le paramètre " . $key . " doit correspondre à un numéro de téléphone");
                  }
                  break;
            }
         }
         return $gp[$key];
      } else {
         throw new Exception("le paramètre " . $key . " ne figure pas en " . $type);
      }

   }

}