<?php
class UserGroupMap{
  private $conn;
  private $table_name="site_user_usergroup_map";

  public $user_id;
  public $group_id;

  public function __construct($db)
  {
    $this->conn=$db;
  }

  public function read()
  {
    /*Leer los registros*/
    $query="SELECT * FROM ". $this->table_name;
    $stmt=$this->conn->prepare($query);

    $stmt->execute();
    return $stmt;
  }

  public function insert($groups_id)
  {
    //Buscar ultimo registro insertado
    $query="SELECT id
            FROM site_users
            ORDER BY id DESC limit 1";

    $stmt=$this->conn->prepare($query);
    $stmt->execute();

    $result=$stmt->fetch(PDO::FETCH_ASSOC);
    $user_id=$result['id'];

    foreach ($groups_id as $group_id) {
      /*Insertar uno o varios registros
       en site_user_usergroup_map tabla pivote */
      $query="INSERT INTO ".$this->table_name." VALUES (:user_id, :group_id)";
      $stmt=$this->conn->prepare($query);

      $stmt->bindParam(":user_id",$user_id);
      $stmt->bindParam(":group_id",$group_id->group_id);

      $stmt->execute();
    }
  }

  public function getGroups($idUser)
  {
    /*Seleccionar todos los grupos a los que pertenece un usuario*/
    $query="SELECT m.group_id
    FROM ".$this->table_name." as m JOIN site_users as u
    ON m.user_id = u.id WHERE u.id = :idUser";

    $stmt=$this->conn->prepare($query);
    $stmt->bindParam(":idUser",$idUser);
    $stmt->execute();

    $groups=array();

    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
      extract($row);
      array_push($groups,$group_id);
    }

    $groups_title=array();

    /*Recorrer todos los grupos a los que pertenece el usuario
    para seleccionar el id y titulo de dicho grupo*/
    foreach ($groups as $idGroup) {
      $query="SELECT id, title FROM site_usergroups
      WHERE id=:idGroup";

      $stmt=$this->conn->prepare($query);
      $stmt->bindParam(":idGroup",$idGroup);
      $stmt->execute();

      $result=$stmt->fetch(PDO::FETCH_ASSOC);
      extract($result);
      $group_item=array(
        'id'=>$id,
        'title'=>$title
      );

      array_push($groups_title,$group_item);
    }
    //Retornar array con los grupos a los que pertenece el usuario
    return $groups_title;
  }
}
 ?>
