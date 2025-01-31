<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_admin = $_POST['id_admin'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profilepicture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profilepicture"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profilepicture"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowed_types = array("jpg", "png", "jpeg", "gif");
    if (!in_array($imageFileType, $allowed_types)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {

            $username = $_SESSION['username']; // Get username from session
            $new_filename = $username . "." . $imageFileType; // Rename fil n e
            $new_filepath = $target_dir . $new_filename; // New file path

            if (move_uploaded_file($_FILES["profilepicture"]["tmp_name"], $new_filepath)) {
                $query = "UPDATE admin SET profilepicture = '$new_filename' WHERE id_admin = $id_admin"; // Update database with new filename
                $result = mysqli_query($koneksi, $query);

                if ($result) {
                    $_SESSION['profilepicture'] = $new_filename; // Update session
                    header("Location: settings.php");
                    exit();
                } else {
                    echo "Error updating profile picture: " . mysqli_error($koneksi);
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
            $result = mysqli_query($koneksi, $query);

            if ($result) {
                header("Location: settings.php");
                exit();
            } else {
                echo "Error updating profile picture: " . mysqli_error($koneksi);
            }

    }
}
?>