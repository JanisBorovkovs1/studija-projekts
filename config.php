<?php
session_start();
$user = $_POST['login'];
$parole = $_POST['pass'];
$parole = SHA1($parole);
include('db.php');
$result = $mysqli->query("SELECT * FROM jb_users WHERE username='$user' AND password='$parole'");
if (mysqli_num_rows($result) > 0){

    while ($row = $result->fetch_assoc()){
        $_SESSION['id'] = $row['id_users'];
        }
    header("Location: next.html");
    }
else{
    header("Location: index.html");
}


?>