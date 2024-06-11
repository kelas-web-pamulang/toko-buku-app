<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Update Data</title>
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
    require_once 'config_db.php';

    $db = new ConfigDB();
    $conn = $db->connect();

    $id_buku = $_GET['id'];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = htmlspecialchars(trim($_POST['nama_buku']));
        $namaPenerbit = htmlspecialchars(trim($_POST['nama_penerbit']));
        $tahunPenerbit = htmlspecialchars(trim($_POST['tahun_penerbit']));
        $genre = htmlspecialchars(trim($_POST['id_genre']));
        $kategori = htmlspecialchars(trim($_POST['id_kategori']));
        $stokTambah = htmlspecialchars(trim($_POST['stok_tambah'])) ?? 0;
        $stokKurang = htmlspecialchars(trim($_POST['stok_kurang'])) ?? 0;
        $harga = htmlspecialchars(trim($_POST['harga']));

        // Fetch current stock
        $result = $db->select("buku", ['AND id_buku=' => $id_buku]);
        $current_stok = $result[0]['stok'];

        // Calculate new stock
        $new_stok = $current_stok + $stokTambah - $stokKurang;  // Change stock based on user input

        $data_buku = [
            'nama_buku' => $nama,
            'nama_penerbit' => $namaPenerbit,
            'tahun_penerbit' => $tahunPenerbit,
            'id_genre' => $genre,
            'id_kategori' => $kategori,
            'stok' => $new_stok,
            'harga' => $harga,
        ];

        $conn->begin_transaction();

        // Prepare and bind
        $stmt = $conn->prepare("UPDATE buku SET nama_buku=?, nama_penerbit=?, tahun_penerbit=?, id_genre=?, id_kategori=?, stok=?, harga=? WHERE id_buku=?");
        $stmt->bind_param("sssiiidi", $nama, $namaPenerbit, $tahunPenerbit, $genre, $kategori, $new_stok, $harga, $id_buku);

        if ($stmt->execute()) {
            $conn->commit();
            echo "<div class='alert alert-success mt-3' role='alert'>Data berhasil diperbaharui</div>";
        } else {
            $conn->rollback();
            echo "<div class='alert alert-danger mt-3' role='alert'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
        $result = $db->select("buku", ['AND id_buku=' => $id_buku]);
    } else {
        $result = $db->select("buku", ['AND id_buku=' => $id_buku]);
    }
    $buku = $result[0];
    ?>
    <div class="container">
        <h1 class="text-center mb-4">Ubah Data Buku</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="nameInput">Nama Buku</label>
                <input type="text" class="form-control" id="nameInput" name="nama_buku" placeholder="Masukkan nama buku" required value="<?php echo htmlspecialchars($buku['nama_buku']); ?>">
            </div>
            <div class="form-group">
                <label for="penerbitInput">Nama Penerbit</label>
                <input type="text" class="form-control" id="penerbitInput" name="nama_penerbit" placeholder="Masukkan nama penerbit" required value="<?php echo htmlspecialchars($buku['nama_penerbit']); ?>">
            </div>
            <div class="form-group">
                <label for="tahunInput">Tahun Penerbit</label>
                <input type="number" class="form-control" id="tahunInput" name="tahun_penerbit" placeholder="Masukkan tahun penerbit" required value="<?php echo htmlspecialchars($buku['tahun_penerbit']); ?>">
            </div>
            <div class="form-group">
                <label for="namaGenre">Nama Genre</label>
                <?php
                    $namaGenre = $conn->query("SELECT id_genre, nama_genre FROM genre");
                    echo "<select class='form-control' id='namaGenre' name='id_genre' required>";
                    echo "<option value=''>Pilih Genre</option>"; 
                    while ($row = $namaGenre->fetch_assoc()) {
                        $selected = ($buku['id_genre'] == $row['id_genre']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($row['id_genre']) . "' $selected>" . htmlspecialchars($row['nama_genre']) . "</option>";
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
                        $selected = ($buku['id_kategori'] == $row['id_kategori']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($row['id_kategori']) . "'$selected>" . htmlspecialchars($row['nama_kategori']) . "</option>";
                    }
                    echo "</select>";
                ?>
            </div>
            <div class="form-group">
                <label for="currentStokInput">Stok Saat Ini</label>
                <input type="number" class="form-control" id="currentStokInput" name="current_stok" readonly value="<?php echo htmlspecialchars($buku['stok']); ?>">
            </div>
            <div class="form-group">
                <label for="stokTambahInput">Tambah Stok</label>
                <input type="number" class="form-control" id="stokTambahInput" name="stok_tambah" placeholder="Masukkan jumlah stok tambahan" value="0">
            </div>
            <div class="form-group">
                <label for="stokKurangInput">Kurangi Stok</label>
                <input type="number" class="form-control" id="stokKurangInput" name="stok_kurang" placeholder="Masukkan jumlah stok pengurangan" value="0">
            </div>
            <div class="form-group">
                <label for="hargaInput">Harga</label>
                <input type="number" class="form-control" id="hargaInput" name="harga" placeholder="Masukkan harga" required value="<?php echo htmlspecialchars($buku['harga']); ?>">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                <a href="index.php" class="btn btn-info">Kembali</a>
            </div>
        </form>

        <?php
            $conn->close();
        ?>
    </div>
</body>
</html>
