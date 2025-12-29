<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");

if (isset($_SESSION["UserID"])) {
  header("Location: ../index.php");
  exit;
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST["Username"] ?? "");
  $password = $_POST["Password"] ?? "";

  if ($username === "" || $password === "") {
    $msg = "Please enter username and password.";
  } else {

    $stmt = $conn->prepare("SELECT UserID, Username, PasswordHash, Role FROM users WHERE Username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();

    if ($u && password_verify($password, $u["PasswordHash"])) {
      $_SESSION["UserID"] = (int)$u["UserID"];
      $_SESSION["Username"] = $u["Username"];
      $_SESSION["Role"] = $u["Role"];

      header("Location: ../index.php");
      exit;
    } else {
      $msg = "Wrong username or password.";
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Prison Management System - PMS | Login Dashboard</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>Prison Management System - PMS | Login Dashboard</span>
      <span class="badge">Login</span>
    </div>
    
  </div>
</div>

<div class="container">

  <div class="card" style="max-width:520px; margin: 30px auto;">
    <h1>Sign in</h1>
    <p class="small">Use your username and password to access the system</p>

    <?php if($msg): ?>
      <div class="alert"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <label>Username</label><br>
      <input name="Username" required><br>

      <label>Password</label><br>
      <input type="password" name="Password" required><br>

      <button type="submit">Login</button>
    </form>

    <hr>
  </div>

</div>

</body>
</html>