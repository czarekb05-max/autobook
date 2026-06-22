<?php

session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])){
    header("Location: repairs.php");
    exit();
}

$repair_id = (int)$_GET['id'];
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

$result = mysqli_query($conn,"
    SELECT repairs.id
    FROM repairs
    JOIN cars ON repairs.car_id = cars.id
    WHERE repairs.id = $repair_id
    AND cars.user_id = $user_id
");

if(mysqli_num_rows($result) == 0){
    die('Brak dostępu');
}

mysqli_query($conn,"
    DELETE FROM repairs
    WHERE id = $repair_id
");

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();