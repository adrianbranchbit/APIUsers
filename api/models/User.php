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

  public function insert($User)
  {
    $query="INSERT INTO ".$this->table_name."
    (name, username,email, password, registerDate) VALUES
    (:name, :username,:email,:password,:registerDate)";

    $stmt=$this->conn->prepare($query);

    //Preparar datos, porque la funcion bindParam solo acepta variables

    $password=password_hash($User['password'],PASSWORD_DEFAULT);
    $fechaRegistro=date('Y-m-d H:i:s');

    $stmt->bindParam(":name",$User['name']);
    $stmt->bindParam(":username",$User['username']);
    $stmt->bindParam(":email",$User['email']);
    $stmt->bindParam(":password",$password);
    $stmt->bindParam(":registerDate",$fechaRegistro);

    return $stmt->execute();
  }

  public function insertUsuario_Group($group_id)
  {
    //Buscar ultimo registro insertado
    $query="SELECT id
            FROM site_users
            ORDER BY id DESC limit 1";

    $stmt=$this->conn->prepare($query);
    $stmt->execute();
    $result=$stmt->fetch(PDO::FETCH_ASSOC);
    $user_id=$result['id'];

    //Insertar en site_user_usergroup_map tabla pivote
    $query="INSERT INTO site_user_usergroup_map VALUES (:user_id, :group_id)";
    $stmt=$this->conn->prepare($query);

    $stmt->bindParam(":user_id",$user_id);
    $stmt->bindParam(":group_id",$group_id);

    return $stmt->execute();
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

  public function validarRegistro($username)
  {
    $query="SELECT COUNT(*) FROM ".$this->table_name."
    WHERE username=:username";
    $stmt=$this->conn->prepare($query);
    $stmt->bindParam(":username",$username);
    $stmt->execute();
    //Retorna el resultado del query
    return $stmt->fetchColumn();
  }


}

/*PRUEBAS UNITARIAS
//Conexion a Database
include_once '../../Conexion.php';
$database=new Database();

$conn=$database->getConnection();


$user=new User($conn);
$contador=$user->insertUsuario_Group(1);
echo $contador['id'];
*/
 ?>
