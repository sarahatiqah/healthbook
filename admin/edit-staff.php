<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['adminId'], $_SESSION['password'])) {

  if (isset($_POST['save'])) {
    $id = clean($_POST['id']);
    $staffId = clean($_POST['staffId']);
    $staffName = clean($_POST['staffName']);
    $staffPhone = clean($_POST['staffPhone']);
    $staffEmail = clean($_POST['staffEmail']);
    $password = clean($_POST['password']);
    $staffAddress = clean($_POST['staffAddress']);

    // Check if the new staffId already exists
    $staffIdCheckQuery = "SELECT id FROM staff WHERE staffId = '$staffId' AND id != '$id'";
    $staffIdCheckResult = mysqli_query($con, $staffIdCheckQuery);

    if (mysqli_num_rows($staffIdCheckResult) > 0) {
      $_SESSION['errprompt'] = "Staff ID already exists.";
      header("location:edit-staff.php?id=" . $id);
      exit;
    }

    // Check if the new staffEmail already exists
    $staffEmailCheckQuery = "SELECT staffEmail FROM staff WHERE staffEmail = '$staffEmail' AND id != '$id'";
    $staffEmailCheckResult = mysqli_query($con, $staffEmailCheckQuery);

    $patientEmailCheckQuery = "SELECT patientEmail FROM patient WHERE patientEmail = '$staffEmail'";
    $patientEmailCheckResult = mysqli_query($con, $patientEmailCheckQuery);

    $doctorEmailCheckQuery = "SELECT doctorEmail FROM doctor WHERE doctorEmail = '$staffEmail'";
    $doctorEmailCheckResult = mysqli_query($con, $doctorEmailCheckQuery);

    if (mysqli_num_rows($staffEmailCheckResult) > 0 || mysqli_num_rows($patientEmailCheckResult) > 0 || mysqli_num_rows($doctorEmailCheckResult) > 0) {
      $_SESSION['errprompt'] = "Email already exists.";
      header("location:edit-staff.php?id=" . $id);
      exit;
    }

    // Continue with the rest of your update logic
    $updateQuery = "UPDATE staff SET
        staffId = '$staffId',
        staffName = '$staffName',
        staffEmail = '$staffEmail',
        staffPhone = '$staffPhone',
        staffAddress = '$staffAddress',
        password = '$password'
        WHERE id = '$id'";

    if (mysqli_query($con, $updateQuery)) {
      $_SESSION['prompt'] = "Staff information updated successfully.";
      header("location:staff.php");
      exit;
    } else {
      $_SESSION['errprompt'] = "Error updating staff information: " . mysqli_error($con);
      header("location:edit-staff.php?id=" . $id);
    }
  }


?>
  <!DOCTYPE html>
  <html lang="en">
  <?php include "head.php"; ?>

  <body class="bg-theme bg-theme9">

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

      <?php
      include "sidebar.php";
      include "header.php";

      ?>
      <div class="clearfix"></div>

      <div class="content-wrapper">
        <div class="container-fluid">
          <?php
          if (isset($_SESSION['errprompt'])) {
            showError();
          } elseif (isset($_SESSION['prompt'])) {
            showPrompt();
          }
          $iddoc = $_GET['id'];
          $query = "SELECT * from staff WHERE id=$iddoc";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);

          ?>
            <div class="row mt-3">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Edit Staff</div>
                    <hr>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id" value="<?php echo $iddoc ?>">
                      <div class="form-group">
                        <label for="input-1">Staff ID</label>
                        <input type="text" name="staffId" class="form-control" placeholder="Enter Staff ID" value="<?php echo $staffId ?>" required>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Staff Name</label>
                        <input type="text" name="staffName" class="form-control" placeholder="Enter Staff Name" value="<?php echo $staffName ?>" required>
                      </div>
                      <div class="form-group">
                        <label for="input-2">Staff Email</label>
                        <input type="email" name="staffEmail" class="form-control" placeholder="Enter Staff Email Address" value="<?php echo $staffEmail ?>" required>
                      </div>
                      <div class="form-group">
                        <label for="input-3">Staff Mobile</label>
                        <input type="number" name="staffPhone" class="form-control" placeholder="Enter Staff Mobile Number" value="<?php echo $staffPhone ?>" required>
                      </div>
                      <div class="form-group">
                        <label for="input-3">Staff Address</label>
                        <textarea name="staffAddress" class="form-control" placeholder="Enter Staff Address" required><?php echo $staffAddress ?></textarea>
                      </div>
                      <div class="form-group">
                        <label for="input-4">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter Password" value="<?php echo $password ?>" required>
                      </div>
                      <div class="form-group">
                        <a href="staff.php" class="btn btn-secondary px-3">Cancel</a>
                        <input type="submit" class="btn btn-primary px-4" name="save" value="Save">
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <?php

          } else {
            die("Error with the query in the database");
          }
            ?>
            </div><!--End Row-->

            <!--start overlay-->
            <div class="overlay toggle-menu"></div>
            <!--end overlay-->

        </div>
        <!-- End container-fluid-->

      </div><!--End content-wrapper-->
      <!--Start Back To Top Button-->
      <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
      <!--End Back To Top Button-->

      <?php include "footer.php"; ?>


  </body>

  </html>
<?php


} else {
  header("location:../index.php");
  exit;
}

unset($_SESSION['prompt']);
unset($_SESSION['errprompt']);
mysqli_close($con);

?>