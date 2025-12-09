<?php
// Thông tin lấy từ mục MySQL Databases của InfinityFree
$servername = "sql204.infinityfree.com"; 
$username = "if0_40631220";           
$password = "hPOlm7HVkyYVy7";         
$dbname = "if0_40631220_final_web";      

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
