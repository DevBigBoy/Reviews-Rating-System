<?php
 include 'components/connect.php';

 if (isset($_POST['submit'])) {
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
    $select_user->execute([$user_id]);
    $fetch_user  = $select_user->fetch(PDO::FETCH_ASSOC);

    $f_name = $_POST['f_name'];
    $f_name = trim($f_name);
    $l_name = $_POST['l_name'];
    $l_name = trim($l_name);
    $email = $_POST['email'];
    $sex = $_POST['sex'];

    if (!empty($f_name)) {
        $update_fname = $conn->prepare("UPDATE `users` SET first_name = ? WHERE id = ?");
        $update_fname->execute([$f_name, $user_id]);
        $success_msg[] = 'first name Updated!';
    }

    if (!empty($l_name)) {
        $update_lname = $conn->prepare("UPDATE `users` SET last_name = ? WHERE id = ?");
        $update_lname->execute([$l_name, $user_id]);
        $success_msg[] = 'last name Updated!';
    }

    if (!empty($email)) {
        $verify_email = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $verify_email->execute([$email]);
        if ($verify_email->rowCount() > 0) {
            $warning_msg[] = 'Email already taken!';
        }else{
            $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
            $update_email->execute([$email, $user_id]);
            $success_msg[] = 'Email updated!';
        }
    }

    if (!empty($sex)) {
        $update_sex = $conn->prepare("UPDATE `users` SET sex = ? WHERE id = ?");
        $update_sex->execute([$sex, $user_id]);
    }

    $image = $_FILES['image']['name'];
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename = create_unique_id().'.'.$ext;
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'upload/'.$rename;

    if(!empty($image)){
        if($image_size > 2000000){
            $warning_msg[] = 'Image Size is too large!';
        } else {
            $update_image = $conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
            $update_image->execute([$rename, $user_id]);

            move_uploaded_file($image_tmp_name, $image_folder);
            // TODO: delete the old one
            if ($fetch_user['image'] != '') {
                unlink('upload/'.$fetch_user['image']);
            }
            
            $success_msg[] = 'Image updated!';
        }
    }

    // TODO:: Update Password
    $prev_pass = $fetch_user['password'];

    $old_pass  = password_hash($_POST['old_pass'], PASSWORD_DEFAULT);
    $empty_old  = password_verify('', $old_pass);

    $new_pass = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
    $empty_new = password_verify('', $new_pass);

    $c_pass = password_verify($_POST['c_pass'], $new_pass);

    // TODO:: Check that user enter old password 
    if ($empty_old != 1) {
        $verify_old_pass = password_verify($_POST['old_pass'], $prev_pass);
        
        //TODO:: check if the old password matches the one in the database the return result must be 1
        if ($verify_old_pass == 1) {
            
            // TODO:: Check new password === confirmed password 
            if ($c_pass == 1) {
                
                // TODO:: so you have to be sure the new !empty
                if ($empty_new != 1) {
                    $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
                    $update_pass->execute([$new_pass, $user_id]);
                    
                    $success_msg[] = 'Password updated!';
                } else {
                    $warning_msg[] = 'Please enter new password!';
                }
            } else {
                $warning_msg[] = 'Confirm password not matched!';
            }
        } else {
            $warning_msg[] = 'Old password not matched!';
        }
    } 

}

if (isset($_POST['delete_image'])) {
    $select_old_pic = $conn->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
    $select_old_pic->execute([$user_id]);
    $fetch_old_pic = $select_old_pic->fetch(PDO::FETCH_ASSOC);

    if($fetch_old_pic['image'] == ''){
        $warning_msg[] = 'Image already deleted!';
    } else {
        $update_old_pic = $conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
        $update_old_pic->execute(['', $user_id]);

        if ($fetch_old_pic['image'] != '') {
            unlink('upload/'.$fetch_old_pic['image']);
        }
        
        $success_msg[] = 'Image deleted!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>update profile</title>

    <!--=================== Custom CSS=================== -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>

    <!--================= HEADER Start =================-->
    <?php  include 'components/header.php' ;?>
    <!--================= HEADER Start =================-->

    <!--================ Update Section Start  ================-->
    <section class="account-form">
        <form action="" method="post" enctype="multipart/form-data">
            <h3>Update your profile!</h3>

            <p class="placeholder">your First Name </p>
            <input type="text" name="f_name" maxlength="30" placeholder="<?= $fetch_profile['first_name']?>"
                class="box">

            <p class="placeholder">your Last Name </p>
            <input type="text" name="l_name" maxlength="30" placeholder="<?= $fetch_profile['last_name']?>" class="box">

            <p class="placeholder">your email </p>
            <input type="email" name="email" maxlength="50" placeholder="<?= $fetch_profile['email']?>" class="box">

            <p class="placeholder">old password </p>
            <input type="password" name="old_pass" maxlength="50" placeholder="enter your old password" class="box">

            <p class="placeholder">new password </p>
            <input type="password" name="new_pass" maxlength="50" placeholder="enter your new password" class="box">

            <p class="placeholder">confirm password </p>
            <input type="password" name="c_pass" maxlength="50" placeholder="confirm your password" class="box">

            <p class="placeholder">Your gender </p>
            <select name="sex" id="" class="box">
                <option value="m">male</option>
                <option value="f">female</option>
            </select>

            <?php if ($fetch_profile['image'] != '') { ?>
            <img src="upload/<?= $fetch_profile['image']?>" alt="" class="image">
            <input type="submit" value="delete image" name="delete_image" class="delete-btn"
                onclick="return confirm('delete this image?');">
            <?php } ;?>

            <p class="placeholder">profile pic </p>
            <input type="file" name="image" class="box" accept="image/*">


            <input type="submit" value="update now" class="btn" name="submit">

        </form>
    </section>
    <!--================ Update Section End  ================-->

    <!--====================== sweetalert ======================-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>



    <!--====================== custome js ======================-->
    <script src="./js/script.js"></script>

    <!--====================== custome js ======================-->
    <?php include 'components/alers.php';?>
</body>

</html>