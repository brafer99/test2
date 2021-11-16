
<?php


session_start();
  if(!isset($_SESSION['valida_usuario'])){
    header("Location:index.php");
  }
  else{
    if($_SESSION['valida_usuario']=="ok"){
      $nombre_usuario=$_SESSION["nombre_usuario"];
    }
  }



?>



<!doctype html>
<html lang="en">
  <head>
    <title>Title</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="../estilos/style.css">
  </head>
  <body>


    <nav class="navbar navbar-expand navbar-light bg-light">
        <div class="nav navbar-nav">
        </div>
    </nav>
  <div class="container">
      <div class="row">