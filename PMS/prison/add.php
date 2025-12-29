<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN"]);

$msg = "";

if($_SERVER["REQUEST_METHOD"]==="POST"){
  $name = trim($_POST["Name"] ?? "");
  $loc  = trim($_POST["Location"] ?? "");
  $cap  = (int)($_POST["TotalCapacity"] ?? 0);

  if($name==="" || $loc==="" || $cap<=0){
    $msg = "Please fill all required fields (capacity must be > 0).";
  } else {
    $st = $conn->prepare("INSERT INTO Prison (Name, Location, TotalCapacity) VALUES (?,?,?)");
    $st->bind_param("ssi", $name, $loc, $cap);

    if($st->execute()){
      header("Location: list.php"); exit;
    } else {
      $msg = "Error: ".$conn->error;
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Add Prison</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Add Prison</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="list.php">Back</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Add Prison</h1>
    <p class="small">Create a new prison entry.</p>

    <?php if($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <form method="POST">
      <label>Prison Name</label>
      <input name="Name" required>

      <label>Location</label>
      <input name="Location" required>

      <label>Total Capacity</label>
      <input type="number" name="TotalCapacity" min="1" required>

      <button type="submit">Save Prison</button>
    </form>
  </div>
</div>

</body>
</html>