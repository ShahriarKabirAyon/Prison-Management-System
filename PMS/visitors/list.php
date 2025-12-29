<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_login();

$res = $conn->query("SELECT * FROM Visitor ORDER BY VisitorID DESC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Visitors</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span><span>PMS</span><span class="badge">Visitors</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="../index.php">Home</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Visitors</h1>
    <p class="small">Visitor directory (family / lawyer / others).</p>

    <div class="actions">
      <a class="btn" href="add.php">+ Add Visitor</a>
    </div>

    <div class="table-wrap">
      <table class="table">
        <tr>
          <th>ID</th><th>Full Name</th><th>Relation</th><th>Phone</th><th>ID Doc</th>
        </tr>

        <?php if($res && $res->num_rows>0): ?>
          <?php while($v=$res->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$v["VisitorID"] ?></td>
              <td><?= htmlspecialchars($v["FullName"]) ?></td>
              <td class="muted"><?= htmlspecialchars($v["Phone"] ?? "") ?></td>
              <td class="muted"><?= htmlspecialchars($v["IDDocumentNo"] ?? "") ?></td>
              <td class="muted"><?= htmlspecialchars($v["Relation"]) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5"><div class="alert">No visitors found.</div></td></tr>
        <?php endif; ?>
      </table>
    </div>

  </div>
</div>

</body>
</html>