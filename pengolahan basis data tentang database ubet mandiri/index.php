<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "ubetmandiri1");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions                                                   
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_user'])) {
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Start transaction
        $conn->begin_transaction();

        try {
            // Check if email already exists
            $checkEmail = $conn->prepare("SELECT * FROM Pengguna WHERE Email = ?");
            $checkEmail->bind_param("s", $email);
            $checkEmail->execute();
            $result = $checkEmail->get_result();

            if ($result->num_rows > 0) {
                // Email already exists
                throw new Exception("Email sudah terdaftar!");
            } else {
                // Insert new user
                $sql = "INSERT INTO Pengguna (Nama, Email, Password) VALUES ('$nama', '$email', '$password')";
                if ($conn->query($sql) !== TRUE) {
                    throw new Exception("Terjadi kesalahan saat menambahkan pengguna.");
                }

                // Commit transaction if everything is successful
                $conn->commit();
                $success_message = "Pengguna berhasil ditambahkan!";
            }
        } catch (Exception $e) {
            // Rollback transaction if an error occurs
            $conn->rollback();
            $error_message = "Terjadi kesalahan: " . $e->getMessage();
        }
    } elseif (isset($_POST['add_service'])) {
        $nama_layanan = $_POST['nama_layanan'];
        $deskripsi = $_POST['deskripsi'];
        $harga = $_POST['harga'];

        // Start transaction for adding service
        $conn->begin_transaction();

        try {
            // Insert service data
            $sql = "INSERT INTO Layanan (Nama_Layanan, Deskripsi, Harga) VALUES ('$nama_layanan', '$deskripsi', '$harga')";
            if ($conn->query($sql) !== TRUE) {
                throw new Exception("Terjadi kesalahan saat menambahkan layanan.");
            }

            // Commit transaction if everything is successful
            $conn->commit();
            $success_message = "Layanan berhasil ditambahkan!";
        } catch (Exception $e) {
            // Rollback transaction if an error occurs
            $conn->rollback();
            $error_message = "Terjadi kesalahan: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_service'])) {
        $id_layanan = $_POST['id_layanan'];

        // Start transaction for deleting service
        $conn->begin_transaction();

        try {
            // Delete service data
            $sql = "DELETE FROM Layanan WHERE ID_Layanan = '$id_layanan'";
            if ($conn->query($sql) !== TRUE) {
                throw new Exception("Terjadi kesalahan saat menghapus layanan.");
            }

            // Commit transaction if everything is successful
            $conn->commit();
            $success_message = "Layanan berhasil dihapus!";
        } catch (Exception $e) {
            // Rollback transaction if an error occurs
            $conn->rollback();
            $error_message = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UbetMandiri1</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        header h1 {
            margin: 0;
            font-size: 2em;
        }
        form {
            background: #fff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form input, form textarea, form button {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            font-size: 1em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background: #333;
            color: #fff;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
<header>
    <h1>UbetMandiri1</h1>
</header>
<div class="container">
    <!-- Display error or success message -->
    <?php if (isset($error_message)): ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php elseif (isset($success_message)): ?>
        <p class="success"><?php echo $success_message; ?></p>
    <?php endif; ?>

    <!-- Form Add User -->
    <form method="POST">
        <h2>Tambah Pengguna</h2>
        <input type="text" name="nama" placeholder="Nama" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="add_user">Tambah Pengguna</button>
    </form>

    <!-- Form Add Service -->
    <form method="POST">
        <h2>Tambah Layanan</h2>
        <input type="text" name="nama_layanan" placeholder="Nama Layanan" required>
        <textarea name="deskripsi" placeholder="Deskripsi Layanan" required></textarea>
        <input type="number" step="0.01" name="harga" placeholder="Harga Layanan" required>
        <button type="submit" name="add_service">Tambah Layanan</button>
    </form>

    <!-- Form Delete Service -->
    <form method="POST">
        <h2>Hapus Layanan</h2>
        <input type="number" name="id_layanan" placeholder="ID Layanan" required>
        <button type="submit" name="delete_service">Hapus Layanan</button>
    </form>

    <!-- Display Users -->
    <h2>Data Pengguna</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM Pengguna");
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['ID_User']}</td><td>{$row['Nama']}</td><td>{$row['Email']}</td></tr>";
        }
        ?>
    </table>

    <!-- Display Services -->
    <h2>Data Layanan</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nama Layanan</th>
            <th>Deskripsi</th>
            <th>Harga</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM Layanan");
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['ID_Layanan']}</td><td>{$row['Nama_Layanan']}</td><td>{$row['Deskripsi']}</td><td>{$row['Harga']}</td></tr>";
        }
        ?>
    </table>

    <!-- Display Transactions -->
    <h2>Data Transaksi</h2>
    <table>
        <tr>
            <th>ID Transaksi</th>
            <th>ID Pengguna</th>
            <th>ID Layanan</th>
            <th>Tanggal</th>
            <th>Jumlah Barang</th>
            <th>Total Pembayaran</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM Transaksi");
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['id_transaksi']}</td><td>{$row['id_user']}</td><td>{$row['id_layanan']}</td><td>{$row['tanggal']}</td><td>{$row['jumlah_barang']}</td><td>{$row['total_pembayaran']}</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
