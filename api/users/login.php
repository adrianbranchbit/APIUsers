<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF8");
  header("Access-Control-Allow-Methods: POST");
//  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

  include_once '../../Conexion.php';
  include_once '../models/User.php';
  include_once '../models/UserGroupMap.php';

  $json=file_get_contents('php://input');
  $data=json_decode($json);

  if(!isset($data->username) || !isset($data->password)){
    echo json_encode(
      array(
        "message"=>"Asegurese de ingresar todos los datos (username & passwors)",
        "status"=>false
      ));
  }else{
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
      $user_groups=new UserGroupMap($conn);
      $idUser=$user->login($username,$password);

      if($idUser!=null){
        http_response_code(200);
        echo json_encode(
          array(
            "id"=>$idUser,
            "username"=>$username,
            "usergroups"=>$user_groups->getGroups($idUser),
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
}
 ?>
