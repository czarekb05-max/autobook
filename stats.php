<?php

session_start();

include 'config/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

$max_query = mysqli_query($conn,
    "SELECT MAX(r.cost) as max_cost
     FROM repairs r
     JOIN cars c ON r.car_id = c.id
     WHERE c.user_id = $user_id");

$max = mysqli_fetch_assoc($max_query);
$max_cost = $max['max_cost'] ?? 0;

$cars_query = mysqli_query($conn,
    "SELECT COUNT(*) as total FROM cars WHERE user_id = $user_id");
$cars = mysqli_fetch_assoc($cars_query);

$repairs_query = mysqli_query($conn,
    "SELECT COUNT(*) as total FROM repairs r
     JOIN cars c ON r.car_id = c.id
     WHERE c.user_id = $user_id");
$repairs = mysqli_fetch_assoc($repairs_query);

$reminders_count = mysqli_num_rows(
    mysqli_query($conn,"
        SELECT reminders.id
        FROM reminders
        JOIN cars ON reminders.car_id = cars.id
        WHERE cars.user_id = ".$_SESSION['user_id']."
        AND reminder_date >= CURDATE()
    ")
);

$cost_query = mysqli_query($conn,
    "SELECT SUM(r.cost) as total FROM repairs r
     JOIN cars c ON r.car_id = c.id
     WHERE c.user_id = $user_id");
$cost = mysqli_fetch_assoc($cost_query);

$total_cost = $cost['total'] ?? 0;
$total_repairs = $repairs['total'] ?? 0;
$total_cars = $cars['total'] ?? 0;

$average_cost = $total_repairs > 0
    ? round($total_cost / $total_repairs)
    : 0;

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

<a href="stats.php" class="active">
    <i class="fa-solid fa-chart-column"></i>
    Statystyki
</a>

<a href="reminders.php" >
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

<h1 class="page-title">📊 Statystyki kosztów</h1>

    <p class="page-subtitle">
        Analiza wydatków związanych z eksploatacją pojazdów
    </p>

<section class="cards">

    <div class="card">
        <i class="fa-solid fa-wallet card-icon"></i>
        <h3>Łączny koszt</h3>
        <span><?php echo $total_cost; ?> zł</span>
    </div>

    <div class="card">
        <i class="fa-solid fa-screwdriver-wrench card-icon"></i>
        <h3>Liczba napraw</h3>
        <span><?php echo $total_repairs; ?></span>
    </div>

    <div class="card">
        <i class="fa-solid fa-chart-line card-icon"></i>
        <h3>Średni koszt</h3>
        <span><?php echo $average_cost; ?> zł</span>
    </div>

    <div class="card">
        <i class="fa-solid fa-car card-icon"></i>
        <h3>Pojazdy</h3>
        <span><?php echo $total_cars; ?></span>
    </div>

  


</section>
<div class="stats-table">

    <h2>Podsumowanie</h2>

    <table>

        <tr>
            <th>Parametr</th>
            <th>Wartość</th>
        </tr>

        <tr>
            <td>Liczba pojazdów</td>
            <td><?php echo $total_cars; ?></td>
        </tr>

        <tr>
            <td>Liczba napraw</td>
            <td><?php echo $total_repairs; ?></td>
        </tr>

        <tr>
            <td>Łączny koszt</td>
            <td><?php echo $total_cost; ?> zł</td>
        </tr>

        <tr>
            <td>Średni koszt naprawy</td>
            <td><?php echo $average_cost; ?> zł</td>
        </tr>

        <tr>
    <td>Najdroższa naprawa</td>
    <td><?php echo $max_cost; ?> zł</td>
</tr>

    </table>

</div>

    </main>

</div>
<script src="js/main.js"></script>
</body>
</html>