<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN"]);

$msg = "";
$blocks = $conn->query("
  SELECT b.BlockID, b.Name AS BlockName, p.Name AS PrisonName
  FROM Block b
  JOIN Prison p ON p.PrisonID = b.PrisonID
  ORDER BY p.Name, b.Name
");

if($_SERVER["REQUEST_METHOD"]==="POST"){
  $blockID = (int)($_POST["BlockID"] ?? 0);
  $cellNo  = trim($_POST["CellNumber"] ?? "");
  $cap     = (int)($_POST["Capacity"] ?? 0);
  $sec     = trim($_POST["SecurityLevel"] ?? "");
  $status  = trim($_POST["Status"] ?? "");

  if($blockID<=0 || $cellNo==="" || $cap<=0 || $sec==="" || $status===""){
    $msg="Please fill all required fields (capacity must be > 0).";
  } else {
    $st = $conn->prepare("INSERT INTO Cell (BlockID, CellNumber, Capacity, SecurityLevel, Status) VALUES (?,?,?,?,?)");
    $st->bind_param("isiss", $blockID, $cellNo, $cap, $sec, $status);

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
  <title>PMS | Add Cell</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Add Cell</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="list.php">Back</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Add Cell</h1>
    <p class="small">Create a cell under a block.</p>

    <?php if($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <form method="POST">
      <label>Block</label>
      <select name="BlockID" required>
        <option value="">-- Select --</option>
        <?php while($b=$blocks->fetch_assoc()): ?>
          <option value="<?= (int)$b["BlockID"] ?>">
            <?= htmlspecialchars($b["PrisonName"]." / ".$b["BlockName"]) ?>
          </option>
        <?php endwhile; ?>
      </select>

      <label>Cell Number</label>
      <input name="CellNumber" required>

      <label>Capacity</label>
      <input type="number" name="Capacity" min="1" required>

      <label>Security Level</label>
      <input name="SecurityLevel" placeholder="High / Medium / Low" required>

      <label>Status</label>
      <select name="Status" required>
        <option value="">-- Select --</option>
        <option>Occupied</option>
        <option>Empty</option>
        <option>UnderMaintenance</option>
      </select>

      <button type="submit">Save Cell</button>
    </form>
  </div>
</div>

</body>
</html>