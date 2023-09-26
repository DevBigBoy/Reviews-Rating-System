<?php
 include 'components/connect.php';

 if (isset($_POST['submit'])) {
    $id = create_unique_id();
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];

    $email = $_POST['email'];

    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT) ;
    $c_pass = password_verify($_POST['c_pass'], $pass); 
    
    $sex = $_POST['sex'];

    $image = $_FILES['image']['name'];
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename = create_unique_id().'.'.$ext;
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'upload/'.$rename;

    //TODO: image validation
    if(!empty($image)){
        if($image_size > 2000000){
            $warning_msg[] = 'Image Size is too large!';
        } else {
            move_uploaded_file($image_tmp_name, $image_folder);
        }
    } else {
        $rename = '';
    }

    $verfiy_email = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $verfiy_email->execute([$email]);

    if ($verfiy_email->rowCount() > 0) {
        $warning_msg[] = 'email already taken!';
    } else {
        if($c_pass == 1){
            $insert_user = $conn->prepare("INSERT INTO `users` (id, first_name, last_name, email, password, image, sex) VALUES (?,?,?,?,?,?,?)");
            $insert_user->execute([$id,$f_name,$l_name,$email,$pass,$rename,$sex]);
            $success_msg[] = 'Registered Successfully!';
        } else {
            $warning_msg[] = 'Confirm password not matched!';
        }  
    }
 }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <!--=================== Custom CSS=================== -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>

    <!--================= HEADER Start =================-->
    <?php  include 'components/header.php' ;?>
    <!--================= HEADER Start =================-->

    <section class="account-form">
        <form action="register.php" method="post" enctype="multipart/form-data">
            <h3>make your account!</h3>

            <p class="placeholder">your First Name <span>*</span></p>
            <input type="text" name="f_name" required maxlength="30" placeholder="enter your first name" class="box">

            <p class="placeholder">your Last Name <span>*</span></p>
            <input type="text" name="l_name" required maxlength="30" placeholder="enter your last name" class="box">

            <p class="placeholder">your email <span>*</span></p>
            <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="box">

            <p class="placeholder">your password <span>*</span></p>
            <input type="password" name="pass" required maxlength="50" placeholder="enter your password" class="box">

            <p class="placeholder">confirm password <span>*</span></p>
            <input type="password" name="c_pass" required maxlength="50" placeholder="confirm your password"
                class="box">

            <p class="placeholder">Your gender <span>*</span></p>
            <select name="sex" id="" class="box">
                <option value="m">male</option>
                <option value="f">female</option>
            </select>

            <p class="placeholder">profile pic </p>
            <input type="file" name="image" class="box" accept="image/*">

            <p class="link">already have an account? <a href="login.php">login now</a></p>
            <input type="submit" value="register now" class="btn" name="submit">

        </form>
    </section>



    <!--====================== sweetalert ======================-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!--====================== custome js ======================-->
    <script src="./js/script.js"></script>

    <!--====================== custome js ======================-->
    <?php include 'components/alers.php';?>
</body>

</html>