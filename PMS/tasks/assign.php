<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN","WARDEN","GUARD"]);

// show real mysqli errors
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$msg = "";

/* helper: check column exists */
function has_col(mysqli $conn, string $table, string $col): bool {
  $st = $conn->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
  $st->bind_param("s", $col);
  $st->execute();
  $r = $st->get_result();
  return ($r && $r->num_rows > 0);
}

$prisoners = $conn->query("SELECT PrisonerID, FullName FROM Prisoner ORDER BY FullName");
$tasks     = $conn->query("SELECT TaskID, Title, Priority FROM Task ORDER BY TaskID DESC");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    $prisonerID = (int)($_POST["PrisonerID"] ?? 0);
    $taskID     = (int)($_POST["TaskID"] ?? 0);
    $date       = $_POST["AssignedDate"] ?? "";
    $status     = $_POST["Status"] ?? "Pending";
    $note       = trim($_POST["Note"] ?? "");
    $assignedBy = (int)($_SESSION["UserID"] ?? 0);

    if ($prisonerID <= 0 || $taskID <= 0 || $date === "" || $assignedBy <= 0) {
      $msg = "Please select prisoner, task and date.";
    } else {
      // Verify FK targets exist (prevents silent FK confusion)
      $chk = $conn->prepare("SELECT 1 FROM Prisoner WHERE PrisonerID=?");
      $chk->bind_param("i", $prisonerID);
      $chk->execute();
      if ($chk->get_result()->num_rows === 0) throw new Exception("Prisoner not found in database.");

      $chk = $conn->prepare("SELECT 1 FROM Task WHERE TaskID=?");
      $chk->bind_param("i", $taskID);
      $chk->execute();
      if ($chk->get_result()->num_rows === 0) throw new Exception("Task not found in database.");

      $chk = $conn->prepare("SELECT 1 FROM users WHERE UserID=?");
      $chk->bind_param("i", $assignedBy);
      $chk->execute();
      if ($chk->get_result()->num_rows === 0) throw new Exception("Logged-in user not found in users table.");

      // Detect correct column name for "assigned by"
      $byCol = has_col($conn, "PrisonerTask", "AssignedByUserID") ? "AssignedByUserID"
            : (has_col($conn, "PrisonerTask", "AssignedBy") ? "AssignedBy" : "");

      if ($byCol === "") {
        throw new Exception("PrisonerTask table has no AssignedByUserID/AssignedBy column. Check DESCRIBE PrisonerTask.");
      }

      $sql = "INSERT INTO PrisonerTask (PrisonerID, TaskID, `$byCol`, AssignedDate, Status, Note)
              VALUES (?,?,?,?,?,?)";
      $st = $conn->prepare($sql);
      $st->bind_param("iiisss", $prisonerID, $taskID, $assignedBy, $date, $status, $note);
      $st->execute();

      // success confirmation (no doubt)
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
  <title>PMS | Assign Task</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="nav"><div class="row">
  <div class="brand"><span class="dot"></span><span>PMS</span><span class="badge">Assign Task</span></div>
  <div class="actions"><a class="btn btn-ghost" href="list.php">Back</a><a class="btn btn-ghost" href="../auth/logout.php">Logout</a></div>
</div></div>

<div class="container"><div class="card">
  <h1>Assign Task</h1>
  <p class="small">Assign an existing task to a prisoner.</p>

  <?php if($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

  <form method="POST">
    <label>Prisoner</label>
    <select name="PrisonerID" required>
      <option value="">-- Select --</option>
      <?php while($p = $prisoners->fetch_assoc()): ?>
        <option value="<?= (int)$p["PrisonerID"] ?>"><?= htmlspecialchars($p["FullName"]) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Task</label>
    <select name="TaskID" required>
      <option value="">-- Select --</option>
      <?php while($t = $tasks->fetch_assoc()): ?>
        <option value="<?= (int)$t["TaskID"] ?>"><?= htmlspecialchars($t["Title"]." (".$t["Priority"].")") ?></option>
      <?php endwhile; ?>
    </select>

    <label>Assigned Date</label>
    <input type="date" name="AssignedDate" required>

    <label>Status</label>
    <select name="Status" required>
      <option>Pending</option>
      <option>InProgress</option>
      <option>Completed</option>
    </select>

    <label>Note (optional)</label>
    <input name="Note">

    <button type="submit">Assign</button>
  </form>
</div></div>
</body>
</html>