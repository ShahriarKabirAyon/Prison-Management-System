<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN","WARDEN"]);

$msg = "";

if($_SERVER["REQUEST_METHOD"]==="POST"){
  $name = trim($_POST["FullName"] ?? "");
  $rel  = trim($_POST["RelationToPrisoner"] ?? "");
  $phone= trim($_POST["Phone"] ?? "");
  $addr = trim($_POST["Address"] ?? "");
  $doc  = trim($_POST["IDDocumentNo"] ?? "");

  if($name==="" || $rel===""){
    $msg="Full Name and Relation are required.";
  } else {
    $st = $conn->prepare(
  "INSERT INTO Visitor (FullName, Phone, Address, IDDocumentNo, Relation)
   VALUES (?,?,?,?,?)");
    $st->bind_param("sssss", $name, $phone, $addr, $doc, $rel);

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
  <title>PMS | Add Visitor</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand"><span class="dot"></span><span>PMS</span><span class="badge">Add Visitor</span></div>
    <div class="actions">
      <a class="btn btn-ghost" href="list.php">Back</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <h1>Add Visitor</h1>
    <p class="small">Register a visitor (family / lawyer / others).</p>

    <?php if($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <form method="POST">
      <label>Full Name</label>
      <input name="FullName" required>

      <label>Relation To Prisoner</label>
      <input name="RelationToPrisoner" placeholder="Father / Mother / Lawyer" required>

      <label>Phone</label>
      <input name="Phone">

      <label>Address</label>
      <input name="Address">

      <label>ID Document No</label>
      <input name="IDDocumentNo">

      <button type="submit">Save Visitor</button>
    </form>
  </div>
</div>

</body>
</html>