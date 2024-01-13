<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['adminId'], $_SESSION['password'])) {

  if (isset($_POST['save'])) {
    $id = clean($_POST['id']);
    $doctorId = clean($_POST['doctorId']);
    $doctorName = clean($_POST['doctorName']);
    $doctorPhone = clean($_POST['doctorPhone']);
    $doctorEmail = clean($_POST['doctorEmail']);
    $password = clean($_POST['password']);
    $specialization = clean($_POST['specialization']);

    // Check if the new doctorId already exists
    $doctorIdCheckQuery = "SELECT id FROM doctor WHERE doctorId = '$doctorId' AND id != '$id'";
    $doctorIdCheckResult = mysqli_query($con, $doctorIdCheckQuery);

    if (mysqli_num_rows($doctorIdCheckResult) > 0) {
      $_SESSION['errprompt'] = "Doctor ID already exists.";
      header("location:edit-doctor.php?id=" . $id);
      exit;
    }

    // Check if the new doctorEmail already exists
    $doctorEmailCheckQuery = "SELECT id FROM doctor WHERE doctorEmail = '$doctorEmail' AND id != '$id'";
    $doctorEmailCheckResult = mysqli_query($con, $doctorEmailCheckQuery);

    $staffEmailCheckQuery = "SELECT staffEmail FROM staff WHERE staffEmail = '$doctorEmail'";
    $staffEmailCheckResult = mysqli_query($con, $staffEmailCheckQuery);

    $patientEmailCheckQuery = "SELECT patientEmail FROM patient WHERE patientEmail = '$doctorEmail'";
    $patientEmailCheckResult = mysqli_query($con, $patientEmailCheckQuery);

    if (mysqli_num_rows($doctorEmailCheckResult) > 0 || mysqli_num_rows($staffEmailCheckResult) > 0 || mysqli_num_rows($patientEmailCheckResult) > 0) {
      $_SESSION['errprompt'] = "Email already exists.";
      header("location:edit-doctor.php?id=" . $id);
      exit;
    }

    // Continue with the rest of your update logic
    $updateQuery = "UPDATE doctor SET
        doctorId = '$doctorId',
        doctorName = '$doctorName',
        doctorEmail = '$doctorEmail',
        doctorPhone = '$doctorPhone',
        specialization = '$specialization',
        password = '$password'
        WHERE id = '$id'";

    if (mysqli_query($con, $updateQuery)) {
      $_SESSION['prompt'] = "Doctor information updated successfully.";
      header("location:doctors.php");
      exit;
    } else {
      $_SESSION['errprompt'] = "Error updating doctor information: " . mysqli_error($con);
      header("location:edit-doctor.php?id=" . $id);
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
          $query = "SELECT a.*,b.name_specialization from doctor a JOIN specialization b WHERE a.specialization=b.id_specialization AND a.id=$iddoc";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);

          ?>
            <div class="row mt-3">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Edit Doctor</div>
                    <hr>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id" value="<?php echo $iddoc ?>">
                      <div class="form-group">
                        <label for="input-1">Doctor ID</label>
                        <input type="text" name="doctorId" class="form-control" placeholder="Enter Doctor ID" value="<?php echo $doctorId ?>" required>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Doctor Name</label>
                        <input type="text" name="doctorName" class="form-control" placeholder="Enter Doctor Name" value="<?php echo $doctorName ?>" required>
                      </div>
                      <div class="form-group">
                        <label for="input-2">Doctor Email</label>
                        <input type="email" name="doctorEmail" class="form-control" placeholder="Enter Doctor Email Address" value="<?php echo $doctorEmail ?>" required>
                      </div>
                      <div class="form-group">
                        <label for="input-3">Doctor Mobile</label>
                        <input type="number" name="doctorPhone" class="form-control" placeholder="Enter Doctor Mobile Number" value="<?php echo $doctorPhone ?>" required>
                      </div>
                      <div class="form-group">
                        <label for="input-3">Doctor Specialization</label>
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
                      <div class="form-group">
                        <label for="input-4">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter Password" value="<?php echo $password ?>" required>
                      </div>
                      <div class="form-group">
                        <a href="doctors.php" class="btn btn-secondary px-3">Cancel</a>
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