<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="aset/css/styles.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Settings</h1>

        <?php
        session_start();
        include 'koneksi.php';

        if (!isset($_SESSION['id_admin'])) {
            header("Location: login.php");
            exit();
        }

        $id_admin = $_SESSION['id_admin'];
        $query = "SELECT * FROM admin WHERE id_admin = $id_admin";
        $result = mysqli_query($koneksi, $query);
        $data = mysqli_fetch_assoc($result);
        ?>

        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Change Password</div>
                    <div class="card-body">
                        <form method="post" action="proses_ganti_password.php">
                            <input type="hidden" name="id_admin" value="<?php echo $data['id_admin']; ?>">
                            <div class="form-group">
                                <label for="password_lama">Old Password:</label>
                                <input type="password" class="form-control" name="password_lama" required>
                            </div>
                            <div class="form-group">
                                <label for="password_baru">New Password:</label>
                                <input type="password" class="form-control" name="password_baru" required>
                            </div>
                            <div class="form-group">
                                <label for="konfirmasi_password">Confirm New Password:</label>
                                <input type="password" class="form-control" name="konfirmasi_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Profile Picture</div>
                    <div class="card-body">
                        <form method="post" action="proses_ganti_foto.php" enctype="multipart/form-data">
                            <input type="hidden" name="id_admin" value="<?php echo $data['id_admin']; ?>">
                            <div class="form-group">
							<img src="uploads/<?php echo $data['profilepicture']; ?>" alt="Profile Picture" width="100" height="100" class="mt-2 mb-2"><br>
                                <label for="profilepicture">Select a new profile picture:</label>
                                <input type="file" class="form-control-file" name="profilepicture" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Change Profile Picture</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <a href="pages-starter.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>