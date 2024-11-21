<?php 
    include 'php/config.php'; // Menghubungkan ke database
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('location: login.php');
        exit();
    }

    // Mendapatkan data dari session
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    // Mengambil data pengguna yang sedang login
    $stmt = $conn->prepare("SELECT * FROM user_form WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        header('location: login.php');
        exit();
    }

    // Menentukan query berdasarkan role
    if ($role === 'admin') {
        // Admin melihat semua pengguna kecuali dirinya sendiri
        $query = "SELECT * FROM user_form WHERE user_id != ?";
    } else {
        // User hanya melihat admin
        $query = "SELECT * FROM user_form WHERE role = 'admin' AND user_id != ?";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $users = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Home Page</title>
</head>
<body>
    <div class="container">
        <section class="users">
            <header class="profile">
                <div class="content">
                    <a href="update_profile.php">
                        <img src="uploaded_img/<?php echo htmlspecialchars($row['img']); ?>" alt="">
                    </a>
                    <div class="details">
                        <span><?php echo htmlspecialchars($row['fname']); ?></span>
                        <p><?php echo htmlspecialchars($row['status']); ?></p>
                    </div>
                </div>
                <a href="php/logout.php?logout_id=<?php echo urlencode($user_id); ?>" class="logout">Logout</a>
            </header>
            <form action="" method="post" class="search">
                <input type="text" name="search_box" placeholder="Enter name or email to search">
                <button name="search_user"><img src="images/search.svg" alt=""></button>
            </form>
            <div class="all_users">
                <?php
                // Menampilkan daftar pengguna sesuai dengan hasil query
                if ($users->num_rows > 0) {
                    while ($user = $users->fetch_assoc()) {
                        echo '<a href="chat.php?user_id=' . htmlspecialchars($user['user_id']) . '">
                                <div class="content">
                                    <img src="uploaded_img/' . htmlspecialchars($user['img']) . '" alt="">
                                    <div class="details">
                                        <span>' . htmlspecialchars($user['fname']) . '</span>
                                        <p>' . htmlspecialchars($user['status']) . '</p>
                                    </div>
                                </div>
                              </a>';
                    }
                } else {
                    echo "<p>No users available to chat</p>";
                }
                ?>
            </div>
        </section>
    </div>
    <script src="js/home.js"></script>
</body>
</html>
