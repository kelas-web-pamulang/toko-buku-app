<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - Toko Buku</title>
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
            font-family:  'Arial', sans-serif;
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
        <h1 class="login-header">Selamat Datang Silahkan Login</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="emailInput">Email</label>
                <input type="email" class="form-control" id="emailInput" name="email" placeholder="Masukkan email" required>
            </div>
            <div class="form-group">
                <label for="passwordInput">Password</label>
                <input type="password" class="form-control" id="passwordInput" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <!-- <a href="forgot_password.php" class="btn btn-secondary w-100 mt-2">Lupa password?</a> -->
        <a href="register.php" class="btn btn-secondary w-100 mt-2">Tidak punya akun? Register</a>
        <?php
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

            session_start();
            if (isset($_SESSION['login'])) {
                header('Location: index.php');
            } 

            require_once 'config_db.php';

            $db = new ConfigDB();
            $conn = $db->connect();

            // function checkNum($number) {
            //     if($number>1) {
            //       throw new Exception("Value must be 1 or below");
            //     }
            //     return true;
            //   }
            // function logError($error) {
            //     error_log($error, 3, 'error.log');
            //  }
            //  try {
            //     echo checkNum(2);	
            // } catch (Exception $e) {
            //     logError($e->getMessage());
            //     echo 'Error : '.$e->getMessage();
            // }
                
            // echo 'Finish';

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $email = $_POST['email'];
                $password = $_POST['password'];

                $query = "SELECT id, email, nama, password FROM user WHERE email = '$email'";
                $queryExecute = $conn->query($query);

                if ($queryExecute->num_rows > 0) {
                    $user = $queryExecute->fetch_assoc();
                    $isPasswordMatch = password_verify($password, $user['password']);
                    if ($isPasswordMatch) {
                        $_SESSION['login'] = true;
                        $_SESSION['userId'] = $user['id'];
                        $_SESSION['userName'] = $user['nama'];

                        setcookie('clientId', $user['id'], time() + 86400, '/');
                        setcookie('clientSecret', hash('sha256', $user['email']), time() + 86400, '/');
                        header('Location: index.php');
                    } else {
                        echo "<div class='alert alert-danger mt-3' role='alert'>Email atau Password salah</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger mt-3' role='alert'>Email atau Password salah</div>";
                }
            }
        ?>
    </div>
</body>
</html>
