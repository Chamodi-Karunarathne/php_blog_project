<?php
$host="localhost";
$user="root";
$pass="";
$dbname="php_blog";

$conn=mysqli_connect($host,$user,$pass,$dbname);
if($conn->connect_error){
    die("Database Connection Failed: ". $conn->connect_error);
}
?>