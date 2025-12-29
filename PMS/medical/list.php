<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_login();

$res = $conn->query("
  SELECT m.RecordID, m.RecordDate, m.Diagnosis, m.Treatment, m.Notes,
         p.FullName AS PrisonerName,
         u.Username AS RecordedBy
  FROM MedicalRecord m
  JOIN Prisoner p ON p.PrisonerID = m.PrisonerID
  JOIN users u ON u.UserID = m.RecordedByUserID
  ORDER BY m.RecordID DESC
");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Medical History</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand"><span class="dot"></span><span>PMS</span><span class="badge">Medical History</span></div>
    <div class="actions">
      <a class="btn btn-ghost" href="../index.php">Home</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Medical Records</h1>
    <p class="small">Prisoner medical history (diagnosis, treatment, notes).</p>

    <div class="actions">
      <a class="btn" href="add.php">+ Add Medical Record</a>
    </div>

    <div class="table-wrap">
      <table class="table">
        <tr>
          <th>ID</th><th>Prisoner</th><th>Date</th><th>Diagnosis</th><th>Treatment</th><th>Recorded By</th><th>Notes</th>
        </tr>

        <?php if($res && $res->num_rows>0): ?>
          <?php while($r=$res->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$r["RecordID"] ?></td>
              <td><?= htmlspecialchars($r["PrisonerName"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["RecordDate"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["Diagnosis"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["Treatment"] ?? "") ?></td>
              <td class="muted"><?= htmlspecialchars($r["RecordedBy"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["Notes"] ?? "") ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7"><div class="alert">No medical records found.</div></td></tr>
        <?php endif; ?>
      </table>
    </div>

  </div>
</div>

</body>
</html>