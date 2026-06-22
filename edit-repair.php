<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include 'config/db.php';

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

$result = mysqli_query($conn,
    "SELECT repairs.*
     FROM repairs
     JOIN cars ON repairs.car_id = cars.id
     WHERE repairs.id = $repair_id
     AND cars.user_id = $user_id");

if(mysqli_num_rows($result) == 0){
    die("Brak dostępu do naprawy");
}

$repair = mysqli_fetch_assoc($result);

if(isset($_POST['update_repair'])){

    $repair_name = $_POST['repair_name'];
    $repair_date = $_POST['repair_date'];
    $cost = $_POST['cost'];
    $description = $_POST['description'];

    mysqli_query($conn,
        "UPDATE repairs
         SET
            repair_name = '$repair_name',
            repair_date = '$repair_date',
            cost = '$cost',
            description = '$description'
         WHERE id = $repair_id");

    header("Location: car-details.php?id=".$repair['car_id']);
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


<h1 class="page-title">✏️ Edytuj naprawę</h1>

<p class="page-subtitle">
    Edytuj dane naprawy
</p>

<div class="form-card">

<form method="POST">


<div class="form-group">
    <label>Nazwa naprawy</label>
    <input type="text"
           name="repair_name"
           value="<?php echo $repair['repair_name']; ?>"
           required>
</div>

<div class="form-group">
    <label>Data naprawy</label>
    <input type="date"
           name="repair_date"
           value="<?php echo $repair['repair_date']; ?>"
           required>
</div>

<div class="form-group">
    <label>Koszt</label>
    <input type="number"
           name="cost"
           value="<?php echo $repair['cost']; ?>"
           required>
</div>

<div class="form-group">
    <label>Opis</label>
    <textarea name="description" rows="5"><?php echo $repair['description']; ?></textarea>
</div>

<button class="submit-btn"
        type="submit"
        name="update_repair">
    Zapisz zmiany
</button>


</form>

</div>

</main>

</div>
<script src="js/main.js"></script>
</body>
</html>
