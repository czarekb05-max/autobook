<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "autobook";

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

if (!$conn) {
    die("Błąd połączenia z bazą danych");
}
?>