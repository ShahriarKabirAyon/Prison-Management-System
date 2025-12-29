<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_login();

$res = $conn->query("
  SELECT c.CellID, c.CellNumber, c.Capacity, c.SecurityLevel, c.Status,
         b.Name AS BlockName, p.Name AS PrisonName
  FROM Cell c
  JOIN Block b ON b.BlockID = c.BlockID
  JOIN Prison p ON p.PrisonID = b.PrisonID
  ORDER BY c.CellID DESC
");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Cells</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Cells</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="../index.php">Home</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Cell List</h1>
    <p class="small">Cells belong to a block (Prison → Block → Cell).</p>

    <?php if(isset($_SESSION["Role"]) && $_SESSION["Role"] === "ADMIN"): ?>
      <div class="actions">
        <a class="btn" href="add.php">+ Add Cell</a>
      </div>
    <?php endif; ?>

    <div class="table-wrap">
      <table class="table">
        <tr>
          <th>ID</th>
          <th>Cell No</th>
          <th>Capacity</th>
          <th>Security</th>
          <th>Status</th>
          <th>Block</th>
          <th>Prison</th>
        </tr>

        <?php if($res && $res->num_rows>0): ?>
          <?php while($r=$res->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$r["CellID"] ?></td>
              <td><?= htmlspecialchars($r["CellNumber"]) ?></td>
              <td class="muted"><?= (int)$r["Capacity"] ?></td>
              <td class="muted"><?= htmlspecialchars($r["SecurityLevel"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["Status"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["BlockName"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["PrisonName"]) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7"><div class="alert">No cells found.</div></td></tr>
        <?php endif; ?>
      </table>
    </div>

  </div>
</div>

</body>
</html>