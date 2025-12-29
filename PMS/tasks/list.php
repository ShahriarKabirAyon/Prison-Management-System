<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_login();

$res = $conn->query("
  SELECT pt.AssignmentID, pt.AssignedDate, pt.Status, pt.Note,
         pr.FullName AS PrisonerName,
         t.Title AS TaskTitle, t.Priority,
         u.Username AS AssignedBy
  FROM PrisonerTask pt
  JOIN Prisoner pr ON pr.PrisonerID = pt.PrisonerID
  JOIN Task t ON t.TaskID = pt.TaskID
  JOIN users u ON u.UserID = pt.AssignedByUserID
  ORDER BY pt.AssignmentID DESC
");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>PMS | Task Assignments</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="nav"><div class="row">
  <div class="brand"><span class="dot"></span><span>PMS</span><span class="badge">Task Assignments</span></div>
  <div class="actions">
    <a class="btn btn-ghost" href="../index.php">Home</a>
    <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
  </div>
</div></div>

<div class="container">
  <div class="card">
    <h1>Task Assignments</h1>
    <div class="actions">
      <a class="btn" href="add_task.php">+ Add Task</a>
      <a class="btn" href="assign.php">+ Assign Task</a>
    </div>

    <div class="table-wrap">
      <table class="table">
        <tr>
          <th>ID</th><th>Prisoner</th><th>Task</th><th>Priority</th>
          <th>Date</th><th>Status</th><th>Assigned By</th><th>Note</th>
        </tr>
        <?php if($res && $res->num_rows>0): while($r=$res->fetch_assoc()): ?>
          <tr>
            <td><?= (int)$r["AssignmentID"] ?></td>
            <td><?= htmlspecialchars($r["PrisonerName"]) ?></td>
            <td><?= htmlspecialchars($r["TaskTitle"]) ?></td>
            <td class="muted"><?= htmlspecialchars($r["Priority"]) ?></td>
            <td class="muted"><?= htmlspecialchars($r["AssignedDate"]) ?></td>
            <td class="muted"><?= htmlspecialchars($r["Status"]) ?></td>
            <td class="muted"><?= htmlspecialchars($r["AssignedBy"]) ?></td>
            <td class="muted"><?= htmlspecialchars($r["Note"] ?? "") ?></td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="8"><div class="alert">No task assignments found.</div></td></tr>
        <?php endif; ?>
      </table>
    </div>
  </div>
</div>
</body>
</html>