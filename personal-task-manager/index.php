<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal</title>
    <!-- main css -->
    <link rel="stylesheet" href="./css/main.css?v=<?php echo time(); ?>">
    <script src="./js/main.js" defer></script>
    <!-- boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- font awsome -->
    <script src="https://kit.fontawesome.com/87b401711e.js" crossorigin="anonymous"></script>
    <!-- font K2D, Readex Pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=K2D:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&family=Readex+Pro:wght@160..700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- header -->
    <header>
        <div class="container">
            <nav class="navbar">
                <!-- logo -->
                <div class="navbar_logo">
                    <img src="./img/logo.png" alt="website logo">
                    <span>
                        <h1>Planex</h1>
                    </span>
                </div>
                <ul class="navbar_links">
                    <a href="#"><button class="navbar_btn"><strong>Home</strong></button></a>
                    <li class="navbar_link"><a href="#"><strong>Features</strong></a></li>
                    <li class="navbar_link"><a href="#"><strong>Plans</strong></a></li>
                    <li class="navbar_link"><a href="#"><strong>Guide</strong></a></li>
                </ul>
                <div class="navbar_icons">
                    <div class="navbar_icon"></div>
                </div>
            </nav>
        </div>
    </header>
    <!-- banner -->
    <section id="banner">
        <div class="container">
            <!-- image -->
            <div class="banner_img">
                <img src="img/banner-img.svg" alt="ilustration of boy">
            </div>
            <!-- heading -->
            <div class="banner_heading">
                <h1>Welcome!</h1>
                <p>Organize your tasks, plan your future,
                    succeed with Planex.</p>
            </div>
            <!-- login -->
            <div class="form_box_login">
                <div class="login">
                    <form action="./php/login.php" method="POST">
                        <h1>LOGIN</h1>
                        <div class="input_box">
                            <input type="email" name="email" placeholder="Email" required>
                            <i class='bx bx-envelope'></i>
                        </div>
                        <div class="input_box">
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                        <div class="forgot_link">
                            <a href="#">Forgot password?</a>
                        </div>
                        <button type="submit" class="login_btn">Login</button>
                        <div class="signup_link">
                            <p>Don't have an account? <a href="./sign-up.php">Sign up</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
</body>

</html>