
<?php

session_start();
include("config/db.php");
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

$dashboard_reminders = mysqli_query($conn,"
    SELECT
        reminders.*,
        cars.brand,
        cars.model
    FROM reminders
    JOIN cars ON reminders.car_id = cars.id
    WHERE cars.user_id = $user_id
    ORDER BY reminder_date ASC
    LIMIT 5
");

$cars_query = mysqli_query($conn,
    "SELECT COUNT(*) AS total
     FROM cars
     WHERE user_id = $user_id");

$cars = mysqli_fetch_assoc($cars_query);

$reminders_count = mysqli_num_rows(
    mysqli_query($conn,"
        SELECT reminders.id
        FROM reminders
        JOIN cars ON reminders.car_id = cars.id
        WHERE cars.user_id = ".$_SESSION['user_id']."
        AND reminder_date >= CURDATE()
    ")
);

$repairs_query = mysqli_query($conn,
    "SELECT COUNT(*) AS total
     FROM repairs
     JOIN cars ON repairs.car_id = cars.id
     WHERE cars.user_id = $user_id");

$repairs = mysqli_fetch_assoc($repairs_query);

$cost_query = mysqli_query($conn,
    "SELECT SUM(repairs.cost) AS total
     FROM repairs
     JOIN cars ON repairs.car_id = cars.id
     WHERE cars.user_id = $user_id");

$cost = mysqli_fetch_assoc($cost_query);

$total_cars = $cars['total'] ?? 0;
$total_repairs = $repairs['total'] ?? 0;
$total_cost = $cost['total'] ?? 0;

$latest_repairs = mysqli_query($conn, "
    SELECT
        repairs.*
    FROM repairs
    JOIN cars ON repairs.car_id = cars.id
    WHERE cars.user_id = $user_id
    ORDER BY repairs.repair_date DESC
    LIMIT 5
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

    
             <a href="dashboard.php" class="active">
    <i class="fa-solid fa-house"></i>
    Strona Główna
</a>

<a href="cars.php" >
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

        <section class="hero">

            <div class="hero-text">

                <h2>Zadbaj o swoje auto</h2>

                <p>
                    Prowadź historię napraw,
                    kontroluj koszty i otrzymuj przypomnienia.
                </p>

                <a href="add-car.php">
    <button>Dodaj samochód</button>
</a>

            </div>

        </section>

        <section class="cards">

   <div class="card">
    <i class="fa-solid fa-car card-icon"></i>

    <h3>Moje samochody</h3>

    <span><?php echo $total_cars; ?></span>

    <a href="cars.php" class="card-link">
        Zobacz wszystkie →
    </a>
</div>

    <div class="card">
    <i class="fa-solid fa-screwdriver-wrench card-icon"></i>

    <h3>Liczba napraw</h3>

    <span><?php echo $total_repairs; ?></span>

    <a href="repairs.php" class="card-link">
        Historia napraw →
    </a>
</div>

   <div class="card">
    <i class="fa-solid fa-wallet card-icon"></i>

    <h3>Łączny koszt</h3>

    <span><?php echo $total_cost; ?> zł</span>

    <a href="stats.php" class="card-link">
        Szczegóły →
    </a>
</div>

    <div class="card">
    <i class="fa-solid fa-calendar-days card-icon"></i>

    <h3>Najbliższy serwis</h3>

    <span>24 dni</span>

    <a href="reminders.php" class="card-link">
        Przypomnienia →
    </a>

</div>

</section>

<section class="bottom-section">

    <div class="repairs">

        <h2>Ostatnie naprawy</h2>

        <table>

            <tr>
                <th>Data</th>
                <th>Naprawa</th>
                <th>Koszt</th>
            </tr>

            <?php while($repair = mysqli_fetch_assoc($latest_repairs)) { ?>

<tr>
    <td><?php echo $repair['repair_date']; ?></td>
    <td><?php echo $repair['repair_name']; ?></td>
    <td><?php echo $repair['cost']; ?> zł</td>
</tr>

<?php } ?>
        </table>

    </div>
    <div class="right-column">

    <div class="alerts">

    <h2>Przypomnienia</h2>

    <?php while($reminder = mysqli_fetch_assoc($dashboard_reminders)) { ?>

    <?php
    $days = ceil(
        (strtotime($reminder['reminder_date']) - time()) / 86400
    );
    ?>

    <div class="alert-card">

        🔔 <?php echo $reminder['title']; ?>

        <br>

        <small>
            <?php echo $reminder['brand'].' '.$reminder['model']; ?>
        </small>

        <br>

        <?php
        if($days > 0){
            echo "Za ".$days." dni";
        } else {
            echo "Termin minął";
        }
        ?>

    </div>

    <?php } ?>

</div>

    <div class="cost-stats">

    <h2>Statystyki kosztów</h2>

    <div class="cost-total">
        <span>Łączny koszt napraw</span>
        <strong><?php echo $total_cost; ?> zł</strong>
    </div>

</div>
</div>

</section>

    </main>

</div>
<script src="js/main.js"></script>
</body>
</html>