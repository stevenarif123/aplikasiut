<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
  
  // Redirect to index.php if session already exists
  if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit; // Stop further execution
  }
// Set the directory you want to zip
$directory = "./";

// Create a new ZipArchive
$zip = new ZipArchive();

// Generate a unique filename for the ZIP file
$zipFileName = 'files_' . date('Y-m-d_H-i-s') . '.zip';

// Create the ZIP file
if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    exit("Unable to open file $zipFileName\n");
}

// Add all files and folders to the ZIP
addFolderToZip($directory, $zip, $directory);

// Close the ZIP file
$zip->close();

// Set the headers to prompt the user to download the file
header("Content-type: application/zip");
header("Content-Disposition: attachment; filename=$zipFileName");
header("Pragma: no-cache");
header("Expires: 0");

// Output the ZIP file contents
readfile($zipFileName);

// Delete the ZIP file after download
unlink($zipFileName);

// Function to recursively add folders and files to the ZIP
function addFolderToZip($folder, $zip, $baseFolder) {
    // Open the folder
    $files = scandir($folder);

    foreach ($files as $file) {
        // Skip the current and parent directories
        if ($file == '.' || $file == '..') {
            continue;
        }

        // Get the full path of the file or folder
        $path = $folder . '/' . $file;
        $localPath = str_replace($baseFolder . '/', '', $path);

        // If it's a directory, recursively add its contents to the ZIP
        if (is_dir($path)) {
            $zip->addEmptyDir($localPath);
            addFolderToZip($path, $zip, $baseFolder);
        }
        // If it's a file, add it to the ZIP
        else {
            $zip->addFile($path, $localPath);
        }
    }
}
?>