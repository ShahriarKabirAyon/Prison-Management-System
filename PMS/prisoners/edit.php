<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN","WARDEN"]);

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("Invalid ID");

$msg = "";

$stmt = $conn->prepare("SELECT * FROM Prisoner WHERE PrisonerID=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
if (!$p) die("Prisoner not found");

$cells = $conn->query("
  SELECT c.CellID, c.CellNumber,
         b.Name AS BlockName,
         pr.Name AS PrisonName
  FROM Cell c
  JOIN Block b ON b.BlockID = c.BlockID
  JOIN Prison pr ON pr.PrisonID = b.PrisonID
  ORDER BY pr.Name, b.Name, c.CellNumber
");

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
    $u = $conn->prepare("
      UPDATE Prisoner
      SET FullName=?, DateOfBirth=?, Gender=?, NationalID=?, AdmissionDate=?,
          Status=?, SecurityLevel=?, CellID=?
      WHERE PrisonerID=?
    ");
    $u->bind_param("sssssssii", $fullName, $dob, $gender, $nid, $admission, $status, $security, $cellID, $id);

    if ($u->execute()) {
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
  <title>PMS | Edit Prisoner</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Edit Prisoner</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="view.php?id=<?= $id ?>">Back</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Edit Prisoner</h1>
    <p class="small">Update basic information and accommodation.</p>

    <?php if($msg): ?>
      <div class="alert"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST">
      <h2>Basic Information</h2>

      <label>Full Name</label>
      <input name="FullName" value="<?= htmlspecialchars($p["FullName"]) ?>" required>

      <label>Date of Birth</label>
      <input type="date" name="DateOfBirth" value="<?= htmlspecialchars($p["DateOfBirth"]) ?>" required>

      <label>Gender</label>
      <select name="Gender" required>
        <option value="">-- Select --</option>
        <option <?= ($p["Gender"]==="Male") ? "selected" : "" ?>>Male</option>
        <option <?= ($p["Gender"]==="Female") ? "selected" : "" ?>>Female</option>
        <option <?= ($p["Gender"]==="Other") ? "selected" : "" ?>>Other</option>
      </select>

      <label>National ID</label>
      <input name="NationalID" value="<?= htmlspecialchars($p["NationalID"]) ?>" required>

      <label>Admission Date</label>
      <input type="date" name="AdmissionDate" value="<?= htmlspecialchars($p["AdmissionDate"]) ?>" required>

      <label>Status</label>
      <select name="Status" required>
        <option value="">-- Select --</option>
        <option <?= ($p["Status"]==="UnderTrial") ? "selected" : "" ?>>UnderTrial</option>
        <option <?= ($p["Status"]==="Convicted") ? "selected" : "" ?>>Convicted</option>
        <option <?= ($p["Status"]==="Released") ? "selected" : "" ?>>Released</option>
        <option <?= ($p["Status"]==="Parole") ? "selected" : "" ?>>Parole</option>
      </select>

      <label>Security Level</label>
      <select name="SecurityLevel" required>
        <option value="">-- Select --</option>
        <option <?= ($p["SecurityLevel"]==="High") ? "selected" : "" ?>>High</option>
        <option <?= ($p["SecurityLevel"]==="Medium") ? "selected" : "" ?>>Medium</option>
        <option <?= ($p["SecurityLevel"]==="Low") ? "selected" : "" ?>>Low</option>
      </select>

      <h2>Accommodation</h2>

      <label>Assign Cell (optional)</label>
      <select name="CellID">
        <option value="">-- Not Assigned --</option>
        <?php
          $selected = (int)($p["CellID"] ?? 0);
          while($c = $cells->fetch_assoc()):
            $cid = (int)$c["CellID"];
            $sel = ($cid === $selected) ? "selected" : "";
        ?>
          <option value="<?= $cid ?>" <?= $sel ?>>
            <?= htmlspecialchars($c["PrisonName"]." / ".$c["BlockName"]." / Cell ".$c["CellNumber"]) ?>
          </option>
        <?php endwhile; ?>
      </select>

      <button type="submit">Update Prisoner</button>
    </form>
  </div>

  <div class="card">
    <h2>Danger Zone</h2>
    <p class="small">Deleting a prisoner will remove the record permanently.</p>
    <div class="actions">
      <a class="btn btn-danger" href="delete.php?id=<?= $id ?>">Delete Prisoner</a>
    </div>
  </div>
</div>

</body>
</html>