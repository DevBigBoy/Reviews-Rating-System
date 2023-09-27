<?php
 include 'components/connect.php';

 if(isset($_POST['submit'])){
    $email = $_POST['email'];
    $pass = $_POST['pass'];

    $verify_email = $conn->prepare("SELECT * FROM `users` WHERE email = ? LIMIT 1");
    $verify_email->execute([$email]);

    if ($verify_email->rowCount() > 0) {
        $fetch = $verify_email->fetch(PDO::FETCH_ASSOC);
        $verify_pass = password_verify($pass, $fetch['password']);
        
        if($verify_pass == 1){
            setcookie('user_id', $fetch['id'], time() + 60*60*24*30, '/');
            header('location: all_posts.php');

        } else {
            $warning_msg[] = 'Incorrect password!';
        }
    } else {
        $warning_msg[] = 'Incorrect email!';
    }
 }
 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!--=================== Custom CSS=================== -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>

    <!--================= HEADER Start =================-->
    <?php  include 'components/header.php' ;?>
    <!--================= HEADER Start =================-->

    <!-- Login Section Start -->
    <section class="account-form">
        <form action="login.php" method="post" enctype="multipart/form-data">
            <h3>welcome back!</h3>

            <p class="placeholder">your email <span>*</span></p>
            <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="box">

            <p class="placeholder">your password <span>*</span></p>
            <input type="password" name="pass" required maxlength="50" placeholder="enter your password" class="box">

            <p class="link">don't have an account? <a href="register.php">register now</a></p>
            <input type="submit" value="login" class="btn" name="submit">

        </form>
    </section>
    <!-- Login Section END -->


    <!--====================== sweetalert ======================-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!--====================== custome js ======================-->
    <script src="./js/script.js"></script>

    <!--====================== custome js ======================-->
    <?php include 'components/alers.php';?>
</body>

</html>