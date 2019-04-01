<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF8");
  header("Access-Control-Allow-Methods: POST");

  include_once '../../Conexion.php';
  include_once '../models/User.php';
  include_once '../models/UserGroupMap.php';

  $json=file_get_contents('php://input');
  $data=json_decode($json);

  /*validar que existan las variables con isset() posiblemente*/
  if(!isset($data->name) || !isset($data->username) || !isset($data->email)
  || !isset($data->password)){
    echo json_encode(array(
      "status"=>false,
      "message"=>"Error! Asegurese de introducir todos los datos (name, username,email,password)"
    ));
  }

  if(!isset($data->groups_id)){
    echo json_encode(array(
      "status"=>false,
      "message"=>"No se seleccionaron grupo(s) de usuario(s)"
    ));
  }

  $groups_id=$data->groups_id;

  if(empty($data->name) || empty($data->username) || empty($data->email)
  || empty($data->password) || count($groups_id)==0){
    http_response_code(404);
    echo json_encode(
      array(
        "message"=>"algun dato esta vacio",
        "status"=>false
      ));
  }else {
  //Conexion a Database
  $database=new Database();
  $conn=$database->getConnection();
  if($conn){
    $user=new User($conn);
    $user_groups=new UserGroupMap($conn);
    if($user->validarRegistro($data->username)>0){
      echo json_encode(
        array(
          "message"=>"el 'username' ya esta en uso",
          "status"=>false
        ));
    }else{
      if($user->insert($data)){
        //Tabla pivote, metodo propio
        $user_groups->insert($groups_id);

        http_response_code(200);
        echo json_encode(
          array(
            "message"=>"Registro insertado con exito",
            "status"=>true
          ));
      }else{
        http_response_code(404);
        echo json_encode(
          array(
            "message"=>"Algo salio mal",
            "status"=>false
          ));
      }
    }
  }
}
 ?>
