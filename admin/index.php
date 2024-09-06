<?php
// Start session if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

if (isset($_SESSION['username'])){
  header("Location: dashboard.php");
}