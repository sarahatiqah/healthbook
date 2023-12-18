<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['doctorId'], $_SESSION['password'])) {



  if (isset($_POST['save'])) {
    $id = clean($_POST['id']);
    $appDate = clean($_POST['appDate']);
    $appTime = clean($_POST['appTime']);


    // Check if the new combination of date and time already exists
    $checkQuery = "SELECT * FROM appointment WHERE appDate = '$appDate' AND appTime = '$appTime' AND appId != '$id'";
    $checkResult = mysqli_query($con, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
      $_SESSION['errprompt'] = "Appointment with the selected date and time already exists.";
      header("location:edit-appointment.php?id=" . $id);
      exit;
    }

    // Continue with the update logic
    $updateQuery = "UPDATE appointment SET
            appDate = '$appDate',
            appTime = '$appTime'
            WHERE appId = '$id'";

    if (mysqli_query($con, $updateQuery)) {
      $_SESSION['prompt'] = "Appointment information updated successfully.";
      header("location:appointment.php");
      exit;
    } else {
      $_SESSION['errprompt'] = "Error updating appointment information: " . mysqli_error($con);
      header("location:edit-appointment.php?id=" . $id);
      exit;
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
          $idapp = $_GET['id'];
          $query = "SELECT a.*,b.patientName,b.patientPhone FROM appointment a JOIN patient b WHERE a.appId=$idapp AND a.patientID=b.id";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);

          ?>
            <div class="row mt-3">
              <div class="col-lg-4">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Edit Appointment</div>
                    <hr>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id" value="<?php echo $idapp ?>">
                      <div class="form-group">
                        <label for="input-1">Patient Name</label>
                        <input type="text" class="form-control" value="<?php echo $patientName ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Patient Phone</label>
                        <input type="number" class="form-control" value="<?php echo $patientPhone ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Appointment Date</label>
                        <input type="date" name="appDate" class="form-control" value="<?php echo $appDate ?>" required>
                      </div>
                      <div class="form-group">
                        <?php
                        $times = array("09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00");

                        foreach ($times as $time) {
                          // Check if the combination of appDate and appTime already exists
                          $checkQuery = "SELECT * FROM appointment WHERE appDate = '$appDate' AND appTime = '$time' AND appId != '$id'";
                          $checkResult = mysqli_query($con, $checkQuery);

                          $isDisabled = mysqli_num_rows($checkResult) > 0 ? "disabled" : "";
                          $isChecked = ($appTime == $time) ? "checked" : "";
                        ?>

                          <div class="icheck-material-white">
                            <input type="radio" id="checkbox<?php echo $time; ?>" name="appTime" value="<?php echo $time; ?>" <?php echo $isChecked; ?> <?php echo $isDisabled; ?> />
                            <label for="checkbox<?php echo $time; ?>"><?php echo date('h:i A', strtotime($time)); ?></label>
                          </div>

                        <?php
                        }
                        ?>
                      </div>

                      <div class="form-group">
                        <a href="appointment.php" class="btn btn-secondary px-3">Cancel</a>
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