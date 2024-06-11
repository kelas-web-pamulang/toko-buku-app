<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Insert Data</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            max-width: 500px;
            border-radius: 10px;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }
        .btn-info:hover {
            background-color: #117a8b;
            border-color: #117a8b;
        }
    </style>
</head>
<body>
<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}
// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie('clientId', '', time() - 3600, '/');
    setcookie('clientSecret', '', time() - 3600, '/');
    header('Location: login.php');
    exit();
}

date_default_timezone_set('Asia/Jakarta');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once 'config_db.php';

$db = new ConfigDB();
$conn = $db->connect();
?>
<div class="container">
    <h1 class="text-center mb-4">Form Data Buku</h1>
    <div class="card">
        <div class="card-body">
            <form action="" method="post">
                <div class="form-group">
                    <label for="nameInput">Nama Buku</label>
                    <input type="text" class="form-control" id="nameInput" name="nama_buku" placeholder="Masukkan nama buku" required>
                </div>
                <div class="form-group">
                    <label for="nameInput">Nama Penerbit</label>
                    <input type="text" class="form-control" id="nameInput" name="nama_penerbit" placeholder="Masukkan nama penerbit" required>
                </div>
                <div class="form-group">
                    <label for="nameInput">Tahun Penerbit</label>
                    <input type="number" class="form-control" id="nameInput" name="tahun_penerbit" placeholder="Masukkan tahun penerbit" required>
                </div>
                <div class="form-group">
                    <label for="namaGenre">Nama Genre</label>
                    <?php
                        $namaGenre = $conn->query("SELECT id_genre, nama_genre FROM genre");
                        echo "<select class='form-control' id='namaGenre' name='id_genre' required>";
                        echo "<option value=''>Pilih Genre</option>";
                        while ($row = $namaGenre->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row['id_genre']) . "'>" . htmlspecialchars($row['nama_genre']) . "</option>";
                        }
                        echo "</select>";
                    ?>
                </div>
                <div class="form-group">
                    <label for="namaKategori">Pilihan Kategori</label>
                    <?php
                        $namaKategori = $conn->query("SELECT id_kategori, nama_kategori FROM kategori");
                        echo "<select class='form-control' id='namaKategori' name='id_kategori' required>";
                        echo "<option value=''>Pilih Kategori</option>";
                        while ($row = $namaKategori->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row['id_kategori']) . "'>" . htmlspecialchars($row['nama_kategori']) . "</option>";
                        }
                        echo "</select>";
                    ?>
                </div>
                <div class="form-group">
                    <label for="nameInput">Stok</label>
                    <input type="number" class="form-control" id="nameInput" name="stok" placeholder="Masukkan jumlah stok" required>
                </div>
                <div class="form-group">
                    <label for="nameInput">Harga</label>
                    <input type="number" class="form-control" id="nameInput" name="harga" placeholder="Masukkan harga" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="index.php" class="btn btn-info">Kembali</a>
            </form>
        </div>
    </div>

    <?php

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = htmlspecialchars(trim($_POST['nama_buku']));
        $namaPenerbit = htmlspecialchars(trim($_POST['nama_penerbit']));
        $tahunPenerbit = htmlspecialchars(trim($_POST['tahun_penerbit']));
        $genre = htmlspecialchars(trim($_POST['id_genre']));
        $kategori = htmlspecialchars(trim($_POST['id_kategori']));
        $stok = htmlspecialchars(trim($_POST['stok']));
        $harga = htmlspecialchars(trim($_POST['harga']));
        $createAt = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO buku (nama_buku, nama_penerbit, tahun_penerbit, id_genre, id_kategori, stok, harga, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiiids", $nama, $namaPenerbit, $tahunPenerbit, $genre, $kategori, $stok, $harga, $createAt);

        if ($stmt->execute() === TRUE) {
            echo "<div class='alert alert-success mt-3' role='alert'>Data inserted successfully</div>";
        } else {
            echo "<div class='alert alert-danger mt-3' role='alert'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
    $conn->close();
    ?>
</div>
</body>
</html>
