<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_login();

$res = $conn->query("
  SELECT vi.VisitID, vi.VisitDateTime, vi.DurationMinutes, vi.Purpose,
         v.FullName AS VisitorName,
         v.Relation AS RelationToPrisoner,
         p.FullName AS PrisonerName,
         u.Username AS ApprovedBy
  FROM Visit vi
  JOIN Visitor v ON v.VisitorID = vi.VisitorID
  JOIN Prisoner p ON p.PrisonerID = vi.PrisonerID
  JOIN users u ON u.UserID = vi.ApprovedBy
  ORDER BY vi.VisitID DESC
");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Visits</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand"><span class="dot"></span><span>PMS</span><span class="badge">Visits</span></div>
    <div class="actions">
      <a class="btn btn-ghost" href="../index.php">Home</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Visits</h1>
    <p class="small">All visit records (Visitor → Prisoner).</p>

    <div class="actions">
      <a class="btn" href="add.php">+ Add Visit</a>
    </div>

    <div class="table-wrap">
      <table class="table">
        <tr>
          <th>ID</th>
          <th>Visitor</th>
          <th>Relation</th>
          <th>Prisoner</th>
          <th>DateTime</th>
          <th>Duration</th>
          <th>Purpose</th>
          <th>Approved By</th>
        </tr>

        <?php if($res && $res->num_rows>0): ?>
          <?php while($r=$res->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$r["VisitID"] ?></td>
              <td><?= htmlspecialchars($r["VisitorName"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["RelationToPrisoner"] ?? "") ?></td>
              <td><?= htmlspecialchars($r["PrisonerName"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["VisitDateTime"]) ?></td>
              <td class="muted"><?= (int)$r["DurationMinutes"] ?> min</td>
              <td class="muted"><?= htmlspecialchars($r["Purpose"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["ApprovedBy"]) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8"><div class="alert">No visits found.</div></td></tr>
        <?php endif; ?>
      </table>
    </div>

  </div>
</div>

</body>
</html>