<!DOCTYPE html>
<html lang="en">

<?php
session_start();
include "dbconnection.php";
include "functions.php";
include "head.php";

if (isset($_POST['login'])) {
  $uname = clean($_POST['username']);
  $password = clean($_POST['password']);

  // Query for patients
  $queryPatient = "SELECT * FROM patient WHERE patientEmail = ? AND password = ?";
  $stmtPatient = mysqli_prepare($con, $queryPatient);
  mysqli_stmt_bind_param($stmtPatient, "ss", $uname, $password);
  mysqli_stmt_execute($stmtPatient);
  $resultPatient = mysqli_stmt_get_result($stmtPatient);

  // Query for admins
  $queryAdmins = "SELECT * FROM admin WHERE adminId = ? AND password = ?";
  $stmtAdmins = mysqli_prepare($con, $queryAdmins);
  mysqli_stmt_bind_param($stmtAdmins, "ss", $uname, $password);
  mysqli_stmt_execute($stmtAdmins);
  $resultAdmins = mysqli_stmt_get_result($stmtAdmins);

  // Query for staff
  $queryStaff = "SELECT * FROM staff WHERE staffId = ? AND password = ?";
  $stmtStaff = mysqli_prepare($con, $queryStaff);
  mysqli_stmt_bind_param($stmtStaff, "ss", $uname, $password);
  mysqli_stmt_execute($stmtStaff);
  $resultStaff = mysqli_stmt_get_result($stmtStaff);

  // Query for doctor
  $queryDoctor = "SELECT * FROM doctor WHERE doctorId = ? AND password = ?";
  $stmtDoctor = mysqli_prepare($con, $queryDoctor);
  mysqli_stmt_bind_param($stmtDoctor, "ss", $uname, $password);
  mysqli_stmt_execute($stmtDoctor);
  $resultDoctor = mysqli_stmt_get_result($stmtDoctor);

  if (mysqli_num_rows($resultPatient) > 0) {
    $row = mysqli_fetch_assoc($resultPatient);
    $_SESSION['id'] = $row['id'];
    $_SESSION['patientEmail'] = $row['patientEmail'];
    $_SESSION['patientName'] = $row['patientName'];
    $_SESSION['password'] = $row['password'];
    header("location: patient/home.php");
    exit;
  } elseif (mysqli_num_rows($resultAdmins) > 0) {
    $row = mysqli_fetch_assoc($resultAdmins);
    $_SESSION['id'] = $row['id'];
    $_SESSION['adminId'] = $row['adminId'];
    $_SESSION['adminName'] = $row['adminName'];
    $_SESSION['password'] = $row['password'];
    header("location: admin/home.php");
    exit;
  } elseif (mysqli_num_rows($resultStaff) > 0) {
    $row = mysqli_fetch_assoc($resultStaff);
    $_SESSION['id'] = $row['id'];
    $_SESSION['staffId'] = $row['staffId'];
    $_SESSION['staffName'] = $row['staffName'];
    $_SESSION['password'] = $row['password'];
    header("location: staff/home.php");
    exit;
  } elseif (mysqli_num_rows($resultDoctor) > 0) {
    $row = mysqli_fetch_assoc($resultDoctor);
    $_SESSION['id'] = $row['id'];
    $_SESSION['doctorId'] = $row['doctorId'];
    $_SESSION['doctorName'] = $row['doctorName'];
    $_SESSION['password'] = $row['password'];
    header("location: doctor/home.php");
    exit;
  } else {
    $_SESSION['errprompt'] = "Your credentials are incorrect.";
  }

  // Close the prepared statements
  mysqli_stmt_close($stmtPatient);
  mysqli_stmt_close($stmtAdmins);
}
?>

<body class="bg-theme bg-theme9 vh-100 d-flex align-items-center">
  <!-- Card -->
  <div class="card card-authentication1 mx-auto p-2">
    <div class="card-body">
      <!-- Title -->
      <div class="text-center">
        <img src="assets/images/logo-icon.svg" class="w-50" alt="logo">
      </div>
      <div class="card-title text-center py-3">Sign In Your Account</div>

      <!-- Alerts -->
      <?php
      if (isset($_SESSION['prompt'])) {
        showPrompt();
      }
      if (isset($_SESSION['errprompt'])) {
        showError();
      }
      ?>

      <!-- Form -->
      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <div class="form-group">
          <label for="username">ID / Email</label>
          <input type="text" name="username" class="form-control input-shadow" placeholder="Enter ID / Email" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="position-relative has-icon-right">
            <input type="password" name="password" class="form-control input-shadow" id="password" placeholder="Enter Password" required>
            <div class="form-control-position toggle-password">
              <i class="fa fa-eye" id="togglePassword"></i>
            </div>
          </div>
        </div>

        <div class="form-group text-right">
          <a href="reset-password.php">
            <h6 class="link">Reset Password</h6>
          </a>
        </div>

        <input class="btn btn-primary btn-block mt-4" type="submit" name="login" value="Sign In">
      </form>
    </div>

    <div class="card-footer text-center border-0 pt-0">
      <h6>Don't have an account? <a href="register.php" class="link">Sign up</a></h6>
    </div>
  </div>
  <!-- Card -->

  <!-- Back To Top Button -->
  <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>
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

mysqli_close($con);
?>