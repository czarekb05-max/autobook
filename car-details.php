<?php

session_start();
include 'config/db.php';

if(!isset($_GET['id'])){
    header("Location: cars.php");
    exit();
}
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$car_id = (int)$_GET['id'];
$search = $_GET['search'] ?? '';

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
    "SELECT * FROM cars
     WHERE id = $car_id
     AND user_id = " . $_SESSION['user_id']);

if(mysqli_num_rows($result) == 0){
    die("Brak dostępu do pojazdu");
}

$car = mysqli_fetch_assoc($result);

$repairs = mysqli_query($conn,
    "SELECT * FROM repairs
     WHERE car_id = $car_id
     AND repair_name LIKE '%$search%'
     ORDER BY repair_date DESC");
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

    <div class="topbar-left">

        <i class="fa-solid fa-bars menu-icon"></i>

        <form method="GET" class="search-box">

    <input
        type="hidden"
        name="id"
        value="<?php echo $car_id; ?>">

    <i class="fa-solid fa-magnifying-glass"></i>

    <input
        type="text"
        name="search"
        placeholder="Szukaj naprawy..."
        value="<?php echo htmlspecialchars($search); ?>">

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

        <h1 class="page-title">🚗 Szczegóły pojazdu</h1>

<p class="page-subtitle">
    Historia serwisowa samochodu
</p>

<h2 class="vehicle-title">
    <?php echo $car['brand']; ?>
    <?php echo $car['model']; ?>
</h2>

<div class="vehicle-info">

    <div class="vehicle-card">

        <?php if(!empty($car['image'])) { ?>

    <img
        src="uploads/<?php echo $car['image']; ?>"
        alt="Auto">

<?php } else { ?>

    <img
        src="images/car.jpg"
        alt="Auto">

<?php } ?>

        <div class="vehicle-data">

            <p><strong>Marka:</strong> <?php echo $car['brand']; ?></p>
            <p><strong>Model:</strong> <?php echo $car['model']; ?></p>
            <p><strong>Rok:</strong> <?php echo $car['production_year']; ?></p>
            <p><strong>Nr rej:</strong> <?php echo $car['plate_number']; ?></p>
            

        </div>

    </div>

</div>

<a href="edit-car.php?id=<?php echo $car['id']; ?>">
    <button class="add-car-btn">
        <i class="fa-solid fa-pen"></i>
        Edytuj pojazd
    </button>
</a>

<a href="delete-car.php?id=<?php echo $car['id']; ?>"
   onclick="return confirm('Czy na pewno usunąć pojazd?')">
    <button class="add-car-btn">
        <i class="fa-solid fa-trash"></i>
        Usuń pojazd
    </button>
</a>

<a href="add-repair.php?car_id=<?php echo $car['id']; ?>">
    <button class="add-car-btn">
        <i class="fa-solid fa-plus"></i>
        Dodaj naprawę
    </button>
</a>

<a href="add-reminder.php?car_id=<?php echo $car['id']; ?>">
    <button class="add-car-btn">
        <i class="fa-solid fa-bell"></i>
        Dodaj przypomnienie
    </button>
</a>

<form method="GET" class="search-box">

    <input
        type="hidden"
        name="id"
        value="<?php echo $car_id; ?>">

    <i class="fa-solid fa-magnifying-glass"></i>

    <input
        type="text"
        name="search"
        placeholder="Szukaj naprawy..."
        value="<?php echo htmlspecialchars($search); ?>">

</form>


<div class="repairs-section">

    <h2>Historia napraw</h2>

    <table>


<tr>
    <th>Data</th>
    <th>Naprawa</th>
    <th>Koszt</th>
    <th>Akcje</th>
</tr>

<?php while($repair = mysqli_fetch_assoc($repairs)) { ?>
      

            <tr>
    <td><?php echo $repair['repair_date']; ?></td>
    <td><?php echo $repair['repair_name']; ?></td>
    <td><?php echo $repair['cost']; ?> zł</td>

    <td>

    <a href="edit-repair.php?id=<?php echo $repair['id']; ?>">
        Edytuj
    </a>

    |

    <a href="delete-repair.php?id=<?php echo $repair['id']; ?>"
       onclick="return confirm('Usunąć naprawę?')">
        Usuń
    </a>

</td>
</tr>

        <?php } ?>

    </table>
</div>

</main>

</div>
<script src="js/main.js"></script>
</body>
</html>