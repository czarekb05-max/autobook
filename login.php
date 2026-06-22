<?php
session_start();
include("config/db.php");

$message = "";

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result) == 1){

        $user = mysqli_fetch_assoc($result);

        if(password_verify($password,$user['password'])){

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['avatar'] = $user['avatar'];

            header("Location: dashboard.php");
            exit();

        }else{
            $message = "Nieprawidłowe hasło";
        }

    }else{
        $message = "Nie znaleziono użytkownika";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Logowanie - AutoBook</title>

<link rel="stylesheet" href="css/style.css">

<style>
.auth-box{
    max-width:500px;
    margin:80px auto;
    background:white;
    padding:40px;
    border-radius:20px;
    box-shadow:0 3px 15px rgba(0,0,0,.08);
}

.auth-box input{
    width:100%;
    padding:15px;
    margin-bottom:15px;
    border:1px solid #ddd;
    border-radius:12px;
}

.auth-box button{
    width:100%;
    padding:15px;
    border:none;
    border-radius:12px;
    background:#0b4ecf;
    color:white;
    cursor:pointer;
}

.error{
    color:red;
    margin-bottom:15px;
}
</style>

</head>
<body>

<div class="auth-box">

<h1>Logowanie</h1>

<p class="error"><?php echo $message; ?></p>

<form method="POST">

    <input
        type="email"
        name="email"
        placeholder="Email"
        required>

    <input
        type="password"
        name="password"
        placeholder="Hasło"
        required>

    <button name="login">
        Zaloguj
    </button>

</form>

</div>

</body>
</html>