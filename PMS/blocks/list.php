<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_login();

$res = $conn->query("
  SELECT b.BlockID, b.Name, b.Type, p.Name AS PrisonName
  FROM Block b
  JOIN Prison p ON p.PrisonID = b.PrisonID
  ORDER BY b.BlockID DESC
");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Blocks</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Blocks</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="../index.php">Home</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Block List</h1>
    <p class="small">Blocks belong to a prison (Prison → Blocks).</p>

    <?php if(isset($_SESSION["Role"]) && $_SESSION["Role"] === "ADMIN"): ?>
      <div class="actions">
        <a class="btn" href="add.php">+ Add Block</a>
      </div>
    <?php endif; ?>

    <div class="table-wrap">
      <table class="table">
        <tr>
          <th>ID</th>
          <th>Block Name</th>
          <th>Type</th>
          <th>Prison</th>
        </tr>

        <?php if($res && $res->num_rows>0): ?>
          <?php while($r=$res->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$r["BlockID"] ?></td>
              <td><?= htmlspecialchars($r["Name"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["Type"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["PrisonName"]) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="4"><div class="alert">No blocks found.</div></td></tr>
        <?php endif; ?>
      </table>
    </div>

  </div>
</div>

</body>
</html>