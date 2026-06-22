<?php


session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])){
    header("Location: cars.php");
    exit();
}

$car_id = (int)$_GET['id'];
$user_id = (int)$_SESSION['user_id'];

$reminders_count = mysqli_num_rows(
    mysqli_query($conn,"
        SELECT reminders.id
        FROM reminders
        JOIN cars ON reminders.car_id = cars.id
        WHERE cars.user_id = ".$_SESSION['user_id']."
        AND reminder_date >= CURDATE()
    ")
);

// sprawdzenie właściciela auta
$result = mysqli_query($conn,
    "SELECT *
     FROM cars
     WHERE id = $car_id
     AND user_id = $user_id");

if(mysqli_num_rows($result) == 0){
    die("Brak dostępu");
}

// usunięcie napraw tego auta
mysqli_query($conn,
    "DELETE FROM repairs
     WHERE car_id = $car_id");

// usunięcie auta
mysqli_query($conn,
    "DELETE FROM cars
     WHERE id = $car_id");

header("Location: cars.php");
exit();