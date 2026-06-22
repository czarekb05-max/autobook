<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include 'config/db.php';

if(isset($_POST['add_car'])){

    $user_id = $_SESSION['user_id'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $plate = $_POST['plate'];

    $image_name = '';

if(!empty($_FILES['image']['name'])){

    $image_name = time() . '_' . $_FILES['image']['name'];

    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        'uploads/' . $image_name
    );
}

    $sql = "INSERT INTO cars(
            user_id,
            brand,
            model,
            production_year,
            plate_number,
            image
        )
        VALUES(
            '$user_id',
            '$brand',
            '$model',
            '$year',
            '$plate',
            '$image_name'
        )";

$reminders_count = mysqli_num_rows(
    mysqli_query($conn,"
        SELECT reminders.id
        FROM reminders
        JOIN cars ON reminders.car_id = cars.id
        WHERE cars.user_id = ".$_SESSION['user_id']."
        AND reminder_date >= CURDATE()
    ")
);

    mysqli_query($conn, $sql);

    header("Location: cars.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoBook</title>

    <link rel="stylesheet" href="css/style.css">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="container">

    <aside class="sidebar">

        <div class="logo">
            <h1>🚗 AutoBook</h1>
            <p>Dziennik serwisowy</p>
        </div>

        <nav>

        <a href="dashboard.php" >
    <i class="fa-solid fa-house"></i>
    Strona Główna
</a>

<a href="cars.php" class="active" >
    <i class="fa-solid fa-car"></i>
    Moje samochody
</a>

            <a href="repairs.php">
    <i class="fa-solid fa-screwdriver-wrench"></i>
    Naprawy
</a>

<a href="stats.php">
    <i class="fa-solid fa-chart-column"></i>
    Statystyki
</a>

<a href="reminders.php">
    <i class="fa-solid fa-bell"></i>
    Przypomnienia
</a>

<a href="logout.php">
    <i class="fa-solid fa-right-from-bracket"></i>
    Wyloguj
</a>

        </nav>

    </aside>

    <main class="content">

        <header class="topbar">

            <input
                type="text"
                placeholder="Szukaj...">

            <div class="user">
                Witaj, Kasia 👋
            </div>

        </header>

<h1 class="page-title">🚗 Dodaj samochód</h1>

<p class="page-subtitle">
    Dodaj nowy pojazd do swojego garażu
</p>

<div class="form-card">

  <form method="POST" enctype="multipart/form-data">

    <div class="form-group">
        <label>Marka</label>
        <input type="text" name="brand" placeholder="Np. Audi" required>
    </div>

    <div class="form-group">
        <label>Model</label>
        <input type="text" name="model" placeholder="Np. A6" required>
    </div>

    <div class="form-group">
        <label>Rok produkcji</label>
        <input type="number" name="year" placeholder="2020" required>
    </div>

    <div class="form-group">
        <label>Numer rejestracyjny</label>
        <input type="text" name="plate" placeholder="WB12345" required>
    </div>

<div class="form-group">
    <label>Zdjęcie samochodu</label>
    <input
        type="file"
        name="image"
        accept="image/*">
</div>

    <button class="submit-btn" type="submit" name="add_car">
        Zapisz samochód
    </button>

</form>

</div>