<?php

session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$search = $_GET['search'] ?? '';
$car_filter = $_GET['car_id'] ?? '';

$reminders_count = mysqli_num_rows(
    mysqli_query($conn,"
        SELECT reminders.id
        FROM reminders
        JOIN cars ON reminders.car_id = cars.id
        WHERE cars.user_id = ".$_SESSION['user_id']."
        AND reminder_date >= CURDATE()
    ")
);

$cars = mysqli_query($conn,"
    SELECT *
    FROM cars
    WHERE user_id = $user_id
    ORDER BY brand, model
");

$reminders = mysqli_query($conn,"
    SELECT
        reminders.*,
        cars.brand,
        cars.model
    FROM reminders
    JOIN cars ON reminders.car_id = cars.id
    WHERE cars.user_id = $user_id

    AND (
        reminders.title LIKE '%$search%'
        OR cars.brand LIKE '%$search%'
        OR cars.model LIKE '%$search%'
    )

    AND (
        '$car_filter' = ''
        OR reminders.car_id = '$car_filter'
    )

    ORDER BY reminder_date ASC
");
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

<a href="cars.php"  >
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

<a href="reminders.php" class="active">
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

            <form method="GET" class="search-box">

   

   <form method="GET" class="search-box">

    <i class="fa-solid fa-magnifying-glass"></i>

    <input
        type="text"
        name="search"
        placeholder="Szukaj przypomnienia..."
        value="<?php echo htmlspecialchars($search); ?>">

    <select name="car_id">

        <option value="">Wszystkie samochody</option>

        <?php while($car = mysqli_fetch_assoc($cars)) { ?>

            <option
                value="<?php echo $car['id']; ?>"
                <?php if($car_filter == $car['id']) echo 'selected'; ?>>

                <?php echo $car['brand'].' '.$car['model']; ?>

            </option>

        <?php } ?>

    </select>

    <button type="submit">
        <i class="fa-solid fa-magnifying-glass"></i>
    </button>


</form>

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

    <h1 class="page-title">📅 Przypomnienia</h1>

    <p class="page-subtitle">
        Nadchodzące serwisy, przeglądy i czynności eksploatacyjne
    </p>

 

<div class="alerts-page">

<?php while($row = mysqli_fetch_assoc($reminders)) { ?>

<?php
$days = ceil(
    (strtotime($row['reminder_date']) - time()) / 86400
);
?>

<div class="alert-card">

    <h3>
        🔔 <?php echo $row['title']; ?>
    </h3>

    <p>
        <?php echo $row['brand'] . ' ' . $row['model']; ?>
    </p>

    <strong>

        <?php
        if($days > 0){
            echo "Za " . $days . " dni";
        } else {
            echo "Termin minął";
        }
        ?>

    </strong>
    <br><br>

<a href="edit-reminder.php?id=<?php echo $row['id']; ?>">
    Edytuj
</a>

|

<a href="delete-reminder.php?id=<?php echo $row['id']; ?>"
   onclick="return confirm('Usunąć przypomnienie?')">
    Usuń
</a>

</div>

<?php } ?>



</div>

</main>

</div>
<script src="js/main.js"></script>
</body>
</html>