<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Toko Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1512820790803-83ca734da794');
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-header {
            margin-bottom: 1.5rem;
            color: #343a40;
        }
        .login-container .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }
        .login-container .form-control {
            border-radius: 4px;
        }
        .login-container .btn-primary {
            margin-top: 1rem;
            background-color: #6f42c1;
            border: none;
        }
        .login-container .btn-secondary {
            margin-top: 0.5rem;
            background-color: #f8f9fa;
            color: #6c757d;
        }
        .login-container a {
            color: #6f42c1;
            text-decoration: none;
        }
        .login-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="login-header">Lupa Password</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="emailInput">Email</label>
                <input type="email" class="form-control" id="emailInput" name="email" placeholder="Masukkan email" required>
            </div>
            <div class="form-group">
                <label for="newPasswordInput">Password Baru</label>
                <input type="password" class="form-control" id="newPasswordInput" name="new_password" placeholder="Masukkan password baru" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ubah Password</button>
            <a href="login.php" class="btn btn-secondary w-100 mt-2">Batal Ubah</a>
        </form>
        <?php
        session_start();

        if(isset($_SESSION['login'])){
            header('Location: index.php');
            exit();
        }
        date_default_timezone_set('Asia/Jakarta');
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
       
         require 'vendor/autoload.php';

         \Sentry\init([
             'dsn' => 'https://1e4fcb86d5f59b0483988c408869dece@o4507427977297920.ingest.us.sentry.io/4507427981295616',
             // Specify a fixed sample rate
             'traces_sample_rate' => 1.0,
             // Set a sampling rate for profiling - this is relative to traces_sample_rate
             'profiles_sample_rate' => 1.0,
         ]);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                require_once 'config_db.php';
                $db = new ConfigDB();
                $conn = $db->connect();
                $email = $_POST['email'];
                $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

                $query = "SELECT id FROM user WHERE email = '$email'";
                $queryExecute = $conn->query($query);
                if ($queryExecute->num_rows > 0) {
                    $user = $queryExecute->fetch_assoc();
                    $userId = $user['id'];
                    $conn->query("UPDATE user SET password = '$newPassword' WHERE id = '$userId'");
                    echo "<div class='alert alert-success mt-3' role='alert'>Password berhasil diupdate. Silahkan <a href='login.php'>login</a></div>";
                } else {
                    echo "<div class='alert alert-danger mt-3' role='alert'>Email tidak ditemukan</div>";
                }
            }
        ?>
    </div>
</body>
</html>
