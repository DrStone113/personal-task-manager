<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- main css -->
    <link rel="stylesheet" href="./css/sign-up.css?v=<?php echo time(); ?>">
    <script src="./js/register.js?v=<?= time() ?>" defer></script>
    <!-- boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- font awsome -->
    <script src="https://kit.fontawesome.com/87b401711e.js" crossorigin="anonymous"></script>
    <!-- font K2D, Readex Pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=K2D:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&family=Readex+Pro:wght@160..700&display=swap" rel="stylesheet">
    <title>Planex - Sign-up your account</title>
    <link rel="icon" type="image/x-icon" href="./img/logo.png">
</head>

<body>
    <header>
        <div class="header-logo">
            <!-- logo -->
            <img src="./img/logo.png" alt="website logo">
            <span>
                <h1>Planex</h1>
            </span>
        </div>
    </header>
    <div class="wrapper">
        <form action="./php/register.php" method="POST">
            <h1>SIGN UP</h1>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required pattern="[A-Za-z0-9]+">
                <i class='bx bx-user'></i>
            </div>
            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
                <i class='bx bx-envelope'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-box">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="btn">Sign Up</button>
        </form>
    </div>

</html>