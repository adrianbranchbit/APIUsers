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
    /*Leer los usuarios registrados*/
    $query="SELECT id, name, username, email, password FROM ". $this->table_name;
    $stmt=$this->conn->prepare($query);

    $stmt->execute();
    return $stmt;
  }

  public function insert($User)
  {
    /*Insertar un usuario*/
    $query="INSERT INTO ".$this->table_name."
    (name, username,email, password, registerDate) VALUES
    (:name, :username,:email,:password,:registerDate)";

    $stmt=$this->conn->prepare($query);

    //Preparar datos, porque la funcion bindParam solo acepta variables
    $password=password_hash($User->password,PASSWORD_DEFAULT);
    $fechaRegistro=date('Y-m-d H:i:s');

    $stmt->bindParam(":name",$User->name);
    $stmt->bindParam(":username",$User->username);
    $stmt->bindParam(":email",$User->email);
    $stmt->bindParam(":password",$password);
    $stmt->bindParam(":registerDate",$fechaRegistro);

    return $stmt->execute();
  }

  public function insertUsuario_Group($groups_id)
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
      $query="INSERT INTO site_user_usergroup_map VALUES (:user_id, :group_id)";
      $stmt=$this->conn->prepare($query);

      $stmt->bindParam(":user_id",$user_id);
      $stmt->bindParam(":group_id",$group_id->group_id);

      $stmt->execute();
    }
  }

  public function login($username,$password)
  {
    $contador=0;
    /*Buscar que el username exista*/
    $query="SELECT * from ".$this->table_name." where username= :username";

    $stmt=$this->conn->prepare($query);
    $stmt->bindParam(":username",$username);
    $stmt->execute();

    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
      /*Si existe el username, verificar contraseña hasheada*/
      //$row['name'] to just $name only
      if(password_verify($password,$row['password'])){
            extract($row);
            //Retornar el id del usaurio
            return $id;
      }
    }
    return null;
  }

  public function getGroup($idUser)
  {
    /*Seleccionar todos los grupos a los que pertenece un usuario*/
    $query="SELECT m.group_id
    FROM site_user_usergroup_map as m JOIN site_users as u
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

  public function validarRegistro($username)
  {
    /*Valida que no exista un username identico,
    al momento de registrarse*/
    $query="SELECT COUNT(*) FROM ".$this->table_name."
    WHERE username=:username";
    $stmt=$this->conn->prepare($query);
    $stmt->bindParam(":username",$username);
    $stmt->execute();
    //Retorna el resultado del query 1 o 0
    return $stmt->fetchColumn();
  }


}

/*PRUEBAS UNITARIAS
//Conexion a Database
include_once '../../Conexion.php';
$database=new Database();

$conn=$database->getConnection();


$user=new User($conn);
$contador=$user->getGroup(101);
*/
 ?>
