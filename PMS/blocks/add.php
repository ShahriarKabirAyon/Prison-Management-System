<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN"]);

$msg = "";
$prisons = $conn->query("SELECT PrisonID, Name FROM Prison ORDER BY Name");

if($_SERVER["REQUEST_METHOD"]==="POST"){
  $prisonID = (int)($_POST["PrisonID"] ?? 0);
  $name = trim($_POST["Name"] ?? "");
  $type = trim($_POST["Type"] ?? "");

  if($prisonID<=0 || $name==="" || $type===""){
    $msg="Please fill all required fields.";
  } else {
    $st = $conn->prepare("INSERT INTO Block (PrisonID, Name, Type) VALUES (?,?,?)");
    $st->bind_param("iss",$prisonID,$name,$type);

    if($st->execute()){
      header("Location: list.php"); exit;
    } else {
      $msg="Error: ".$conn->error;
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Add Block</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Add Block</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="list.php">Back</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Add Block</h1>
    <p class="small">Create a block under a prison.</p>

    <?php if($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <form method="POST">
      <label>Prison</label>
      <select name="PrisonID" required>
        <option value="">-- Select --</option>
        <?php while($p=$prisons->fetch_assoc()): ?>
          <option value="<?= (int)$p["PrisonID"] ?>"><?= htmlspecialchars($p["Name"]) ?></option>
        <?php endwhile; ?>
      </select>

      <label>Block Name</label>
      <input name="Name" required>

      <label>Type</label>
      <input name="Type" placeholder="High Security / General / Female" required>

      <button type="submit">Save Block</button>
    </form>
  </div>
</div>

</body>
</html>