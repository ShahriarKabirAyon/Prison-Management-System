<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN","WARDEN","GUARD"]);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$msg = "";

function has_col(mysqli $conn, string $table, string $col): bool {
  $st = $conn->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
  $st->bind_param("s", $col);
  $st->execute();
  $r = $st->get_result();
  return ($r && $r->num_rows > 0);
}

$visitors  = $conn->query("SELECT VisitorID, FullName FROM Visitor ORDER BY FullName");
$prisoners = $conn->query("SELECT PrisonerID, FullName FROM Prisoner ORDER BY FullName");

if($_SERVER["REQUEST_METHOD"]==="POST"){
  try {
    $visitorID  = (int)($_POST["VisitorID"] ?? 0);
    $prisonerID = (int)($_POST["PrisonerID"] ?? 0);
    $dt         = $_POST["VisitDateTime"] ?? "";
    $dur        = (int)($_POST["DurationMinutes"] ?? 0);
    $purpose    = trim($_POST["Purpose"] ?? "");
    $approvedBy = (int)($_SESSION["UserID"] ?? 0);

    // datetime-local -> MySQL DATETIME
    $dt = str_replace("T", " ", $dt);
    if ($dt !== "" && strlen($dt) === 16) $dt .= ":00";

    if($visitorID<=0 || $prisonerID<=0 || $dt==="" || $dur<=0 || $purpose==="" || $approvedBy<=0){
      $msg="Please fill all required fields.";
    } else {

      // detect approved-by column in Visit
      $byCol = has_col($conn, "Visit", "ApprovedBy") ? "ApprovedBy"
            : (has_col($conn, "Visit", "ApprovedByUserID") ? "ApprovedByUserID" : "");

      if ($byCol === "") {
        throw new Exception("Visit table has no ApprovedBy/ApprovedByUserID column. Check DESCRIBE Visit.");
      }

      $sql = "INSERT INTO Visit (VisitorID, PrisonerID, VisitDateTime, DurationMinutes, Purpose, `$byCol`)
              VALUES (?,?,?,?,?,?)";
      $st = $conn->prepare($sql);
      $st->bind_param("iisisi", $visitorID, $prisonerID, $dt, $dur, $purpose, $approvedBy);
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
  <title>PMS | Add Visit</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="nav"><div class="row">
  <div class="brand"><span class="dot"></span><span>PMS</span><span class="badge">Add Visit</span></div>
  <div class="actions"><a class="btn btn-ghost" href="list.php">Back</a><a class="btn btn-ghost" href="../auth/logout.php">Logout</a></div>
</div></div>

<div class="container"><div class="card">
  <h1>Add Visit</h1>
  <p class="small">Create a visit record (Visitor → Prisoner).</p>

  <?php if($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

  <form method="POST">
    <label>Visitor</label>
    <select name="VisitorID" required>
      <option value="">-- Select --</option>
      <?php while($v=$visitors->fetch_assoc()): ?>
        <option value="<?= (int)$v["VisitorID"] ?>"><?= htmlspecialchars($v["FullName"]) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Prisoner</label>
    <select name="PrisonerID" required>
      <option value="">-- Select --</option>
      <?php while($p=$prisoners->fetch_assoc()): ?>
        <option value="<?= (int)$p["PrisonerID"] ?>"><?= htmlspecialchars($p["FullName"]) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Visit Date & Time</label>
    <input type="datetime-local" name="VisitDateTime" required>

    <label>Duration (minutes)</label>
    <input type="number" name="DurationMinutes" min="1" required>

    <label>Purpose</label>
    <input name="Purpose" required>

    <button type="submit">Save Visit</button>
  </form>
</div></div>
</body>
</html>