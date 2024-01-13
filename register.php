<!DOCTYPE html>
<html lang="en">

<?php
session_start();
include "dbconnection.php";
include "functions.php";
include "head.php";

if (isset($_POST['register'])) {
  $icPatient = clean($_POST['icPatient']);
  $patientName = clean($_POST['patientName']);
  $patientPhone = clean($_POST['patientPhone']);
  $patientEmail = clean($_POST['patientEmail']);
  $password = clean($_POST['password']);
  $patientGender = clean($_POST['patientGender']);
  $patientRace = clean($_POST['patientRace']);
  $patientAddress = clean($_POST['patientAddress']);

  // Use prepared statements to avoid SQL injection
  $stmtPatient = mysqli_prepare($con, "SELECT patientEmail FROM patient WHERE patientEmail = ?");
  mysqli_stmt_bind_param($stmtPatient, "s", $patientEmail);
  mysqli_stmt_execute($stmtPatient);
  mysqli_stmt_store_result($stmtPatient);

  $stmtDoctor = mysqli_prepare($con, "SELECT doctorEmail FROM doctor WHERE doctorEmail = ?");
  mysqli_stmt_bind_param($stmtDoctor, "s", $patientEmail);
  mysqli_stmt_execute($stmtDoctor);
  mysqli_stmt_store_result($stmtDoctor);

  $stmtStaff = mysqli_prepare($con, "SELECT staffEmail FROM staff WHERE staffEmail = ?");
  mysqli_stmt_bind_param($stmtStaff, "s", $patientEmail);
  mysqli_stmt_execute($stmtStaff);
  mysqli_stmt_store_result($stmtStaff);

  if (mysqli_stmt_num_rows($stmtPatient) == 0 && mysqli_stmt_num_rows($stmtDoctor) == 0 && mysqli_stmt_num_rows($stmtStaff) == 0) {
    // Check if IC number already exists
    $stmtIC = mysqli_prepare($con, "SELECT icPatient FROM patient WHERE icPatient = ?");
    mysqli_stmt_bind_param($stmtIC, "s", $icPatient);
    mysqli_stmt_execute($stmtIC);
    mysqli_stmt_store_result($stmtIC);

    if (mysqli_stmt_num_rows($stmtIC) == 0) {
      // Insert new patient
      $stmtInsert = mysqli_prepare($con, "INSERT INTO patient (icPatient, patientName, patientEmail, password, patientPhone, patientGender, patientRace, patientAddress) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      mysqli_stmt_bind_param($stmtInsert, "ssssssss", $icPatient, $patientName, $patientEmail, $password, $patientPhone, $patientGender, $patientRace, $patientAddress);

      if (mysqli_stmt_execute($stmtInsert)) {
        $_SESSION['prompt'] = "Account registered. You can now sign in.";
        header("location:index.php");
        exit;
      } else {
        $_SESSION['errprompt'] = "Error with the query: " . mysqli_error($con);
      }
    } else {
      $_SESSION['errprompt'] = "IC number already exists.";
    }
  } else {
    $_SESSION['errprompt'] = "Email already exists.";
  }

  mysqli_close($con); // Close the database connection
}
?>

<?php
if (isset($_SESSION['password'])) {
  if (isset($_SESSION['staffId']))
    header("location:staff/home.php");
  else if (isset($_SESSION['adminId']))
    header("location:admin/home.php");
  else if (isset($_SESSION['doctorId']))
    header("location:doctor/home.php");
  else if (isset($_SESSION['id']))
    header("location:patient/home.php");
  exit;
}
?>

<body class="bg-theme bg-theme9 pb-4">
  <!-- Card -->
  <div class="card card-authentication1 mx-auto p-2 mt-4 mb-0">
    <div class="card-body">
      <!-- Title -->
      <div class="text-center">
        <img src="assets/images/logo.svg" class="w-50" alt="logo">
      </div>
      <div class="card-title text-center py-3">Register an Account</div>

      <!-- Errors -->
      <?php
      if (isset($_SESSION['errprompt'])) {
        showError();
      }
      ?>

      <!-- Form -->
      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="icPatient">IC Number</label>
          <input type="text" name="icPatient" class="form-control input-shadow" placeholder="Enter IC Number" required pattern="^\d{12}$" title="Must be 12 numbers">
        </div>
        <div class="form-group">
          <label for="patientName">Name (As in IC)</label>
          <input type="text" name="patientName" class="form-control input-shadow" placeholder="Enter Full Name" required>
        </div>
        <div class="form-group">
          <label for="patientEmail">Email</label>
          <input type="email" name="patientEmail" class="form-control input-shadow" placeholder="Enter Email" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <div class="position-relative has-icon-right">
            <input type="password" name="password" id="password" class="form-control input-shadow" placeholder="Enter Password" required minlength="5">
            <div class="form-control-position toggle-password">
              <i class="fa fa-eye" id="togglePassword"></i>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="patientPhone">Phone Number</label>
          <input type="text" name="patientPhone" class="form-control input-shadow" placeholder="Enter Phone Number" required pattern="^[0-9]+$" title="Must be only numbers">
        </div>
        <div class="form-group">
          <label for="patientGender">Gender</label>
          <select name="patientGender" class="form-control input-shadow" required>
            <option value="" selected disabled>Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>
        <div class="form-group">
          <label for="patientRace">Race</label>
          <select name="patientRace" class="form-control input-shadow" required>
            <option value="" selected disabled>Select Race</option>
            <option value="Malay">Malay</option>
            <option value="Chinese">Chinese</option>
            <option value="Indian">Indian</option>
            <option value="Other Bumiputera">Other Bumiputera</option>
            <option value="Others">Others</option>
          </select>
        </div>
        <div class="form-group">
          <label for="patientAddress">Address</label>
          <textarea name="patientAddress" class="form-control input-shadow" placeholder="Enter Address" required pattern="\w+" title="Must not be blank"></textarea>
        </div>

        <div class="pt-3">
          <input type="submit" name="register" class="btn btn-primary btn-block" value="Sign Up">
        </div>
      </form>
    </div>

    <div class="card-footer text-center border-0 pt-0">
      <h6>Already have an account? <a href="index.php" class="link">Sign in</a></h6>
    </div>
  </div>

  <!-- Back To Top Button -->
  <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
  <!-- Back To Top Button -->

  <!-- Bootstrap Core -->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function(e) {
      // toggle the type attribute
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      // toggle the eye icon class
      this.classList.toggle('fa-eye-slash');
    });
  </script>
</body>

</html>

<?php

unset($_SESSION['prompt']);
unset($_SESSION['errprompt']);

?>