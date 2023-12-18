<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['id'], $_SESSION['password'])) {


  if (isset($_POST['save'])) {
    $id = clean($_POST['id']);
    $icPatient = clean($_POST['icPatient']);
    $patientName = clean($_POST['patientName']);
    $patientPhone = clean($_POST['patientPhone']);
    $patientEmail = clean($_POST['patientEmail']);
    $password = clean($_POST['password']);
    $patientAddress = clean($_POST['patientAddress']);


    // Check if the email is being updated
    $currentIcQuery = "SELECT icPatient FROM patient WHERE id = '$id'";
    $currentIcResult = mysqli_query($con, $currentIcQuery);
    $currentIcRow = mysqli_fetch_assoc($currentIcResult);
    $currentIc = $currentIcRow['icPatient'];

    if ($currentIc != $icPatient) {
      // Email is being updated, check for uniqueness
      $IcCheckQuery = "SELECT icPatient FROM patient WHERE icPatient = '$icPatient'";
      $IcCheckResult = mysqli_query($con, $IcCheckQuery);

      if (mysqli_num_rows($IcCheckResult) > 0) {
        $_SESSION['errprompt'] = "IC already exists.";
        header("location:profile.php");
        exit;
      }
    }

    // Check if the email is being updated
    $currentEmailQuery = "SELECT patientEmail FROM patient WHERE id = '$id'";
    $currentEmailResult = mysqli_query($con, $currentEmailQuery);
    $currentEmailRow = mysqli_fetch_assoc($currentEmailResult);
    $currentEmail = $currentEmailRow['patientEmail'];

    if ($currentEmail != $patientEmail) {
      // Email is being updated, check for uniqueness
      $emailCheckQuery = "SELECT patientEmail FROM patient WHERE patientEmail = '$patientEmail'";
      $emailCheckResult = mysqli_query($con, $emailCheckQuery);

      if (mysqli_num_rows($emailCheckResult) > 0) {
        $_SESSION['errprompt'] = "Email already exists.";
        header("location:profile.php");
        exit;
      }
    }

    // Continue with the rest of your update logic
    $updateQuery = "UPDATE patient SET
        icPatient = '$icPatient',
        patientName = '$patientName',
        patientEmail = '$patientEmail',
        patientPhone = '$patientPhone',
        patientAddress = '$patientAddress',
        password = '$password'
        WHERE id = '$id'";

    if (mysqli_query($con, $updateQuery)) {
      $_SESSION['prompt'] = "Information updated successfully.";
      header("location:profile.php");
      exit;
    } else {
      $_SESSION['errprompt'] = "Error updating information: " . mysqli_error($con);
      header("location:profile.php");
    }
  }

?>
  <!DOCTYPE html>
  <html lang="en">

  <?php include "head.php"; ?>

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


            <div class="col-lg-8">
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
                          <h6>IC Number: <?php echo $icPatient ?></h6>
                          <h6>Name: <?php echo $patientName ?></h6>
                          <h6>Email: <?php echo $patientEmail ?></h6>
                          <h6>Phone Number: <?php echo $patientPhone ?></h6>
                          <h6>Address: <?php echo $patientAddress ?></h6>
                        </div>


                      </div>
                      <!--/row-->
                    </div>


                    <div class="tab-pane" id="edit">
                      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                        <input name="id" type="hidden" value="<?php echo $id ?>">
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">IC Number</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="icPatient" type="text" value="<?php echo $icPatient ?>">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Full Name</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="patientName" type="text" value="<?php echo $patientName ?>">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Email</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="patientEmail" type="email" value="<?php echo $patientEmail ?>">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Phone Number</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="patientPhone" type="number" value="<?php echo $patientPhone ?>">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Address</label>
                          <div class="col-lg-9">
                            <textarea name="patientAddress" class="form-control" required><?php echo $patientAddress ?></textarea>
                          </div>
                        </div>

                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Password</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="password" type="password" value="<?php echo $password ?>">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label"></label>
                          <div class="col-lg-9">
                            <a href="home.php" class="btn btn-secondary px-3">Cancel</a>
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