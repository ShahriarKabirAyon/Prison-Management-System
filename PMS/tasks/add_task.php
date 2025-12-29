<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_role(["ADMIN","WARDEN","GUARD"]);

$msg="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
  $title = trim($_POST["Title"] ?? "");
  $desc  = trim($_POST["Description"] ?? "");
  $prio  = $_POST["Priority"] ?? "Medium";

  if($title==="") $msg="Title is required.";
  else{
    $st=$conn->prepare("INSERT INTO Task (Title, Description, Priority) VALUES (?,?,?)");
    $st->bind_param("sss",$title,$desc,$prio);
    if($st->execute()){ header("Location: list.php"); exit; }
    else $msg="Error: ".$conn->error;
  }
}
?>
<!doctype html><html><head>
<meta charset="utf-8"><title>PMS | Add Task</title>
<link rel="stylesheet" href="../assets/style.css"></head><body>
<div class="nav"><div class="row">
  <div class="brand"><span class="dot"></span><span>PMS</span><span class="badge">Add Task</span></div>
  <div class="actions"><a class="btn btn-ghost" href="list.php">Back</a><a class="btn btn-ghost" href="../auth/logout.php">Logout</a></div>
</div></div>

<div class="container"><div class="card">
  <h1>Add Task</h1>
  <?php if($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

  <form method="POST">
    <label>Title</label>
    <input name="Title" required>

    <label>Description</label>
    <input name="Description">

    <label>Priority</label>
    <select name="Priority" required>
      <option>Low</option><option selected>Medium</option><option>High</option>
    </select>

    <button type="submit">Save Task</button>
  </form>
</div></div>
</body></html>