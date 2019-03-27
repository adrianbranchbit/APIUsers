<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF8");

  include_once '../../Conexion.php';
  include_once '../models/User.php';

  //Conexion a Database
  $database=new Database();

  $conn=$database->getConnection();

  if($conn){
    $user=new User($conn);

    $stmt=$user->read();
    $num=$stmt->rowCount();

    if($num>0){
      $users_array=array();
      $users_array["data"]=array();

      while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
        //$row['name'] to just $name only
        extract($row);

        $user_item=array(
          'id'=> $id,
          'name'=>$name,
          'username'=>$username,
          'email'=>$email,
          'password'=>$password
        );

        array_push($users_array["data"],$user_item);
      }

      http_response_code(200);

      echo json_encode($users_array);
    }else{
      http_response_code(404);

      echo json_encode(
        array("message"=>"Ningun usuario registrado")
      );
    }


  }


 ?>
