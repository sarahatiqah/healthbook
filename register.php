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
  $patientAddress = clean($_POST['patientAddress']);



  $query = "SELECT patientEmail FROM patient WHERE patientEmail = '$patientEmail'";
  $result = mysqli_query($con, $query);

  if (mysqli_num_rows($result) == 0) {

    $query = "SELECT icPatient FROM patient WHERE icPatient = '$icPatient'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 0) {

      $query = "INSERT INTO patient (icPatient,patientName,patientEmail, password,patientPhone,patientAddress)
        VALUES ('$icPatient', '$patientName', '$patientEmail','$password', '$patientPhone', '$patientAddress')";

      if (mysqli_query($con, $query)) {

        $_SESSION['prompt'] = "Account registered. You can now log in.";
        header("location:index.php");
        exit;
      } else {

        die("Error with the query");
      }
    } else {

      $_SESSION['errprompt'] = "IC number already exists.";
    }
  } else {

    $_SESSION['errprompt'] = "Email already exists.";
  }
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
                <input type="text" name="patientEmail" class="form-control input-shadow" placeholder="Enter Your Email ID" required>
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
              <label for="patientAddress" class="sr-only">Address</label>
              <div class="position-relative has-icon-right">
                <textarea name="patientAddress" class="form-control input-shadow" placeholder="Enter Your Address" required></textarea>
                <div class="form-control-position">
                  <i class="zmdi zmdi-group"></i>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="icheck-material-white">
                <input type="checkbox" id="user-checkbox" />
                <label for="user-checkbox">I Agree With Terms & Conditions</label>
              </div>
            </div>

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