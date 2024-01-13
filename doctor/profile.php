<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['doctorId'], $_SESSION['password'])) {

  if (isset($_POST['save'])) {
    $id = clean($_POST['id']);
    $doctorId = clean($_POST['doctorId']);
    $doctorName = clean($_POST['doctorName']);
    $doctorPhone = clean($_POST['doctorPhone']);
    $doctorEmail = clean($_POST['doctorEmail']);
    $password = clean($_POST['password']);
    $specialization = clean($_POST['specialization']);

    // Check if the doctorId is being updated
    $currentIdQuery = "SELECT doctorId FROM doctor WHERE id = '$id'";
    $currentIdResult = mysqli_query($con, $currentIdQuery);
    $currentIdRow = mysqli_fetch_assoc($currentIdResult);
    $currentId = $currentIdRow['doctorId'];

    if ($currentId != $doctorId) {
      // doctorId is being updated, check for uniqueness
      $idCheckQuery = "SELECT doctorId FROM doctor WHERE doctorId = '$doctorId'";
      $idCheckResult = mysqli_query($con, $idCheckQuery);

      if (mysqli_num_rows($idCheckResult) > 0) {
        $_SESSION['errprompt'] = "Doctor ID already exists.";
        header("location:profile.php");
        exit;
      }
    }

    // Check if the doctorEmail is being updated
    $currentEmailQuery = "SELECT doctorEmail FROM doctor WHERE id = '$id'";
    $currentEmailResult = mysqli_query($con, $currentEmailQuery);
    $currentEmailRow = mysqli_fetch_assoc($currentEmailResult);
    $currentEmail = $currentEmailRow['doctorEmail'];

    // Check if the new doctorEmail already exists
    $emailCheckQuery = "SELECT doctorEmail FROM doctor WHERE doctorEmail = '$doctorEmail' AND id != '$id'";
    $emailCheckResult = mysqli_query($con, $emailCheckQuery);

    $staffEmailCheckQuery = "SELECT staffEmail FROM staff WHERE staffEmail = '$doctorEmail'";
    $staffEmailCheckResult = mysqli_query($con, $staffEmailCheckQuery);

    $patientEmailCheckQuery = "SELECT patientEmail FROM patient WHERE patientEmail = '$doctorEmail'";
    $patientEmailCheckResult = mysqli_query($con, $patientEmailCheckQuery);

    if (mysqli_num_rows($emailCheckResult) > 0 || mysqli_num_rows($staffEmailCheckResult) > 0 || mysqli_num_rows($patientEmailCheckResult) > 0) {
      $_SESSION['errprompt'] = "Email already exists.";
      header("location:profile.php");
      exit;
    }

    // Continue with the rest of your update logic
    $updateQuery = "UPDATE doctor SET
        doctorName = '$doctorName',
        doctorEmail = '$doctorEmail',
        doctorPhone = '$doctorPhone',
        specialization = '$specialization',
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
                          <h6>Doctor ID: <?php echo $doctorId ?></h6>
                          <h6>Name: <?php echo $doctorName ?></h6>
                          <h6>Email: <?php echo $doctorEmail ?></h6>
                          <h6>Phone Number: <?php echo $doctorPhone ?></h6>
                          <h6>Specialization: <?php echo $name_specialization ?></h6>
                        </div>


                      </div>
                      <!--/row-->
                    </div>


                    <div class="tab-pane" id="edit">
                      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                        <input name="id" type="hidden" value="<?php echo $id ?>">
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">doctor ID</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="doctorId" type="text" value="<?php echo $doctorId ?> " disabled>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Full Name</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="doctorName" type="text" value="<?php echo $doctorName ?>" required>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Email</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="doctorEmail" type="email" value="<?php echo $doctorEmail ?>" required>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Phone Number</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="doctorPhone" type="number" value="<?php echo $doctorPhone ?>" required>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Specialization</label>
                          <div class="col-lg-9">
                            <select class="form-control" name="specialization" required>
                              <option value="" selected disabled>Select Specialization</option>

                              <?php
                              $query = "SELECT * FROM specialization";
                              $result = mysqli_query($con, $query);
                              while ($row = mysqli_fetch_assoc($result)) {
                                $id_specialization = $row['id_specialization'];
                                $name_specialization = $row['name_specialization'];

                                // Check if the current faculty value matches the option, if yes, set it as selected
                                $selected = ($specialization == $id_specialization) ? "selected" : "";

                                echo '<option value="' . $id_specialization . '" ' . $selected . '>' . $name_specialization . '</option>';
                              }
                              ?>

                            </select>
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