<?php
include  "Includes/class.user.php";
include  "Includes/config.php";

if(isset($_POST['logout'])){
  if($user->Logout()){
    $user->redirect("index.php");
  }
}




?>

<!DOCTYPE html>
<html lang="en">
<head>   
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">  
    <link rel="stylesheet" href="css/stylesheet.css">
    <script src="https://kit.fontawesome.com/a1b10b23f2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/stylesheet.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-black py-4">
  <div class="container-fluid">
    <a class="navbar-brand text-light" href="#">LoggingSystem</a>
    <div class="headerdiv">
      
      <form method='post' class="">
      <?php
        
            if($user->checkloginstatus()){ 
      ?>

        <a class="btn btn-light" href="account.php">Account</a>

      <?php 
      if($user->checkuserole(20)){
        echo "<a href='admin.php' class='m-1 btn btn-danger'>Admin Page</a>";
      }}
        if($user->checkloginstatus()){

      ?>

      <input type="submit" class=" btn btn-light text-right" id="button3" name="logout" value="Logout"><br>

      <?php 

      }

      ?>

      </form>
    </div>
  </div>
</nav>