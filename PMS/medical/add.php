<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN","MEDICAL"]);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$msg = "";

function has_col(mysqli $conn, string $table, string $col): bool {
  $st = $conn->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
  $st->bind_param("s", $col);
  $st->execute();
  $r = $st->get_result();
  return ($r && $r->num_rows > 0);
}

$prisoners = $conn->query("SELECT PrisonerID, FullName FROM Prisoner ORDER BY FullName");

if($_SERVER["REQUEST_METHOD"]==="POST"){
  try {
    $prisonerID = (int)($_POST["PrisonerID"] ?? 0);
    $date       = $_POST["RecordDate"] ?? "";
    $diag       = trim($_POST["Diagnosis"] ?? "");
    $treat      = trim($_POST["Treatment"] ?? "");
    $notes      = trim($_POST["Notes"] ?? "");
    $by         = (int)($_SESSION["UserID"] ?? 0);

    if($prisonerID<=0 || $date==="" || $diag==="" || $by<=0){
      $msg="Please fill all required fields.";
    } else {

      // detect recorded-by column in MedicalRecord
      $byCol = has_col($conn, "MedicalRecord", "RecordedByUserID") ? "RecordedByUserID"
            : (has_col($conn, "MedicalRecord", "RecordedBy") ? "RecordedBy" : "");

      if ($byCol === "") {
        throw new Exception("MedicalRecord table has no RecordedByUserID/RecordedBy column. Check DESCRIBE MedicalRecord.");
      }

      $sql = "INSERT INTO MedicalRecord (PrisonerID, `$byCol`, RecordDate, Diagnosis, Treatment, Notes)
              VALUES (?,?,?,?,?,?)";
      $st = $conn->prepare($sql);
      $st->bind_param("iissss", $prisonerID, $by, $date, $diag, $treat, $notes);
      $st->execute();

      header("Location: list.php?ok=1");
      exit;
    }
  } catch (Throwable $e) {
    $msg = "Error: " . $e->getMessage();
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Add Medical Record</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="nav"><div class="row">
  <div class="brand"><span class="dot"></span><span>PMS</span><span class="badge">Add Medical</span></div>
  <div class="actions"><a class="btn btn-ghost" href="list.php">Back</a><a class="btn btn-ghost" href="../auth/logout.php">Logout</a></div>
</div></div>

<div class="container"><div class="card">
  <h1>Add Medical Record</h1>
  <p class="small">Record diagnosis and treatment for a prisoner.</p>

  <div class="alert">
  DB: <?= htmlspecialchars($conn->query("SELECT DATABASE() db")->fetch_assoc()["db"]) ?>
  | UserID: <?= (int)($_SESSION["UserID"] ?? 0) ?>
  | Role: <?= htmlspecialchars($_SESSION["Role"] ?? "") ?>
  | Method: <?= htmlspecialchars($_SERVER["REQUEST_METHOD"]) ?>
</div>


  <?php if($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

  <form method="POST">
    <label>Prisoner</label>
    <select name="PrisonerID" required>
      <option value="">-- Select --</option>
      <?php while($p=$prisoners->fetch_assoc()): ?>
        <option value="<?= (int)$p["PrisonerID"] ?>"><?= htmlspecialchars($p["FullName"]) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Record Date</label>
    <input type="date" name="RecordDate" required>

    <label>Diagnosis</label>
    <input name="Diagnosis" required>

    <label>Treatment (optional)</label>
    <input name="Treatment">

    <label>Notes (optional)</label>
    <input name="Notes">

    <button type="submit">Save Medical Record</button>
  </form>
</div></div>
</body>
</html>