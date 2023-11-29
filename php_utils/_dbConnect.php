<?php
$server = "localhost";
$uname = "root"; //your username here
$pword = "";// your password here
$dbname="OLMS";

$conn = mysqli_connect($server, $uname, $pword, $dbname);

// if(!$conn){
//     // die("Connection failed: ".mysqli_error());
//     $connection_status = "Connection not successfully established";
// }else{
//     echo "Connection successfull";
// }
?>