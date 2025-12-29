<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN"]);

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $fullName = trim($_POST["FullName"] ?? "");
  $username = trim($_POST["Username"] ?? "");
  $password = $_POST["Password"] ?? "";
  $role     = $_POST["Role"] ?? "";

  if ($fullName === "" || $username === "" || $password === "" || $role === "") {
    $msg = "Please fill all required fields.";
  } else {
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (FullName, Username, PasswordHash, Role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullName, $username, $hash, $role);

    if ($stmt->execute()) {
      header("Location: list.php");
      exit;
    } else {
      $msg = "Error: " . $st->error;
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Add User</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Add User</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="list.php">Back</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Create New User</h1>
    <p class="small">Create a login user account with role-based access.</p>

    <?php if($msg): ?>
      <div class="alert"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Full Name</label>
      <input name="FullName" required>

      <label>Username</label>
      <input name="Username" required>

      <label>Password</label>
      <input type="password" name="Password" required>

      <label>Role</label>
      <select name="Role" required>
        <option value="">-- Select --</option>
        <option>ADMIN</option>
        <option>WARDEN</option>
        <option>GUARD</option>
        <option>MEDICAL</option>
      </select>

      <button type="submit">Create User</button>
    </form>
  </div>
</div>

</body>
</html>