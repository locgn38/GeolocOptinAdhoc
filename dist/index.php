<?php
/**
 * all request cross here
 */
require_once 'APP.php';

try {
   /**
    * Define request routes config renderer
    */
   APP::init();
   //dispatch
   APP::run();
} catch (Exception $e) {
   //there are no erreur.html here, so die
   die($e->getMessage());
}
?>