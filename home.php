<?php 
include "header.php";

if(!$user->checkloginstatus()){
    $user->redirect("index.php");
}

if(!isset($_SESSION)){
    session_start();
}

    echo "<p class='text-center'>Welcome {$_SESSION['user_id']}</p>";

?>

