<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN"]);

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $fullName   = trim($_POST["FullName"] ?? "");
  $phone      = trim($_POST["Phone"] ?? "");
  $joinDate   = $_POST["JoinDate"] ?? "";
  $address    = trim($_POST["Address"] ?? "");
  $baseSalary = trim($_POST["BaseSalary"] ?? "");
  $staffType  = $_POST["StaffType"] ?? "";

  $rank         = trim($_POST["Rank"] ?? "");
  $shiftType    = trim($_POST["AssignedShiftType"] ?? "");
  $department   = trim($_POST["Department"] ?? "");
  $specialty    = trim($_POST["Specialty"] ?? "");
  $licenseNo    = trim($_POST["LicenseNo"] ?? "");
  $responsibility = trim($_POST["ResponsibilityArea"] ?? "");

  if ($fullName === "" || $joinDate === "" || $baseSalary === "" || $staffType === "") {
    $msg = "Please fill all required fields.";
  } else {

    $stmt = $conn->prepare("INSERT INTO Staff (FullName, Phone, JoinDate, Address, BaseSalary) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssd", $fullName, $phone, $joinDate, $address, $baseSalary);

    if ($stmt->execute()) {
      $newStaffID = $conn->insert_id;

      if ($staffType === "Guard") {
        $st = $conn->prepare("INSERT INTO Guard (StaffID, `Rank`, AssignedShiftType) VALUES (?, ?, ?)");
        $st->bind_param("iss", $newStaffID, $rank, $shiftType);
        $st->execute();
      }

      if ($staffType === "AdminStaff") {
        $st = $conn->prepare("INSERT INTO AdminStaff (StaffID, Department) VALUES (?, ?)");
        $st->bind_param("is", $newStaffID, $department);
        $st->execute();
      }

      if ($staffType === "MedicalStaff") {
        $st = $conn->prepare("INSERT INTO MedicalStaff (StaffID, Specialty, LicenseNo) VALUES (?, ?, ?)");
        $st->bind_param("iss", $newStaffID, $specialty, $licenseNo);
        $st->execute();
      }

      if ($staffType === "Warden") {
        $st = $conn->prepare("INSERT INTO Warden (StaffID, ResponsibilityArea) VALUES (?, ?)");
        $st->bind_param("is", $newStaffID, $responsibility);
        $st->execute();
      }

      header("Location: list.php");
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
  <title>PMS | Add Staff</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Add Staff</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="list.php">Back</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Add New Staff</h1>
    <p class="small">Create staff and store subtype information (Guard/Admin/Medical/Warden).</p>

    <?php if($msg): ?>
      <div class="alert"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST">

      <h2>Basic Information</h2>

      <label>Full Name</label>
      <input name="FullName" required>

      <label>Phone</label>
      <input name="Phone">

      <label>Join Date</label>
      <input type="date" name="JoinDate" required>

      <label>Address</label>
      <input name="Address">

      <label>Base Salary</label>
      <input type="number" step="0.01" name="BaseSalary" required>

      <label>Staff Type</label>
      <select name="StaffType" required>
        <option value="">-- Select --</option>
        <option value="Guard">Guard</option>
        <option value="AdminStaff">Admin Staff</option>
        <option value="MedicalStaff">Medical Staff</option>
        <option value="Warden">Warden</option>
      </select>

      <h2>Subtype Details</h2>
      <p class="small">Fill only the section that matches the selected staff type.</p>

      <label>Guard Rank</label>
      <input name="Rank" placeholder="e.g., Sergeant">

      <label>Guard Shift Type</label>
      <input name="AssignedShiftType" placeholder="Morning / Evening / Night">

      <label>Admin Department</label>
      <input name="Department" placeholder="HR / IT / Accounts">

      <label>Medical Specialty</label>
      <input name="Specialty" placeholder="Doctor / Nurse">

      <label>Medical License No</label>
      <input name="LicenseNo" placeholder="Unique license number">

      <label>Warden Responsibility Area</label>
      <input name="ResponsibilityArea" placeholder="Full prison / Block A">

      <button type="submit">Save Staff</button>
    </form>
  </div>
</div>

</body>
</html>