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
        $_SESSION['prompt'] = "Account registered. You can now log in.";
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

    <div class="card card-authentication1 mx-auto my-4">
      <div class="card-body">
        <div class="card-content p-2">
          <div class="text-center">
            <img src="assets/images/logo-icon.png" alt="logo icon">
          </div>
          <div class="card-title text-uppercase text-center py-3">HealthBook | Registration</div>
          <?php
          if (isset($_SESSION['errprompt'])) {
            showError();
          }
          ?>
          <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
              <label for="icPatient" class="sr-only">Name</label>
              <div class="position-relative has-icon-right">
                <input type="number" name="icPatient" class="form-control input-shadow" placeholder="Enter Your IC Number" required>
                <div class="form-control-position">
                  <i class="zmdi zmdi-accounts-list"></i>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="patientName" class="sr-only">Name</label>
              <div class="position-relative has-icon-right">
                <input type="text" name="patientName" class="form-control input-shadow" placeholder="Enter Your Full Name" required>
                <div class="form-control-position">
                  <i class="icon-user"></i>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="patientEmail" class="sr-only">Email ID</label>
              <div class="position-relative has-icon-right">
                <input type="email" name="patientEmail" class="form-control input-shadow" placeholder="Enter Your Email ID" required>
                <div class="form-control-position">
                  <i class="icon-envelope-open"></i>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="password" class="sr-only">Password</label>
              <div class="position-relative has-icon-right">
                <input type="password" name="password" class="form-control input-shadow" placeholder="Enter Password" required>
                <div class="form-control-position">
                  <i class="icon-lock"></i>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="patientPhone" class="sr-only">Phone Number</label>
              <div class="position-relative has-icon-right">
                <input type="number" name="patientPhone" class="form-control input-shadow" placeholder="Enter Your Phone Number" required>
                <div class="form-control-position">
                  <i class="icon-phone"></i>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="patientGender" class="sr-only">Gender</label>
              <div class="position-relative has-icon-right">
                <select name="patientGender" class="form-control input-shadow" required>
                  <option value="" selected disabled>Select Gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="patientRace" class="sr-only">Race</label>
              <div class="position-relative has-icon-right">
                <select name="patientRace" class="form-control input-shadow" required>
                  <option value="" selected disabled>Select Race</option>
                  <option value="Malay">Malay</option>
                  <option value="Chinese">Chinese</option>
                  <option value="Indian">Indian</option>
                  <option value="Other Bumiputera">Other Bumiputera</option>
                  <option value="Others">Others</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="patientAddress" class="sr-only">Address</label>
              <div class="position-relative has-icon-right">
                <textarea name="patientAddress" class="form-control input-shadow" placeholder="Enter Your Address" required></textarea>
                <div class="form-control-position">
                  <i class="zmdi zmdi-group"></i>
                </div>
              </div>
            </div>

            <!-- <div class="form-group">
              <div class="icheck-material-white">
                <input type="checkbox" id="user-checkbox" />
                <label for="user-checkbox">I Agree With Terms & Conditions</label>
              </div>
            </div> -->

            <!-- <button type="button" class="btn btn-success btn-block waves-effect waves-light">Sign Up</button> -->
            <input type="submit" name="register" class="btn btn-success btn-block waves-effect waves-light" value="sign up">



          </form>
        </div>
      </div>
      <div class="card-footer text-center py-3">
        <p class="text-warning mb-0">Already have an account? <a href="index.php"> Login here</a></p>
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

?>