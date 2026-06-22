<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include 'config/db.php';

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


$result = mysqli_query($conn,
    "SELECT *
     FROM cars
     WHERE id = $car_id
     AND user_id = $user_id");

if(mysqli_num_rows($result) == 0){
    die("Brak dostępu do pojazdu");
}

$car = mysqli_fetch_assoc($result);

if(isset($_POST['update_car'])){

   $brand = $_POST['brand'];
$model = $_POST['model'];
$year = $_POST['year'];
$plate = $_POST['plate'];

$image_sql = "";

if(!empty($_FILES['image']['name'])){

    $image_name = time() . '_' . $_FILES['image']['name'];

    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        'uploads/' . $image_name
    );

    $image_sql = ", image = '$image_name'";
}

mysqli_query($conn,
    "UPDATE cars
     SET
        brand = '$brand',
        model = '$model',
        production_year = '$year',
        plate_number = '$plate'
        $image_sql
     WHERE id = $car_id");
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

    <a href="dashboard.php">
        <i class="fa-solid fa-house"></i>
        Strona Główna
    </a>

    <a href="cars.php" class="active">
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

    <div class="topbar-left">

        <i class="fa-solid fa-bars menu-icon"></i>

        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Szukaj...">
        </div>

    </div>

    <div class="topbar-right">

        <div class="notification">
            <i class="fa-regular fa-bell"></i>
            <span class="badge"><?php echo $reminders_count; ?></span>
        </div>

        <div class="user-profile">

            <?php if(!empty($_SESSION['avatar'])) { ?>

    <img
        src="uploads/<?php echo $_SESSION['avatar']; ?>"
        alt="avatar">

<?php } else { ?>

    <img
        src="images/avatar.png"
        alt="avatar">

<?php } ?>

            <div>
                <strong><?php echo $_SESSION['user_name']; ?></strong>
                <p><?php echo $_SESSION['user_email']; ?></p>
            </div>

        </div>

    </div>

</header>


<h1 class="page-title">✏️ Edytuj samochód</h1>

<p class="page-subtitle">
    Zmień dane pojazdu
</p>

<div class="form-card">

<form method="POST" enctype="multipart/form-data">


<div class="form-group">
    <label>Marka</label>
    <input type="text"
           name="brand"
           value="<?php echo $car['brand']; ?>"
           required>
</div>

<div class="form-group">
    <label>Model</label>
    <input type="text"
           name="model"
           value="<?php echo $car['model']; ?>"
           required>
</div>

<div class="form-group">
    <label>Rok produkcji</label>
    <input type="number"
           name="year"
           value="<?php echo $car['production_year']; ?>"
           required>
</div>

<div class="form-group">
    <label>Numer rejestracyjny</label>
    <input type="text"
           name="plate"
           value="<?php echo $car['plate_number']; ?>"
           required>
</div>

<div class="form-group">
    <label>Nowe zdjęcie samochodu</label>
    <input
        type="file"
        name="image"
        accept="image/*">
</div>

<button class="submit-btn"
        type="submit"
        name="update_car">
    Zapisz zmiany
</button>


</form>

</div>

</main>

</div>
<script src="js/main.js"></script>
</body>
</html>
