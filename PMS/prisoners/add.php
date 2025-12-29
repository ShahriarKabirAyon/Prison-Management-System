<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN","WARDEN"]);

$msg = "";

/* Load cells for assignment */
$cells = $conn->query("
  SELECT c.CellID, c.CellNumber,
         b.Name AS BlockName,
         p.Name AS PrisonName
  FROM Cell c
  JOIN Block b ON b.BlockID = c.BlockID
  JOIN Prison p ON p.PrisonID = b.PrisonID
  ORDER BY p.Name, b.Name, c.CellNumber
");

/* Handle form submit */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $fullName   = trim($_POST["FullName"] ?? "");
  $dob        = $_POST["DateOfBirth"] ?? "";
  $gender     = $_POST["Gender"] ?? "";
  $nid        = trim($_POST["NationalID"] ?? "");
  $admission  = $_POST["AdmissionDate"] ?? "";
  $status     = $_POST["Status"] ?? "";
  $security   = $_POST["SecurityLevel"] ?? "";

  $cellID = $_POST["CellID"] ?? "";
  $cellID = ($cellID === "") ? NULL : (int)$cellID;

  if ($fullName==="" || $dob==="" || $gender==="" || $nid==="" || $admission==="" || $status==="" || $security==="") {
    $msg = "Please fill all required fields.";
  } else {

    $stmt = $conn->prepare("
      INSERT INTO Prisoner
      (FullName, DateOfBirth, Gender, NationalID, AdmissionDate, Status, SecurityLevel, CellID)
      VALUES (?,?,?,?,?,?,?,?)
    ");

    $stmt->bind_param(
      "sssssssi",
      $fullName, $dob, $gender, $nid, $admission, $status, $security, $cellID
    );

    if ($stmt->execute()) {
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
  <title>PMS | Add Prisoner</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Add Prisoner</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="list.php">Back</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">

  <div class="card">
    <h1>Add New Prisoner</h1>
    <p class="small">Enter basic information and assign a cell (optional).</p>

    <?php if($msg): ?>
      <div class="alert"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST">

      <h2>Basic Information</h2>

      <label>Full Name</label><br>
      <input type="text" name="FullName" required><br>

      <label>Date of Birth</label><br>
      <input type="date" name="DateOfBirth" required><br>

      <label>Gender</label><br>
      <select name="Gender" required><br>
        <option value="">-- Select --</option>
        <option>Male</option>
        <option>Female</option>
        <option>Other</option>
      </select><br>

      <label>National ID</label><br>
      <input type="text" name="NationalID" required><br>

      <label>Admission Date</label><br>
      <input type="date" name="AdmissionDate" required><br>

      <label>Status</label><br>
      <select name="Status" required><br>
        <option value="">-- Select --</option>
        <option>UnderTrial</option>
        <option>Convicted</option>
        <option>Released</option>
        <option>Parole</option>
      </select><br>

      <label>Security Level</label><br>
      <select name="SecurityLevel" required><br>
        <option value="">-- Select --</option>
        <option>High</option>
        <option>Medium</option>
        <option>Low</option>
      </select>
<br>
<br>
      <h2>Accommodation</h2>

      <label>Assign Cell (optional)</label><br>
      <select name="CellID"><br>
        <option value="">-- Not Assigned --</option>
        <?php while($c = $cells->fetch_assoc()): ?>
          <option value="<?= (int)$c["CellID"] ?>">
            <?= htmlspecialchars($c["PrisonName"]." / ".$c["BlockName"]." / Cell ".$c["CellNumber"]) ?>
          </option>
        <?php endwhile; ?>
      </select><br>
<br>
<br>
      <button type="submit">Save Prisoner</button><br>
    </form>
  </div>

</div>

</body>
</html>