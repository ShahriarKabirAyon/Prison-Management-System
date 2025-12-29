<?php
$conn = new mysqli("localhost", "root", "", "PMS");
if ($conn->connect_error) die("DB Connection Failed: " . $conn->connect_error);
?>