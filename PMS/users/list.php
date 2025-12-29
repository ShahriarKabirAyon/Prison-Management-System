<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN"]);

$res = $conn->query("SELECT UserID, FullName, Username, Role FROM users ORDER BY UserID DESC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Users</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Users</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="../index.php">Home</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>User Management</h1>
    <p class="small">Only ADMIN can create and view users.</p>

    <div class="actions">
      <a class="btn" href="add.php">+ Add User</a>
    </div>

    <div class="table-wrap">
      <table class="table">
        <tr>
          <th>ID</th>
          <th>Full Name</th>
          <th>Username</th>
          <th>Role</th>
        </tr>

        <?php if($res && $res->num_rows>0): ?>
          <?php while($u=$res->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$u["UserID"] ?></td>
              <td><?= htmlspecialchars($u["FullName"]) ?></td>
              <td class="muted"><?= htmlspecialchars($u["Username"]) ?></td>
              <td class="muted"><?= htmlspecialchars($u["Role"]) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="4"><div class="alert">No users found.</div></td></tr>
        <?php endif; ?>
      </table>
    </div>
  </div>
</div>

</body>
</html>