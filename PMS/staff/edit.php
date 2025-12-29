<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN"]); // Staff manage only ADMIN

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("Invalid ID");

$msg = "";

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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $fullName   = trim($_POST["FullName"] ?? "");
  $phone      = trim($_POST["Phone"] ?? "");
  $joinDate   = $_POST["JoinDate"] ?? "";
  $address    = trim($_POST["Address"] ?? "");
  $baseSalary = trim($_POST["BaseSalary"] ?? "");

  if ($fullName === "" || $joinDate === "" || $baseSalary === "") {
    $msg = "Please fill all required fields.";
  } else {

    $u = $conn->prepare("UPDATE Staff SET FullName=?, Phone=?, JoinDate=?, Address=?, BaseSalary=? WHERE StaffID=?");
    $u->bind_param("ssssdi", $fullName, $phone, $joinDate, $address, $baseSalary, $id);

    if ($u->execute()) {

      if ($guard) {
        $rank  = trim($_POST["Rank"] ?? "");
        $shift = trim($_POST["AssignedShiftType"] ?? "");
        $u2 = $conn->prepare("UPDATE Guard SET `Rank`=?, AssignedShiftType=? WHERE StaffID=?");
        $u2->bind_param("ssi", $rank, $shift, $id);
        $u2->execute();
      }

      if ($admin) {
        $dept = trim($_POST["Department"] ?? "");
        $u2 = $conn->prepare("UPDATE AdminStaff SET Department=? WHERE StaffID=?");
        $u2->bind_param("si", $dept, $id);
        $u2->execute();
      }

      if ($medical) {
        $spec = trim($_POST["Specialty"] ?? "");
        $lic  = trim($_POST["LicenseNo"] ?? "");
        $u2 = $conn->prepare("UPDATE MedicalStaff SET Specialty=?, LicenseNo=? WHERE StaffID=?");
        $u2->bind_param("ssi", $spec, $lic, $id);
        $u2->execute();
      }

      if ($warden) {
        $resp = trim($_POST["ResponsibilityArea"] ?? "");
        $u2 = $conn->prepare("UPDATE Warden SET ResponsibilityArea=? WHERE StaffID=?");
        $u2->bind_param("si", $resp, $id);
        $u2->execute();
      }

      header("Location: view.php?id=".$id);
      exit;

    } else {
      $msg = "Error: " . $conn->error;
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Edit Staff</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Edit Staff</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="view.php?id=<?= $id ?>">Back</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">

  <div class="card">
    <h1>Edit Staff</h1>
    <p class="small">Update staff basic info and subtype details.</p>

    <?php if($msg): ?>
      <div class="alert"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST">
      <h2>Basic Information</h2>

      <label>Full Name</label>
      <input name="FullName" value="<?= htmlspecialchars($s["FullName"]) ?>" required>

      <label>Phone</label>
      <input name="Phone" value="<?= htmlspecialchars($s["Phone"] ?? "") ?>">

      <label>Join Date</label>
      <input type="date" name="JoinDate" value="<?= htmlspecialchars($s["JoinDate"]) ?>" required>

      <label>Address</label>
      <input name="Address" value="<?= htmlspecialchars($s["Address"] ?? "") ?>">

      <label>Base Salary</label>
      <input type="number" step="0.01" name="BaseSalary" value="<?= htmlspecialchars($s["BaseSalary"]) ?>" required>

      <h2>Subtype</h2>

      <?php if($guard): ?>
        <p class="badge">Guard</p>
        <label>Rank</label>
        <input name="Rank" value="<?= htmlspecialchars($guard["Rank"] ?? "") ?>">
        <label>Shift Type</label>
        <input name="AssignedShiftType" value="<?= htmlspecialchars($guard["AssignedShiftType"] ?? "") ?>">

      <?php elseif($admin): ?>
        <p class="badge">Admin Staff</p>
        <label>Department</label>
        <input name="Department" value="<?= htmlspecialchars($admin["Department"] ?? "") ?>">

      <?php elseif($medical): ?>
        <p class="badge">Medical Staff</p>
        <label>Specialty</label>
        <input name="Specialty" value="<?= htmlspecialchars($medical["Specialty"] ?? "") ?>">
        <label>License No</label>
        <input name="LicenseNo" value="<?= htmlspecialchars($medical["LicenseNo"] ?? "") ?>">

      <?php elseif($warden): ?>
        <p class="badge">Warden</p>
        <label>Responsibility Area</label>
        <input name="ResponsibilityArea" value="<?= htmlspecialchars($warden["ResponsibilityArea"] ?? "") ?>">

      <?php else: ?>
        <div class="alert">No subtype record found for this staff.</div>
      <?php endif; ?>

      <button type="submit">Update Staff</button>
    </form>
  </div>

  <div class="card">
    <h2>Danger Zone</h2>
    <p class="small">Deleting a staff will remove the record permanently.</p>
    <div class="actions">
      <a class="btn btn-danger" href="delete.php?id=<?= $id ?>">Delete Staff</a>
    </div>
  </div>

</div>

</body>
</html>