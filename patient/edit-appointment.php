<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['id'], $_SESSION['password'])) {
  // $idapp = $_GET['id'];

  $idapp = isset($_GET['id']) ? $_GET['id'] : '';
  $did = isset($_GET['did']) ? $_GET['did'] : '';


  if (isset($_POST['save'])) {
    $appid = clean($_POST['appid']);
    $appDate = clean($_POST['appDate']);
    $doctorID = clean($_POST['doctorID']);
    $patientId = clean($_POST['patientId']);
    // Check if appTime is set, if not, use $_POST['appTime'], otherwise use $_POST['currenTime']
    $appTime = isset($_POST['appTime']) ? clean($_POST['appTime']) : clean($_POST['currentAppTime']);


    $checkQuery = "SELECT * FROM appointment WHERE appDate = ? AND appTime = ? AND patientId = ?";
    $checkStmt = mysqli_prepare($con, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "sss", $appDate, $appTime, $patientId);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);

    if (mysqli_num_rows($checkResult) > 0) {
      $_SESSION['errprompt'] = "Appointment with the selected date, time, and doctor already exists.";
      //   header("location:new-appointment.php");
      header("location:edit-appointment.php?id=" . urlencode($appid) . "&did=" . urlencode($doctorID) . "&appDate=" . urlencode($appDate));
      exit;
    }

    // Continue with the update logic
    $updateQuery = "UPDATE appointment SET appDate = ?, appTime = ? WHERE appId = ?";
    $updateStmt = mysqli_prepare($con, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "ssi", $appDate, $appTime, $appid);

    if (mysqli_stmt_execute($updateStmt)) {
      $_SESSION['prompt'] = "Appointment information updated successfully.";
      header("location:appointment.php");
      exit;
    } else {
      $_SESSION['errprompt'] = "Error updating appointment information: " . mysqli_error($con);
      // header("location:edit-appointment.php?id=" . $id . "did" . $did);
      header("location:edit-appointment.php?id=" . urlencode($id) . "&did=" . urlencode($did));

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

          $query = "SELECT a.*,b.patientName,b.patientPhone, c.name_dependent,a.doctorID,d.doctorName,e.name_specialization,e.id_specialization FROM appointment a 
          JOIN patient b 
          LEFT JOIN dependent c ON a.dependentID = c.id_dependent
          JOIN doctor d
          JOIN specialization e 
          WHERE a.doctorID=d.id AND d.specialization=e.id_specialization AND a.appId=$idapp AND a.patientID=b.id";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);
            $appTime = isset($appTime) ? $appTime : '';
            $appDate = (isset($_GET['appDate']) && $_GET['appDate'] !== null) ? $_GET['appDate'] : date('Y-m-d');
          ?>
            <div class="row mt-3">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Edit Appointment</div>
                    <hr>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="appid" value="<?php echo $idapp ?>">
                      <input type="hidden" name="doctorID" value="<?php echo $doctorID ?>">
                      <input type="hidden" name="id_specialization" value="<?php echo $id_specialization ?>">
                      <input type="hidden" name="patientId" value="<?php echo $patientId ?>">
                      <input type="hidden" name="currentAppTime" value="<?php echo htmlspecialchars($appTime); ?>">
                      <div class="form-group">
                        <label for="input-1">Doctor Name</label>
                        <input type="text" class="form-control" value="<?php echo $doctorName ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Specialization</label>
                        <input type="text" class="form-control" value="<?php echo $name_specialization ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Patient Name</label>
                        <input type="text" class="form-control" value="<?php echo $patientName ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Patient Phone</label>
                        <input type="number" class="form-control" value="<?php echo $patientPhone ?>" disabled>
                      </div>

                      <div class="form-group">
                        <label for="input-1">Patient's Dependent</label>
                        <input type="text" class="form-control" value="<?php echo ($name_dependent ? $name_dependent : "No Dependent") ?>" disabled>
                      </div>

                      <div class="form-group">
                        <label for="input-1">Appointment Date</label>
                        <?php
                        // Calculate the current date
                        $currentDate = date('Y-m-d');
                        ?>
                        <input type="date" name="appDate" id="appDate" class="form-control" value="<?php echo $appDate; ?>" min="<?php echo $currentDate; ?>" required onchange="refreshPage()">
                      </div>

                      <div class="form-group" id="timeSlotsContainer">
                        <label for="input-1">Time Slot:</label>
                        <?php
                        $appTime = isset($appTime) ? $appTime : '';

                        $times = array("09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00");

                        // Get the current date and time
                        $currentDateTime = new DateTime();
                        $currentDate = $currentDateTime->format("Y-m-d");
                        $currentTime = $currentDateTime->format("H:i:s");

                        foreach ($times as $time) {
                          // Check if the combination of appDate and appTime already exists in the database
                          $checkQuery = "SELECT COUNT(*) as count FROM appointment WHERE appDate = '$appDate' AND appTime = '$time' AND doctorID='$did'";
                          $checkResult = mysqli_query($con, $checkQuery);
                          $rowCount = mysqli_fetch_assoc($checkResult)['count'];

                          // Compare the current date and time with the time slot
                          $isDisabled = ($rowCount > 0 || ($appDate == $currentDate && $time < $currentTime)) ? "disabled" : "";
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
      <script>
        function refreshPage() {
          // Get the selected date
          var selectedDate = document.getElementById('appDate').value;

          // Redirect to the same page with the selected date as a query parameter
          window.location.href = 'edit-appointment.php?id=' + <?php echo $idapp; ?> + '&did=' + <?php echo $did; ?> + '&appDate=' + selectedDate;
        }
      </script>
      <script>
        function validateForm() {
          // Get all radio buttons with the name 'appTime'
          var checkboxes = document.getElementsByName("appTime");
          var isChecked = false;

          // Check if at least one checkbox is checked
          for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
              isChecked = true;
              break;
            }
          }

          // Display an alert if no checkbox is checked
          if (!isChecked) {
            alert("Please select a time slot.");
            return false; // Prevent form submission
          }

          return true; // Continue with form submission
        }
      </script>
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