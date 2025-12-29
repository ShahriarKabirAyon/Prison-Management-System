<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN"]);

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("Invalid ID");

$stmt = $conn->prepare("SELECT StaffID, FullName FROM Staff WHERE StaffID=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$s = $stmt->get_result()->fetch_assoc();
if (!$s) die("Staff not found");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $d = $conn->prepare("DELETE FROM Guard WHERE StaffID=?");
  $d->bind_param("i", $id); $d->execute();

  $d = $conn->prepare("DELETE FROM AdminStaff WHERE StaffID=?");
  $d->bind_param("i", $id); $d->execute();

  $d = $conn->prepare("DELETE FROM MedicalStaff WHERE StaffID=?");
  $d->bind_param("i", $id); $d->execute();

  $d = $conn->prepare("DELETE FROM Warden WHERE StaffID=?");
  $d->bind_param("i", $id); $d->execute();

  // Delete main staff
  $d = $conn->prepare("DELETE FROM Staff WHERE StaffID=?");
  $d->bind_param("i", $id);
  $d->execute();

  header("Location: list.php");
  exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Delete Staff</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Delete Staff</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="view.php?id=<?= (int)$s["StaffID"] ?>">Back</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Confirm Delete</h1>

    <div class="alert">
      You are about to delete: <b><?= htmlspecialchars($s["FullName"]) ?></b> (ID: <?= (int)$s["StaffID"] ?>).
      This action cannot be undone.
    </div>

    <form method="POST">
      <div class="actions">
        <a class="btn btn-ghost" href="view.php?id=<?= (int)$s["StaffID"] ?>">Cancel</a>
        <button type="submit">Yes, Delete</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>