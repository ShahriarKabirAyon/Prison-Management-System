<?php
require_once(__DIR__ . "/config/db.php");
require_once(__DIR__ . "/includes/auth.php");
require_login();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title> Prison Management System - PMS | Dashboard</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>Prison Management System - PMS</span>
      <span class="badge">PMS</span>
    </div>

    <div class="actions">
      <span class="badge"><?= htmlspecialchars($_SESSION["Role"]) ?></span>
      <a class="btn btn-ghost" href="auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">

  <div class="card">
    <h1>Dashboard</h1>
    <p class="small">
      Welcome, <b><?= htmlspecialchars($_SESSION["Username"]) ?></b>.  
      Use the features below to manage the prison management system
    </p>
  </div>

  <div class="card">
    <h2>Core Features</h2>
    <div class="actions">
      <a class="btn" href="prisoners/list.php">Prisoners</a>
      <a class="btn" href="staff/list.php">Staff</a>
      <a class="btn" href="users/list.php">Users</a>
    </div>
  </div>

  <div class="card">
    <h2>Infrastructure</h2>
    <div class="actions">
      <a class="btn" href="prison/list.php">Prisons</a>
      <a class="btn" href="blocks/list.php">Blocks</a>
      <a class="btn" href="cells/list.php">Cells</a>
    </div>
  </div>

  <div class="card">
  <h2>Operations</h2>
  <div class="actions">
    <a class="btn" href="tasks/list.php">Task Assignments</a>
    <a class="btn" href="visitors/list.php">Visitors</a>
    <a class="btn" href="visits/list.php">Visits</a>
    <a class="btn" href="medical/list.php">Medical History</a>
  </div>
</div>

  <div class="card">
    <h2>System Info</h2>
    <p class="small">
      • Built with <b>HTML, CSS, PHP, MySQL</b><br>
      <br>
      <br>
      ©All rights resereved by Jafran, Orni & Ayon<br>
    </p>
  </div>

</div>

</body>
</html>