<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function require_login() {
  if (!isset($_SESSION["UserID"])) {
    header("Location: /PMS/auth/login.php");
    exit;
  }
}

function require_role($roles) {
  require_login();
  $role = $_SESSION["Role"] ?? "";
  if (!in_array($role, $roles)) {
    die("Access denied");
  }
}