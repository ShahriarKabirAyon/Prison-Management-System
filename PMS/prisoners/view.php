<?php
require_once(__DIR__ . "/../config/db.php");
require_once(__DIR__ . "/../includes/auth.php");
require_login();

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("Invalid ID");

$stmt = $conn->prepare("
  SELECT pr.*,
         c.CellNumber,
         b.Name AS BlockName,
         pz.Name AS PrisonName
  FROM Prisoner pr
  LEFT JOIN Cell c ON c.CellID = pr.CellID
  LEFT JOIN Block b ON b.BlockID = c.BlockID
  LEFT JOIN Prison pz ON pz.PrisonID = b.PrisonID
  WHERE pr.PrisonerID=?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
if (!$p) die("Prisoner not found");

$ut = $cv = null;

$q = $conn->prepare("SELECT * FROM UnderTrialPrisoner WHERE PrisonerID=?");
$q->bind_param("i", $id);
$q->execute();
$ut = $q->get_result()->fetch_assoc();

$q = $conn->prepare("SELECT * FROM ConvictedPrisoner WHERE PrisonerID=?");
$q->bind_param("i", $id);
$q->execute();
$cv = $q->get_result()->fetch_assoc();

$tasksStmt = $conn->prepare("
  SELECT pt.AssignmentID, pt.AssignedDate, pt.Status, pt.Note,
         t.Title, t.Priority,
         u.Username AS AssignedBy
  FROM PrisonerTask pt
  JOIN Task t ON t.TaskID = pt.TaskID
  JOIN users u ON u.UserID = pt.AssignedByUserID
  WHERE pt.PrisonerID=?
  ORDER BY pt.AssignmentID DESC
");
$tasksStmt->bind_param("i", $id);
$tasksStmt->execute();
$tasksRes = $tasksStmt->get_result();

$visitsStmt = $conn->prepare("
  SELECT vi.VisitID, vi.VisitDateTime, vi.DurationMinutes, vi.Purpose,
         v.FullName AS VisitorName,
         v.Relation AS RelationToPrisoner,
         u.Username AS ApprovedBy
  FROM Visit vi
  JOIN Visitor v ON v.VisitorID = vi.VisitorID
  JOIN users u ON u.UserID = vi.ApprovedBy
  WHERE vi.PrisonerID=?
  ORDER BY vi.VisitID DESC
");
$visitsStmt->bind_param("i", $id);
$visitsStmt->execute();
$visitsRes = $visitsStmt->get_result();

$medStmt = $conn->prepare("
  SELECT m.RecordID, m.RecordDate, m.Diagnosis, m.Treatment, m.Notes,
         u.Username AS RecordedBy
  FROM MedicalRecord m
  JOIN users u ON u.UserID = m.RecordedByUserID
  WHERE m.PrisonerID=?
  ORDER BY m.RecordID DESC
");
$medStmt->bind_param("i", $id);
$medStmt->execute();
$medRes = $medStmt->get_result();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PMS | Prisoner Details</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="nav">
  <div class="row">
    <div class="brand">
      <span class="dot"></span>
      <span>PMS</span>
      <span class="badge">Prisoner Details</span>
    </div>
    <div class="actions">
      <a class="btn btn-ghost" href="list.php">Back</a>
      <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="container">

  <div class="card">
    <h1><?= htmlspecialchars($p["FullName"]) ?></h1>
    <p class="small">Prisoner ID: <?= (int)$p["PrisonerID"] ?></p>

    <hr>

    <p><b>National ID:</b> <?= htmlspecialchars($p["NationalID"]) ?></p>
    <p><b>Date of Birth:</b> <?= htmlspecialchars($p["DateOfBirth"]) ?></p>
    <p><b>Gender:</b> <?= htmlspecialchars($p["Gender"]) ?></p>
    <p><b>Admission Date:</b> <?= htmlspecialchars($p["AdmissionDate"]) ?></p>
    <p><b>Status:</b> <?= htmlspecialchars($p["Status"]) ?></p>
    <p><b>Security Level:</b> <?= htmlspecialchars($p["SecurityLevel"]) ?></p>
  </div>

  <div class="card">
    <h2>Accommodation</h2>
    <p><b>Prison:</b> <?= htmlspecialchars($p["PrisonName"] ?? "N/A") ?></p>
    <p><b>Block:</b> <?= htmlspecialchars($p["BlockName"] ?? "N/A") ?></p>
    <p><b>Cell:</b> <?= htmlspecialchars($p["CellNumber"] ?? "N/A") ?></p>
  </div>

  <div class="card">
    <h2>Case Status</h2>
    <?php if($ut): ?>
      <p><b>Type:</b> Under Trial</p>
      <p><b>Court:</b> <?= htmlspecialchars($ut["CourtName"]) ?></p>
      <p><b>Next Hearing:</b> <?= htmlspecialchars($ut["NextHearingDate"]) ?></p>
    <?php elseif($cv): ?>
      <p><b>Type:</b> Convicted</p>
      <p><b>Sentence Start:</b> <?= htmlspecialchars($cv["SentenceStartDate"]) ?></p>
      <p><b>Sentence End:</b> <?= htmlspecialchars($cv["SentenceEndDate"]) ?></p>
      <p><b>Parole Eligibility:</b> <?= htmlspecialchars($cv["ParoleEligibilityDate"]) ?></p>
    <?php else: ?>
      <div class="alert">No subtype record found.</div>
    <?php endif; ?>
  </div>

  <div class="card">
    <h2>Assigned Tasks</h2>
    <div class="actions">
      <a class="btn" href="../tasks/assign.php">+ Assign Task</a>
    </div>

    <div class="table-wrap">
      <table class="table">
        <tr>
          <th>ID</th><th>Task</th><th>Priority</th><th>Date</th><th>Status</th><th>Assigned By</th><th>Note</th>
        </tr>
        <?php if($tasksRes && $tasksRes->num_rows>0): ?>
          <?php while($t=$tasksRes->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$t["AssignmentID"] ?></td>
              <td><?= htmlspecialchars($t["Title"]) ?></td>
              <td class="muted"><?= htmlspecialchars($t["Priority"]) ?></td>
              <td class="muted"><?= htmlspecialchars($t["AssignedDate"]) ?></td>
              <td class="muted"><?= htmlspecialchars($t["Status"]) ?></td>
              <td class="muted"><?= htmlspecialchars($t["AssignedBy"]) ?></td>
              <td class="muted"><?= htmlspecialchars($t["Note"] ?? "") ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7"><div class="alert">No tasks assigned to this prisoner.</div></td></tr>
        <?php endif; ?>
      </table>
    </div>
  </div>

  <div class="card">
    <h2>Visits</h2>
    <div class="actions">
      <a class="btn" href="../visits/add.php">+ Add Visit</a>
      <a class="btn btn-ghost" href="../visitors/list.php">Visitors</a>
    </div>

    <div class="table-wrap">
      <table class="table">
        <tr>
          <th>ID</th><th>Visitor</th><th>Relation</th><th>DateTime</th><th>Duration</th><th>Purpose</th><th>Approved By</th>
        </tr>
        <?php if($visitsRes && $visitsRes->num_rows>0): ?>
          <?php while($v=$visitsRes->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$v["VisitID"] ?></td>
              <td><?= htmlspecialchars($v["VisitorName"]) ?></td>
              <td class="muted"><?= htmlspecialchars($v["RelationToPrisoner"]) ?></td>
              <td class="muted"><?= htmlspecialchars($v["VisitDateTime"]) ?></td>
              <td class="muted"><?= (int)$v["DurationMinutes"] ?> min</td>
              <td class="muted"><?= htmlspecialchars($v["Purpose"]) ?></td>
              <td class="muted"><?= htmlspecialchars($v["ApprovedBy"]) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7"><div class="alert">No visits found for this prisoner.</div></td></tr>
        <?php endif; ?>
      </table>
    </div>
  </div>

  <div class="card">
    <h2>Medical History</h2>
    <div class="actions">
      <a class="btn" href="../medical/add.php">+ Add Medical Record</a>
    </div>

    <div class="table-wrap">
      <table class="table">
        <tr>
          <th>ID</th><th>Date</th><th>Diagnosis</th><th>Treatment</th><th>Recorded By</th><th>Notes</th>
        </tr>
        <?php if($medRes && $medRes->num_rows>0): ?>
          <?php while($m=$medRes->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$m["RecordID"] ?></td>
              <td class="muted"><?= htmlspecialchars($m["RecordDate"]) ?></td>
              <td class="muted"><?= htmlspecialchars($m["Diagnosis"]) ?></td>
              <td class="muted"><?= htmlspecialchars($m["Treatment"] ?? "") ?></td>
              <td class="muted"><?= htmlspecialchars($m["RecordedBy"]) ?></td>
              <td class="muted"><?= htmlspecialchars($m["Notes"] ?? "") ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6"><div class="alert">No medical records found for this prisoner.</div></td></tr>
        <?php endif; ?>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="actions">
      <a class="btn" href="edit.php?id=<?= (int)$p["PrisonerID"] ?>">Edit Prisoner</a>
      <a class="btn btn-danger" href="delete.php?id=<?= (int)$p["PrisonerID"] ?>">Delete Prisoner</a>
    </div>
  </div>

</div>

</body>
</html>