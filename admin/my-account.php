<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="aset/css/styles.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">My Account</h1>

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
                    <div class="card-header">Account Details</div>
                    <div class="card-body">
                        <p><strong>Username:</strong> <?php echo $data['username']; ?></p>
                        <p><strong>Email:</strong> <?php echo $data['email']; ?></p>
                        <p><strong>Nama Lengkap:</strong> <?php echo $data['nama_lengkap']; ?></p>
                        <p><strong>Phone Number:</strong> <?php echo $data['phone_number']; ?></p>
                        <p><strong>Profile Picture:</strong></p>
                        <img src="uploads/<?php echo $data['profilepicture']; ?>" alt="Profile Picture" style="width: 100px; height: auto;">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Edit Account</div>
                    <div class="card-body">
                        <form method="post" action="proses_edit_akun.php" enctype="multipart/form-data">
                            <input type="hidden" name="id_admin" value="<?php echo $data['id_admin']; ?>">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" class="form-control" name="username" value="<?php echo $data['username']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" name="email" value="<?php echo $data['email']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="nama_lengkap">Nama Lengkap:</label>
                                <input type="text" class="form-control" name="nama_lengkap" value="<?php echo $data['nama_lengkap']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone_number">Phone Number:</label>
                                <input type="text" class="form-control" name="phone_number" value="<?php echo $data['phone_number']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="profilepicture">Profile Picture:</label>
                                <input type="file" class="form-control" name="profilepicture">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
