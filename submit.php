<?php
// Handles the form submission and returns a formatted HTML card.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo '<div class="alert">Invalid request.</div>';
    exit;
}

function clean($v) {
    return htmlspecialchars(trim($v ?? ""), ENT_QUOTES, "UTF-8");
}

$name    = trim($_POST["name"] ?? "");
$email   = trim($_POST["email"] ?? "");
$phone   = trim($_POST["phone"] ?? "");
$dob     = trim($_POST["dob"] ?? "");
$course  = trim($_POST["course"] ?? "");
$gender  = trim($_POST["gender"] ?? "");
$interests = $_POST["interests"] ?? [];
$message = trim($_POST["message"] ?? "");

// Server-side validation (never trust only the browser)
$errors = [];
if (!preg_match("/^[A-Za-z .]{3,}$/", $name))            $errors[] = "Invalid name.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL))          $errors[] = "Invalid email.";
if (!preg_match("/^[6-9][0-9]{9}$/", $phone))            $errors[] = "Invalid phone number.";
if (!$dob || !strtotime($dob))                           $errors[] = "Invalid date of birth.";
if ($course === "")                                      $errors[] = "Branch is required.";
if (!in_array($gender, ["Male", "Female", "Other"]))     $errors[] = "Gender is required.";
if (!is_array($interests) || count($interests) === 0)    $errors[] = "Select at least one interest.";

if ($errors) {
    echo '<div class="alert"><strong>Please fix the following:</strong><ul>';
    foreach ($errors as $e) echo "<li>" . clean($e) . "</li>";
    echo "</ul></div>";
    exit;
}

date_default_timezone_set("Asia/Kolkata");
$regId = "TS26-" . strtoupper(substr(md5($email . time()), 0, 6));
$time  = date("d M Y, h:i A");
$dobFmt = date("d M Y", strtotime($dob));
?>
<h2 class="success-title">&#10004; Registration Successful!</h2>
<table class="details">
  <tr><th>Registration ID</th><td><strong><?= clean($regId) ?></strong></td></tr>
  <tr><th>Name</th><td><?= clean($name) ?></td></tr>
  <tr><th>Email</th><td><?= clean($email) ?></td></tr>
  <tr><th>Phone</th><td><?= clean($phone) ?></td></tr>
  <tr><th>Date of Birth</th><td><?= clean($dobFmt) ?></td></tr>
  <tr><th>Branch</th><td><?= clean($course) ?></td></tr>
  <tr><th>Gender</th><td><?= clean($gender) ?></td></tr>
  <tr><th>Interests</th>
      <td><?php foreach ($interests as $i) echo '<span class="tag">' . clean($i) . '</span>'; ?></td></tr>
  <tr><th>Reason</th><td><?= $message !== "" ? nl2br(clean($message)) : "<em>Not provided</em>" ?></td></tr>
  <tr><th>Registered On</th><td><?= clean($time) ?></td></tr>
</table>
<div class="actions">
  <button id="printBtn" type="button">Print</button>
  <button id="resetBtn" type="button">New Registration</button>
</div>
