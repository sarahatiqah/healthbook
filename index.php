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
    $_SESSION['errprompt'] = "Wrong Email/ID or password.";
  }

  // Close the prepared statements
  mysqli_stmt_close($stmtPatient);
  mysqli_stmt_close($stmtAdmins);
}
?>

<body class="bg-theme bg-theme2">

  <!-- start loader -->
  <div id="pageloader-overlay" class="visible incoming">
    <div class="loader-wrapper-outer">
      <div class="loader-wrapper-inner">
        <div class="loader"></div>
      </div>
    </div>
  </div>
  <!-- end loader -->

  <!-- Start wrapper-->
  <div id="wrapper">

    <div class="loader-wrapper">
      <div class="lds-ring">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>
    <div class="card card-authentication1 mx-auto my-5">
      <div class="card-body">
        <div class="card-content p-2">
          <div class="text-center">
            <img src="assets/images/logo-icon.png" alt="logo icon">
          </div>
          <div class="card-title text-uppercase text-center py-3">HealthBook | LOGIN</div>
          <?php

          if (isset($_SESSION['prompt'])) {
            showPrompt();
          }

          if (isset($_SESSION['errprompt'])) {
            showError();
          }

          ?>
          <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="form-group">
              <label for="username" class="sr-only">Username/Email</label>
              <div class="position-relative has-icon-right">
                <input type="text" name="username" class="form-control input-shadow" placeholder="Enter Username/Email">
                <div class="form-control-position">
                  <i class="icon-user"></i>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="exampleInputPassword" class="sr-only">Password</label>
              <div class="position-relative has-icon-right">
                <input type="password" name="password" class="form-control input-shadow" placeholder="Enter Password">
                <div class="form-control-position">
                  <i class="icon-lock"></i>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-6">
                <div class="icheck-material-white">
                  <!-- <input type="checkbox" id="user-checkbox" checked="" />
                <label for="user-checkbox">Remember me</label> -->
                </div>
              </div>
              <div class="form-group col-6 text-right">
                <a href="reset-password.php">Reset Password</a>
              </div>
            </div>
            <!-- <button type="button" class="btn btn-light btn-block">LogIn</button> -->
            <input class="btn  btn-success btn-block" type="submit" name="login" value="sign in">


          </form>
        </div>
      </div>
      <div class="card-footer text-center py-3">
        <p class="text-warning mb-0">Do not have an account? <a href="register.php"> Register here</a></p>
      </div>
    </div>

    <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->



  </div><!--wrapper-->

  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>

  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>

</body>

</html>
<?php

unset($_SESSION['prompt']);
unset($_SESSION['errprompt']);

mysqli_close($con);

?>