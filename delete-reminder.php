<?php

session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$reminder_id = (int)$_GET['id'];
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
    SELECT reminders.id
    FROM reminders
    JOIN cars ON reminders.car_id = cars.id
    WHERE reminders.id = $reminder_id
    AND cars.user_id = $user_id
");

if(mysqli_num_rows($result) == 0){
    die("Brak dostępu");
}

mysqli_query($conn,"
    DELETE FROM reminders
    WHERE id = $reminder_id
");

header("Location: reminders.php");
exit();