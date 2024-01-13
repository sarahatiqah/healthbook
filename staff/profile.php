<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['staffId'], $_SESSION['password'])) {

  if (isset($_POST['save'])) {
    $id = clean($_POST['id']);
    $staffId = clean($_POST['staffId']);
    $staffName = clean($_POST['staffName']);
    $staffPhone = clean($_POST['staffPhone']);
    $staffEmail = clean($_POST['staffEmail']);
    $password = clean($_POST['password']);
    $staffAddress = clean($_POST['staffAddress']);

    // Check if the staffId is being updated
    $currentIdQuery = "SELECT staffId FROM staff WHERE id = '$id'";
    $currentIdResult = mysqli_query($con, $currentIdQuery);
    $currentIdRow = mysqli_fetch_assoc($currentIdResult);
    $currentId = $currentIdRow['staffId'];

    if ($currentId != $staffId) {
      // staffId is being updated, check for uniqueness
      $idCheckQuery = "SELECT staffId FROM staff WHERE staffId = '$staffId'";
      $idCheckResult = mysqli_query($con, $idCheckQuery);

      if (mysqli_num_rows($idCheckResult) > 0) {
        $_SESSION['errprompt'] = "Staff ID already exists.";
        header("location:profile.php");
        exit;
      }
    }

    // Check if the staffEmail is being updated
    $currentEmailQuery = "SELECT staffEmail FROM staff WHERE id = '$id'";
    $currentEmailResult = mysqli_query($con, $currentEmailQuery);
    $currentEmailRow = mysqli_fetch_assoc($currentEmailResult);
    $currentEmail = $currentEmailRow['staffEmail'];

    // Check if the new staffEmail already exists
    $emailCheckQuery = "SELECT staffEmail FROM staff WHERE staffEmail = '$staffEmail' AND id != '$id'";
    $emailCheckResult = mysqli_query($con, $emailCheckQuery);

    $doctorEmailCheckQuery = "SELECT doctorEmail FROM doctor WHERE doctorEmail = '$staffEmail'";
    $doctorEmailCheckResult = mysqli_query($con, $doctorEmailCheckQuery);

    $patientEmailCheckQuery = "SELECT patientEmail FROM patient WHERE patientEmail = '$staffEmail'";
    $patientEmailCheckResult = mysqli_query($con, $patientEmailCheckQuery);

    if (mysqli_num_rows($emailCheckResult) > 0 || mysqli_num_rows($doctorEmailCheckResult) > 0 || mysqli_num_rows($patientEmailCheckResult) > 0) {
      $_SESSION['errprompt'] = "Email already exists.";
      header("location:profile.php");
      exit;
    }

    // Continue with the rest of your update logic
    $updateQuery = "UPDATE staff SET
        staffName = '$staffName',
        staffEmail = '$staffEmail',
        staffPhone = '$staffPhone',
        staffAddress = '$staffAddress',
        password = '$password'
        WHERE id = '$id'";

    if (mysqli_query($con, $updateQuery)) {
      $_SESSION['prompt'] = "Information updated successfully.";
      header("location:profile.php");
      exit;
    } else {
      $_SESSION['errprompt'] = "Error updating information: " . mysqli_error($con);
      header("location:profile.php");
      exit;
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
          ?>
          <div class="row mt-3">


            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                    <li class="nav-item">
                      <a href="javascript:void();" data-target="#profile" data-toggle="pill" class="nav-link active"><i class="icon-user"></i> <span class="hidden-xs">Profile</span></a>
                      </l i>
                    <li class="nav-item">
                      <a href="javascript:void();" data-target="#edit" data-toggle="pill" class="nav-link"><i class="icon-note"></i> <span class="hidden-xs">Edit</span></a>
                    </li>
                  </ul>
                  <div class="tab-content p-3">
                    <div class="tab-pane active" id="profile">
                      <h5 class="mb-3">My Profile</h5>
                      <div class="row">
                        <div class="col-md-6">
                          <h6>Staff ID: <?php echo $staffId ?></h6>
                          <h6>Name: <?php echo $staffName ?></h6>
                          <h6>Email: <?php echo $staffEmail ?></h6>
                          <h6>Phone Number: <?php echo $staffPhone ?></h6>
                          <h6>Address: <?php echo $staffAddress ?></h6>
                        </div>


                      </div>
                      <!--/row-->
                    </div>


                    <div class="tab-pane" id="edit">
                      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                        <input name="id" type="hidden" value="<?php echo $id ?>">
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Staff ID</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="staffId" type="text" value="<?php echo $staffId ?>" disabled>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Full Name</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="staffName" type="text" value="<?php echo $staffName ?>" required>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Email</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="staffEmail" type="email" value="<?php echo $staffEmail ?>" required>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Phone Number</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="staffPhone" type="number" value="<?php echo $staffPhone ?>" required>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Address</label>
                          <div class="col-lg-9">
                            <textarea name="staffAddress" class="form-control" required><?php echo $staffAddress ?></textarea>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Password</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="password" type="password" value="<?php echo $password ?>" required>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label"></label>
                          <div class="col-lg-9">
                            <a href="profile.php" class="btn btn-secondary px-3">Cancel</a>
                            <input type="submit" class="btn btn-primary" name="save" value="Save Changes">
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

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