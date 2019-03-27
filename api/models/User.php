<?php
class User{
  private $conn;
  private $table_name="site_users";

  public $id;
  public $name;
  public $username;
  public $email;
  public $password;

  public function __construct($db)
  {
    $this->conn=$db;
  }

  public function read()
  {
    $query="SELECT id, name, username, email, password FROM ". $this->table_name;
    $stmt=$this->conn->prepare($query);

    $stmt->execute();
    return $stmt;
  }

  public function login($username,$password)
  {
    $contador=0;
    $query="SELECT * from ".$this->table_name." where username= :username";
    //$query="SELECT * FROM ".$this->table_name." where username= :username and password=:password";
    $stmt=$this->conn->prepare($query);
    $stmt->execute(array(
      ":username"=>$username
      //":password"=>$password
    ));

    $user_item=array();
    $users_array["data"]=array();

    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
      //$row['name'] to just $name only
      if(password_verify($password,$row['password'])){
            extract($row);
            $user_item=array(
              'id'=>$id
            );
      }
    }
    return $user_item;
  }

  public function getGroup($idUser)
  {
    $query="SELECT m.group_id
    FROM site_user_usergroup_map as m JOIN site_users as u
    ON m.user_id = u.id WHERE u.id = :idUser";
    $stmt=$this->conn->prepare($query);
    $stmt->execute(array(
      ':idUser'=>$idUser
    ));

    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    extract($row);
    $idGroup=$group_id;

    $query="SELECT id, title FROM site_usergroups
    WHERE id=:idGroup";

    $stmt=$this->conn->prepare($query);
    $stmt->execute(array(
      ':idGroup'=>$idGroup
    ));

    return $stmt;
  }
}
 ?>
