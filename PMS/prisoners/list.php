<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_login();

$result = $conn->query("
  SELECT pr.PrisonerID, pr.FullName, pr.Gender, pr.Status, pr.SecurityLevel,
         c.CellNumber,
         b.Name AS BlockName,
         pz.Name AS PrisonName
  FROM Prisoner pr
  LEFT JOIN Cell c ON c.CellID = pr.CellID
  LEFT JOIN Block b ON b.BlockID = c.BlockID
  LEFT JOIN Prison pz ON pz.PrisonID = b.PrisonID
  ORDER BY pr.PrisonerID DESC
");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Prisoners</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Prisoners</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="../index.php">Home</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">

  <div class="card">
    <h1>Prisoners</h1>
    <p class="small">List of all registered prisoners and their current accommodation.</p>

    <div class="actions">
      <a class="btn" href="add.php">+ Add Prisoner</a>
    </div>

    <div class="table-wrap">
      <table class="table">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Gender</th>
          <th>Status</th>
          <th>Security</th>
          <th>Prison</th>
          <th>Block</th>
          <th>Cell</th>
          <th>Action</th>
        </tr>

        <?php if($result && $result->num_rows > 0): ?>
          <?php while($r = $result->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$r["PrisonerID"] ?></td>
              <td><?= htmlspecialchars($r["FullName"]) ?></td>
              <td><?= htmlspecialchars($r["Gender"]) ?></td>
              <td><?= htmlspecialchars($r["Status"]) ?></td>
              <td><?= htmlspecialchars($r["SecurityLevel"]) ?></td>

              <td class="muted"><?= htmlspecialchars($r["PrisonName"] ?? "N/A") ?></td>
              <td class="muted"><?= htmlspecialchars($r["BlockName"] ?? "N/A") ?></td>
              <td class="muted"><?= htmlspecialchars($r["CellNumber"] ?? "N/A") ?></td>

              <td>
                <a class="btn btn-ghost" href="view.php?id=<?= (int)$r["PrisonerID"] ?>">View</a>
                <a class="btn btn-ghost" href="edit.php?id=<?= (int)$r["PrisonerID"] ?>">Edit</a>
                <a class="btn btn-danger" href="delete.php?id=<?= (int)$r["PrisonerID"] ?>">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="9">
              <div class="alert">No prisoners found.</div>
            </td>
          </tr>
        <?php endif; ?>
      </table>
    </div>

  </div>

</div>

</body>
</html>