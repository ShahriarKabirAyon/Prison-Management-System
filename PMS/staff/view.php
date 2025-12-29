<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_login();

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("Invalid ID");

$stmt = $conn->prepare("SELECT * FROM Staff WHERE StaffID=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$s = $stmt->get_result()->fetch_assoc();
if (!$s) die("Staff not found");

$guard = $admin = $medical = $warden = null;

$q = $conn->prepare("SELECT * FROM Guard WHERE StaffID=?");
$q->bind_param("i", $id); $q->execute();
$guard = $q->get_result()->fetch_assoc();

$q = $conn->prepare("SELECT * FROM AdminStaff WHERE StaffID=?");
$q->bind_param("i", $id); $q->execute();
$admin = $q->get_result()->fetch_assoc();

$q = $conn->prepare("SELECT * FROM MedicalStaff WHERE StaffID=?");
$q->bind_param("i", $id); $q->execute();
$medical = $q->get_result()->fetch_assoc();

$q = $conn->prepare("SELECT * FROM Warden WHERE StaffID=?");
$q->bind_param("i", $id); $q->execute();
$warden = $q->get_result()->fetch_assoc();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Staff Details</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Staff Details</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="list.php">Back</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">

  <div class="card">
    <h1><?= htmlspecialchars($s["FullName"]) ?></h1>
    <p class="small">Staff ID: <?= (int)$s["StaffID"] ?></p>

    <hr>

    <p><b>Phone:</b> <?= htmlspecialchars($s["Phone"] ?? "") ?></p>
    <p><b>Join Date:</b> <?= htmlspecialchars($s["JoinDate"]) ?></p>
    <p><b>Address:</b> <?= htmlspecialchars($s["Address"] ?? "") ?></p>
    <p><b>Base Salary:</b> <?= htmlspecialchars($s["BaseSalary"]) ?></p>
  </div>

  <div class="card">
    <h2>Subtype Information</h2>

    <?php if($guard): ?>
      <p><b>Type:</b> Guard</p>
      <p><b>Rank:</b> <?= htmlspecialchars($guard["Rank"] ?? "") ?></p>
      <p><b>Shift:</b> <?= htmlspecialchars($guard["AssignedShiftType"] ?? "") ?></p>

    <?php elseif($admin): ?>
      <p><b>Type:</b> Admin Staff</p>
      <p><b>Department:</b> <?= htmlspecialchars($admin["Department"] ?? "") ?></p>

    <?php elseif($medical): ?>
      <p><b>Type:</b> Medical Staff</p>
      <p><b>Specialty:</b> <?= htmlspecialchars($medical["Specialty"] ?? "") ?></p>
      <p><b>License No:</b> <?= htmlspecialchars($medical["LicenseNo"] ?? "") ?></p>

    <?php elseif($warden): ?>
      <p><b>Type:</b> Warden</p>
      <p><b>Responsibility Area:</b> <?= htmlspecialchars($warden["ResponsibilityArea"] ?? "") ?></p>

    <?php else: ?>
      <div class="alert">No subtype record found.</div>
    <?php endif; ?>
  </div>

  <?php if(isset($_SESSION["Role"]) && $_SESSION["Role"] === "ADMIN"): ?>
    <div class="card">
      <div class="actions">
        <a class="btn" href="edit.php?id=<?= (int)$s["StaffID"] ?>">Edit Staff</a>
        <a class="btn btn-danger" href="delete.php?id=<?= (int)$s["StaffID"] ?>">Delete Staff</a>
      </div>
    </div>
  <?php endif; ?>

</div>

</body>
</html>