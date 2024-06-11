<?php
session_start();

if(isset($_SESSION['login'])){
    header('Location: index.php');
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register - Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
        .register-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .register-header {
            margin-bottom: 1.5rem;
            color: #343a40;
        }
        .register-container .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }
        .register-container .form-control {
            border-radius: 4px;
        }
        .register-container .btn-primary {
            margin-top: 1rem;
            background-color: #6f42c1;
            border: none;
        }
        .register-container .btn-secondary {
            margin-top: 0.5rem;
            background-color: #f8f9fa;
            color: #6c757d;
        }
        .register-container a {
            color: #6f42c1;
            text-decoration: none;
        }
        .register-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1 class="register-header">Register</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="nameInput">Name</label>
                <input type="text" class="form-control" id="nameInput" name="name" placeholder="Masukkan nama" required>
            </div>
            <div class="form-group">
                <label for="emailInput">Email</label>
                <input type="email" class="form-control" id="emailInput" name="email" placeholder="Masukkan email" required>
            </div>
            <div class="form-group">
                <label for="passwordInput">Password</label>
                <input type="password" class="form-control" id="passwordInput" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <a href="login.php" class="btn btn-secondary w-100 mt-2">Sudah punya akun? Login</a>
        <?php
            date_default_timezone_set('Asia/Jakarta');
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
            error_reporting(E_ALL);

            require_once 'config_db.php';

            $db = new ConfigDB();
            $conn = $db->connect();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $name = $_POST['name'];
                $email = $_POST['email'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $createAt = date('Y-m-d H:i:s');

                $query = "INSERT INTO user (email, nama, password, role, tgl_buat) VALUES ('$email', '$name', '$password', 'admin', '$createAt')";
                $queryExecute = $conn->query($query);

                if ($queryExecute) {
                    echo "<div class='alert alert-success mt-3' role='alert'>Akun berhasil di register</div>";
                } else {
                    echo "<div class='alert alert-danger mt-3' role='alert'>Error: " . $query . "<br>" . $conn->error . "</div>";
                }
            }
        ?>
    </div>
</body>
</html>
