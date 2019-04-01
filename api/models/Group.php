<?php
class Group{
  private $conn;
  private $table_name="site_usergroups";

  public $id;
  public $parent_id;
  public $lft;
  public $rgt;
  public $title;

  public function __construct($db)
  {
    $this->conn=$db;
  }

  public function read()
  {
    /*Leer los usuarios registrados*/
    $query="SELECT * FROM ". $this->table_name;
    $stmt=$this->conn->prepare($query);

    $stmt->execute();
    return $stmt;
  }

  public function insert($User)
  {

  }
}
 ?>
