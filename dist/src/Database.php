<?php
/**
 * PDO connexion
 *
 */
class Database {

   private $config = false;

   private $connexion = false;

   public static $_instance = null;

   public static function getInstance() {
      if (self::$_instance == null) {
         self::$_instance = new self();
      }
      return self::$_instance;
   }

   /**
    * Init connexion pgsql
    * @throws Exception
    */
   public function __construct() {
      try {
         $this->config = APP::getInstance()->config['database'];
         $dsn = "pgsql:host=" . $this->config['server'] . " dbname=" . $this->config['dbname'] . " user=" . $this->config['username'] . " password=" . $this->config['password'];
         $this->connexion = new PDO($dsn, $this->config['username'], $this->config['password']);
      } catch (PDOException $e) {
         throw new Exception($e->getMessage());
      }
   }

   /**
    * execute select query
    * @param string $select
    * @param array $bindValues
    * @throws Exception
    * @return unknown|boolean
    */
   private function select($select, $bindValues) {
      try {
         $sth = $this->connexion->prepare($select);
         $success = $sth->execute($bindValues);
         if (! $success) {
            throw new Exception($sth->errorInfo()[2] . ":" . $select);
         }
         $result = $sth->fetchAll(PDO::FETCH_CLASS);
         if ($result && $sth->rowCount() == 1) {
            return $result[0];
         } else {
            return false;
         }
      } catch (PDOException $e) {
         throw new Exception($e->getMessage());
      }
   }

   /**
    * exceute update query
    * @param string $update
    * @param array $bindValues
    * @throws Exception
    * @return integer rowcount or false
    */
   private function update($update, $bindValues) {
      try {
         $sth = $this->connexion->prepare($update);
         $success = $sth->execute($bindValues);
         if (! $success) {
            throw new Exception($sth->errorInfo()[2]);
         }
         $result = $sth->rowCount();
         return $result;
      } catch (PDOException $e) {
         throw new Exception($e->getMessage());
      }
   }

   /**
    * execute query insert
    * @param string $insert
    * @param array $bindValues
    * @throws Exception
    * @return integer id created or false
    */
   private function insert($insert, $bindValues) {
      try {
         $sth = $this->connexion->prepare($insert);
         $success = $sth->execute($bindValues);
         if (! $success) {
            throw new Exception($sth->errorInfo()[2]);
         }
         $id = $this->connexion->lastInsertId("smsloc_id_seq");
         return $id;
      } catch (PDOException $e) {
         throw new Exception($e->getMessage());
      }
   }

   /**
    * Get instance of PDO
    * @return object
    */
   public function getConnexion() {
      return $this->connexion;
   }

   /**
    * Authentify the user
    * @param string $login
    * @param string $password
    * @return array infos user
    */
   public function auth($login, $password) {
      $select = "SELECT * FROM utilisateurs WHERE code=? and pwd=?";
      $values = array(
            $login,
            $password
      );
      return $this->select($select, $values);
   }

   /**
    * get geometric infos for user
    * @param integer $id
    * @return array
    */
   public function getGeom($id) {
      $select = "SELECT st_x(geom) as x, st_y(geom) as y, nom, zoom FROM utilisateurs WHERE id=?";
      $values = array(
            $id
      );
      return $this->select($select, $values);
   }

   /**
    * Add new row for smsloc
    * @param string $unite
    * @param string $msg
    * @param string $lang
    * @param string $tel
    * @param string $val
    * @return integer id
    */
   public function addSmsLock($unite, $msg, $lang, $tel, $val) {
      $insert = "INSERT INTO public.smsloc (id, code, msg, lang, tel, val) VALUES (DEFAULT, ?, ?, ?, ?, ?)";
      $values = array(
            $unite,
            $msg,
            $lang,
            $tel,
            $val
      );
      return $this->insert($insert, $values);
   }

   /**
    * Update row, set status=envoye
    * @param integer $id
    * @param string $hash
    * @return rowcount updated
    */
   public function setSmsSend($id, $hash) {
      $update = "UPDATE public.smsloc SET hash=?, statut='ENVOYE' WHERE id=?";
      $values = array(
            $hash,
            $id
      );
      return $this->update($update, $values);
   }



}