<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF8");
  header("Access-Control-Allow-Methods: POST");

  include_once '../../Conexion.php';
  include_once '../models/User.php';

  $json=file_get_contents('php://input');
  $data=json_decode($json);

  $group_id=$data->group_id;

  $User=array(
  'name'=>$data->name,
  'username'=>$data->username,
  'email'=>$data->email,
  'password'=>$data->password
  );

  if(empty($User['name']) || empty($User['username']) || empty($User['email']) || empty($User['password'])){
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
    if($user->validarRegistro($User['username'])>0){
      echo json_encode(
        array(
          "message"=>"el 'username' ya esta en uso",
          "status"=>false
        ));
    }else{
    if($user->insert($User)){
      //Tabla pivote
      $user->insertUsuario_Group($group_id);

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
