<?php
include("config/db.php");

$message = "";

if(isset($_POST['register'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $avatar = '';

if(!empty($_FILES['avatar']['name'])){

    $avatar = time() . '_' . $_FILES['avatar']['name'];

    move_uploaded_file(
        $_FILES['avatar']['tmp_name'],
        'uploads/' . $avatar
    );
}

    $sql = "INSERT INTO users(
            name,
            email,
            password,
            avatar
        )
        VALUES(
            '$name',
            '$email',
            '$password',
            '$avatar'
        )";

    if(mysqli_query($conn,$sql)){
        $message = "Konto zostało utworzone!";
    }else{
        $message = "Błąd rejestracji!";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Rejestracja - AutoBook</title>

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

.auth-box h1{
    margin-bottom:25px;
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

.success{
    color:green;
    margin-bottom:15px;
}

</style>

</head>
<body>

<div class="auth-box">

    <h1>Rejestracja</h1>

    <p class="success"><?php echo $message; ?></p>

    <form method="POST" enctype="multipart/form-data">

        <input
            type="text"
            name="name"
            placeholder="Imię"
            required>

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
        <div style="margin-bottom:15px;">
    <label>Avatar</label>
    <input
        type="file"
        name="avatar"
        accept="image/*">
</div>


        <button name="register">
            Załóż konto
        </button>

    </form>

</div>

</body>
</html>