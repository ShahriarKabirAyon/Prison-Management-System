<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_login();

$res = $conn->query("SELECT PrisonID, Name, Location, TotalCapacity FROM Prison ORDER BY PrisonID DESC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Prisons</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Prisons</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="../index.php">Home</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Prison List</h1>
    <p class="small">All prisons registered in the system.</p>

    <?php if(isset($_SESSION["Role"]) && $_SESSION["Role"] === "ADMIN"): ?>
      <div class="actions">
        <a class="btn" href="add.php">+ Add Prison</a>
      </div>
    <?php endif; ?>

    <div class="table-wrap">
      <table class="table">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Location</th>
          <th>Total Capacity</th>
        </tr>

        <?php if($res && $res->num_rows>0): ?>
          <?php while($r=$res->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$r["PrisonID"] ?></td>
              <td><?= htmlspecialchars($r["Name"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["Location"]) ?></td>
              <td class="muted"><?= (int)$r["TotalCapacity"] ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="4"><div class="alert">No prisons found.</div></td></tr>
        <?php endif; ?>
      </table>
    </div>

  </div>
</div>

</body>
</html>