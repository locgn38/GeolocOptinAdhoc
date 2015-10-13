<?php
/**
 * Renderer
 * is the instance for each view ($this)
 * add utils function to this class
 */
class Renderer {

   private $_request = false;

   public function __construct($request) {
      $this->_request = $request;
   }


   public function render($viewPath) {
      if(file_exists($viewPath)) {
         require_once $viewPath;
         die;
      } else {
         throw new Exception('file ' . $viewPath . " not exist");
      }
   }

   public function waitParam($type, $key, $filtres) {
      return $this->_request->waitParam($type, $key, $filtres);
   }
}