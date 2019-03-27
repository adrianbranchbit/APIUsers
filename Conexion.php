<?php
class Database{
 //Credenciales
  private $host="localhost";
  private $db_name="branchbi_sitedb";
  private $username="root";
  private $password="";
  public $conn;

  //Obtener la conexion
  public function getConnection()
  {
    $this->conn=null;
    try {
      $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name,$this->username, $this->password);
      $this->conn->exec("set names utf8");
    } catch (PDOException $exception) {
      echo "Connection error: ".$exception->getMessage();
    }
    return $this->conn;
  }
}

/*
$db=new Database();
$conn=$db->getConnection();
if($conn){
  echo "conectado";
}*/
?>
