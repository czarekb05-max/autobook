<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$search = $_GET['search'] ?? '';
$user_id = (int)$_SESSION['user_id'];
$brand = $_GET['brand'] ?? '';

$brands = mysqli_query($conn,"
    SELECT DISTINCT brand
    FROM cars
    WHERE user_id = $user_id
    ORDER BY brand
");

$reminders_count = mysqli_num_rows(
    mysqli_query($conn,"
        SELECT reminders.id
        FROM reminders
        JOIN cars ON reminders.car_id = cars.id
        WHERE cars.user_id = ".$_SESSION['user_id']."
        AND reminder_date >= CURDATE()
    ")
);

$result = mysqli_query($conn, "
    SELECT *
FROM cars
WHERE user_id = $user_id

AND (
    brand LIKE '%$search%'
    OR model LIKE '%$search%'
    OR plate_number LIKE '%$search%'
)

AND (
    '$brand' = ''
    OR brand = '$brand'
)
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

        <form method="GET" class="search-box">

    <i class="fa-solid fa-magnifying-glass"></i>

    <input
        type="text"
        name="search"
        placeholder="Szukaj auta..."
        value="<?php echo htmlspecialchars($search); ?>">
        
        <button type="submit">
    <i class="fa-solid fa-magnifying-glass"></i>
</button>
        
        <select name="brand">

    <option value="">Wszystkie marki</option>

    <?php while($row = mysqli_fetch_assoc($brands)) { ?>

        <option
            value="<?php echo $row['brand']; ?>"
            <?php if($brand == $row['brand']) echo 'selected'; ?>>

            <?php echo $row['brand']; ?>

        </option>

    <?php } ?>

</select>

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

        <h1 class="page-title">Moje samochody</h1>

<a href="add-car.php">
    <button class="add-car-btn">
        <i class="fa-solid fa-plus"></i>
        Dodaj samochód
    </button>
</a>

<section class="cars-grid">

<?php while($car = mysqli_fetch_assoc($result)) { ?>

    <div class="car-card">

        <?php if(!empty($car['image'])) { ?>

    <img
        src="uploads/<?php echo $car['image']; ?>"
        alt="Auto">

<?php } else { ?>

    <img
        src="images/car.jpg"
        alt="Auto">

<?php } ?>

        <div class="car-info">

            <h3>
                <?php echo $car['brand']; ?>
                <?php echo $car['model']; ?>
            </h3>

            <p>Rok: <?php echo $car['production_year']; ?></p>

            <p>Nr rej: <?php echo $car['plate_number']; ?></p>

            <a href="car-details.php?id=<?php echo $car['id']; ?>">
    <button>Szczegóły</button>
</a>

<a href="delete-car.php?id=<?php echo $car['id']; ?>"
   onclick="return confirm('Na pewno usunąć samochód?')">
    <button>Usuń</button>
</a>

        </div>

    </div>

<?php } ?>

</section>

    </main>

</div>
<script src="js/main.js"></script>
</body>
</html>