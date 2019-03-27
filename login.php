<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF8");

  include_once '../../Conexion.php';
  include_once '../models/User.php';

  $json=file_get_contents('php://input');

  return $json;

 ?>
