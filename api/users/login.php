<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  include_once '../../Conexion.php';
  include_once '../models/User.php';

  $json=file_get_contents('php://input');
  $data=json_decode($json);

  $username=$data->username;
  $password=$data->password;

  if(empty($username) || empty($password)){
    http_response_code(404);

    echo json_encode(
      array(
        "message"=>"Usuario o contrasenia vacia",
        "status"=>false
      ));
  }else{
  //Conexion a Database
  $database=new Database();

  $conn=$database->getConnection();

  if($conn){
    $user=new User($conn);
    $dataUser=$user->login($username,$password);
    //$num=$stmt->rowCount();
    $num=count($dataUser);
    if($num>0){
      $users_array=array();
      $users_array["data"]=array();

      $idUser=$dataUser['id'];

      $stmt=$user->getGroup($idUser);
      $row=$stmt->fetch(PDO::FETCH_ASSOC);


      http_response_code(200);
      echo json_encode(
        array(
          "id"=>$idUser,
          "usergroup"=>$row,
          "message"=>"Inicio de sesion correcto ",
          "status"=>true)
      );
    }else{
      http_response_code(404);

      echo json_encode(
        array(
          "message"=>"Usuario o contrasenia invalida",
          "status"=>false
        )
      );
    }
  }
}
 ?>
