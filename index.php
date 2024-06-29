<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Toko Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1512820790803-83ca734da794') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .overlay {
            background-color: rgba(0, 0, 0, 0.7);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .content {
            position: relative;
            z-index: 1;
            flex: 1;
            padding-top: 70px; /* Height of the sticky header */
            padding-bottom: 60px; /* Height of the sticky footer */
            overflow-y: auto;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            margin-top: 30px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table th {
            position: sticky;
            top: 70px; /* Adjust based on the height of the navbar */
            z-index: 2;
            background-color: rgba(0, 123, 255, 0.7); /* Match the navbar color */
        }
        .search-form input, .search-form select, .search-form button {
            margin-right: 10px;
        }
        .header-buttons a {
            margin-right: 10px;
        }
        .header-buttons .btn {
            width: 100px;
        }
        .navbar-custom {
            background-color: rgba(0, 123, 255, 0.7);
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
        }
        footer {
            background-color: #343a40;
            color: white;
            padding: 20px;
            text-align: center;
            position: sticky;
            bottom: 0;
            width: 100%;
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
?>

    <div class="overlay"></div>
    <div class="container content">
        <nav class="navbar navbar-expand-lg navbar-custom mb-3">
            <div class="container-fluid">
                <a class="navbar-brand text-white" href="#">Toko Buku Sinar Pamulang</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a id="logoutButton" href="logout.php" class="btn btn-danger">Logout</a>
                        </li>
                        <!-- <li class="nav-item">
                        <a class="nav-link text-white" href="profile.php">Profil</a>
                    </li> -->
                    </ul>
                </div>
            </div>
        </nav>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center">
                <h1>Selamat datang, <?php echo htmlspecialchars($_SESSION['userName']); ?>!</h1>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <form action="" method="get" class="d-flex search-form">
                            <input class="form-control" placeholder="Cari Data" name="search"/>
                            <select name="search_by" class="form-select">
                                <option value="">Search All</option>
                                <option value="nama_buku">Nama Buku</option>
                                <option value="nama_penerbit">Nama Penerbit</option>
                                <option value="tahun_penerbit">Tahun Penerbit</option>
                            </select>
                            <button type="submit" class="btn btn-success">Cari</button>
                        </form>
                    </div>
                    <div class="col-md-4 d-flex justify-content-end header-buttons">
                        <a href="insert.php" class="btn btn-success">Tambah Data</a>
                    </div>
                </div>
                <table class="table table-striped table-bordered table-hover">
                    <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Buku</th>
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>Genre</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Tgl. Buat</th>
                        <th colspan="2">Pilihan</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
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
                      
                    require_once 'config_db.php';

                    $db = new ConfigDB();
                    $conn = $db->connect();

                //  function checkNum($number) {
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
echo $name;
                    $conditional = [];
                    if (isset($_GET['search'])) {
                        $search = $_GET['search'];
                        $search_by = $_GET['search_by'];
                        if ($search_by == 'nama_buku') {
                            $conditional['AND nama_buku LIKE'] = "%$search%";
                        } else if ($search_by == 'tahun_penerbit') {
                            $conditional['AND tahun_penerbit LIKE'] = "%$search%";
                        } else if ($search_by == 'nama_penerbit') {
                            $conditional['AND nama_penerbit LIKE'] = "%$search%";
                        }
                    } else if (isset($_GET['delete'])) {
                        $query = $db->update('buku', [
                            'deleted_at' => date('Y-m-d H:i:s')
                        ], $_GET['delete']);
                    }

                    $perPage = 10;
                    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($currentPage - 1) * $perPage;

                    $countQuery = "SELECT COUNT(*) AS total FROM buku b WHERE b.deleted_at IS NULL";
                    if (!empty($conditional)) {
                        foreach ($conditional as $key => $value) {
                            $countQuery .= " $key '$value'";
                        }
                    }

                    $countResult = $conn->query($countQuery);
                    $totalRows = $countResult->fetch_assoc()['total'];
                    $totalPages = ceil($totalRows / $perPage);

                    $query = "SELECT b.id_buku, b.nama_buku, b.nama_penerbit, b.tahun_penerbit, g.nama_genre,
                    k.nama_kategori, b.stok, b.harga, b.created_at
                    FROM buku b
                    LEFT JOIN genre g ON b.id_genre = g.id_genre
                    LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
                    WHERE b.deleted_at IS NULL";

                    if (!empty($conditional)) {
                        foreach ($conditional as $key => $value) {
                            $query .= " $key '$value'";
                        }
                    }

                    $query .= " ORDER BY b.created_at DESC"; // Add this line to sort by created_at in descending order
                    $query .= " LIMIT $offset, $perPage";

                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        foreach ($result as $key => $row) {
                            echo "<tr>";
                            echo "<td>".($offset + $key + 1)."</td>";
                            echo "<td>".$row['nama_buku']."</td>";
                            echo "<td>".$row['nama_penerbit']."</td>";
                            echo "<td>".$row['tahun_penerbit']."</td>";
                            echo "<td>".$row['nama_genre']."</td>";
                            echo "<td>".$row['nama_kategori']."</td>";
                            echo "<td>".$row['stok']."</td>";
                            echo "<td>".$row['harga']."</td>";
                            echo "<td>".$row['created_at']."</td>";
                            echo "<td><a class='btn btn-sm btn-info' href='update.php?id=".$row['id_buku']."'>Update</a></td>";
                            echo "<td><a class='btn btn-sm btn-danger delete-button' href='index.php?delete=".$row['id_buku']."'>Delete</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='11' class='text-center'>No Data</td></tr>";
                    }

                    $db->close();
                    ?>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php if($currentPage <= 1) echo 'disabled'; ?>">
                            <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for($page = 1; $page <= $totalPages; $page++): ?>
                            <li class="page-item <?php if($currentPage == $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $page; ?>"><?php echo $page; ?></a></li>
                        <?php endfor; ?>
                        <li class="page-item <?php if($currentPage >= $totalPages) echo 'disabled'; ?>">
                            <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <footer>
        <div class="container">
            Tugas Project-Praktisi Mengajar dibuat oleh Alfa Azriansah Yasin & Dery Saputra
        </div>
    </footer>
    <script>
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const confirmed = confirm('Apakah Anda yakin ingin menghapus data ini?');
                if (confirmed) {
                    window.location.href = this.href;
                }
            });
        });

        document.getElementById('logoutButton').addEventListener('click', function(event) {
            event.preventDefault();
            const confirmed = confirm('Apakah Anda yakin ingin logout?');
            if (confirmed) {
                window.location.href = this.href;
            }
        });
    </script>
</body>
</html>
