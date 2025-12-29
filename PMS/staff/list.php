<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_login();

$res = $conn->query("SELECT StaffID, FullName, Phone, JoinDate, BaseSalary FROM Staff ORDER BY StaffID DESC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Staff</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Staff</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="../index.php">Home</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Staff List</h1>
    <p class="small">All staff members registered in the system.</p>

    <?php if(isset($_SESSION["Role"]) && $_SESSION["Role"] === "ADMIN"): ?>
      <div class="actions">
        <a class="btn" href="add.php">+ Add Staff</a>
      </div>
    <?php endif; ?>

    <div class="table-wrap">
      <table class="table">
        <tr>
          <th>ID</th>
          <th>Full Name</th>
          <th>Phone</th>
          <th>Join Date</th>
          <th>Base Salary</th>
          <th>Action</th>
        </tr>

        <?php if($res && $res->num_rows > 0): ?>
          <?php while($r = $res->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$r["StaffID"] ?></td>
              <td><?= htmlspecialchars($r["FullName"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["Phone"] ?? "") ?></td>
              <td class="muted"><?= htmlspecialchars($r["JoinDate"]) ?></td>
              <td class="muted"><?= htmlspecialchars($r["BaseSalary"]) ?></td>
              <td>
                <a class="btn btn-ghost" href="view.php?id=<?= (int)$r["StaffID"] ?>">View</a>
                <?php if(isset($_SESSION["Role"]) && $_SESSION["Role"] === "ADMIN"): ?>
                  <a class="btn btn-ghost" href="edit.php?id=<?= (int)$r["StaffID"] ?>">Edit</a>
                  <a class="btn btn-danger" href="delete.php?id=<?= (int)$r["StaffID"] ?>">Delete</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6"><div class="alert">No staff found.</div></td>
          </tr>
        <?php endif; ?>
      </table>
    </div>

  </div>
</div>

</body>
</html>