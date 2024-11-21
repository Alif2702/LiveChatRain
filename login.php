<?php
    include 'php/config.php'; // Menghubungkan ke database
    session_start();
    if (isset($_POST['submit'])) { // Jika tombol submit diklik

        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, md5($_POST['password']));

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) { // Memastikan email valid

            $select = mysqli_query($conn, "SELECT * FROM user_form WHERE email = '$email' 
                                           AND password = '$password' "); // Memeriksa email dan password
            if (mysqli_num_rows($select) > 0) {
                $row = mysqli_fetch_assoc($select);
                $status = 'Active Now'; // Status pengguna saat ini

                $update = mysqli_query($conn, "UPDATE user_form SET status = '$status' 
                                                WHERE user_id = '{$row['user_id']}' ");
                
                if ($update) {
                    $_SESSION['user_id'] = $row['user_id']; // Menyimpan user_id ke sesi
                    $_SESSION['role'] = $row['role']; // Menyimpan role ke sesi

                    header('location: home.php');
                }
            } else {
                $alert[] = "Incorrect password or email!";
            }
        } else {
            $alert[] = "$email is not a valid email!";
        }
    }

    if (isset($_SESSION['user_id'])) {
        header("location: home.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>welcome back</title>
</head>
<body>
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data">
            <h3>welcome back</h3>
            <?php 
                if(isset($alert)){
                    foreach($alert as $alert){
                        echo '<div class="alert">'.$alert.'</div>';
                    }
                }
            ?>
            <input type="email" name="email" placeholder="enter email" class="box" required>
            <input type="password" name="password" placeholder="enter password" class="box" required>
            <input type="submit" name="submit" class="btn" value="start chatting">
            <p>don't have an account? <a href="index.php">register now</a></p>
        </form>
    </div>
</body>
</html>