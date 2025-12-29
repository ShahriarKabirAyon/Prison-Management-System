<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN","WARDEN"]);

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("Invalid ID");

$stmt = $conn->prepare("SELECT PrisonerID, FullName FROM Prisoner WHERE PrisonerID=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
if (!$p) die("Prisoner not found");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $del = $conn->prepare("DELETE FROM Prisoner WHERE PrisonerID=?");
  $del->bind_param("i", $id);
  $del->execute();

  header("Location: list.php");
  exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Delete Prisoner</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Delete Prisoner</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="view.php?id=<?= (int)$p["PrisonerID"] ?>">Back</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Confirm Delete</h1>
    <div class="alert">
      You are about to delete: <b><?= htmlspecialchars($p["FullName"]) ?></b> (ID: <?= (int)$p["PrisonerID"] ?>).
      This action cannot be undone.
    </div>

    <form method="POST">
      <div class="actions">
        <a class="btn btn-ghost" href="view.php?id=<?= (int)$p["PrisonerID"] ?>">Cancel</a>
        <button type="submit">Yes, Delete</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>