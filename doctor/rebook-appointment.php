<?php
session_start();
require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['doctorId'], $_SESSION['password'])) {



  if (isset($_POST['save'])) {
    $idapp = clean($_POST['idapp']);
    $appDate = clean($_POST['appDate']);
    $appTime = clean($_POST['appTime']);
    $patientID = clean($_POST['patientId']);
    // Set dependentID to null if not provided
    $dependentID = isset($_POST['dependentID']) ? clean($_POST['dependentID']) : null;
    $doctorID = clean($_POST['doctorID']);

    $checkQuery = "SELECT * FROM appointment WHERE appDate = ? AND appTime = ?  AND patientId = ?";
    $checkStmt = mysqli_prepare($con, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "sss", $appDate, $appTime, $patientID);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);

    if (mysqli_num_rows($checkResult) > 0) {
      $_SESSION['errprompt'] = "Appointment with the selected date, time, and doctor already exists.";
      header("location:rebook-appointment.php?id=" . urlencode($idapp) . "&did=" . urlencode($doctorID) . "&appDate=" . urlencode($appDate));
      exit;
    }


    if ($dependentID != NULL) {
      $insertQuery = "INSERT INTO appointment (patientId, appDate, appTime, doctorID, dependentID) VALUES (?, ?, ?, ?, ?)";
      $insertStmt = mysqli_prepare($con, $insertQuery);
      mysqli_stmt_bind_param($insertStmt, "sssss", $patientID, $appDate, $appTime, $doctorID, $dependentID);
    } else {
      $insertQuery = "INSERT INTO appointment (patientId, appDate, appTime, doctorID) VALUES (?, ?, ?, ?)";
      $insertStmt = mysqli_prepare($con, $insertQuery);
      mysqli_stmt_bind_param($insertStmt, "ssss", $patientID, $appDate, $appTime, $doctorID);
    }


    if (mysqli_stmt_execute($insertStmt)) {
      $_SESSION['prompt'] = "Rebook appointment added successfully.";
      header("location:appointment.php");
      exit;
    } else {
      $_SESSION['errprompt'] = "Error adding a rebook appointment: " . mysqli_error($con);
      header("location:new-appointment.php");
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
          $idapp = $_GET['id'];
          $did = isset($_GET['did']) ? $_GET['did'] : '';
          $query = "SELECT a.patientId,a.dependentID,a.appId,a.appTime, b.patientName,b.patientPhone, c.name_dependent,d.doctorName FROM appointment a 
          JOIN patient b
          LEFT JOIN dependent c ON a.dependentID = c.id_dependent
          JOIN doctor d
          WHERE a.appId=$idapp AND a.patientId=b.id AND a.doctorID=$did AND a.doctorID=d.id";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);
            $appTime = isset($appTime) ? $appTime : '';
            $appDate = (isset($_GET['appDate']) && $_GET['appDate'] !== null) ? $_GET['appDate'] : date('Y-m-d');
          ?>
            <div class="row mt-3">
              <div class="col-lg-4">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Rebook Appointment</div>
                    <hr>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" onsubmit="return validateForm()">
                      <input type="hidden" name="idapp" value="<?php echo $idapp ?>">
                      <input type="hidden" name="currentAppTime" value="<?php echo htmlspecialchars($appTime); ?>">
                      <input type="hidden" name="patientId" value="<?php echo $patientId ?>">
                      <input type="hidden" name="dependentID" value="<?php echo $dependentID ?>">
                      <input type="hidden" name="doctorID" value="<?php echo $did ?>">
                      <div class="form-group">
                        <label for="input-1">Doctor Name</label>
                        <input type="text" class="form-control" value="<?php echo $doctorName ?>" disabled>
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
                        <input type="text" name="dependentID" class="form-control" value="<?php echo ($name_dependent ? $name_dependent : "No Dependent") ?>" disabled>
                      </div>


                      <div class="form-group">
                        <label for="input-1">Appointment Date</label>
                        <?php
                        // Calculate the current date
                        $currentDate = date('Y-m-d');
                        $currentDateTime = new DateTime();
                        $currentDate = $currentDateTime->format("Y-m-d");
                        $currentTime = $currentDateTime->format("H:i:s");

                        ?>
                        <input type="date" name="appDate" id="appDate" class="form-control" value="<?php echo $appDate; ?>" min="<?php echo $currentDate; ?>" required onchange="refreshPageDate()">
                      </div>

                      <div class="form-group" id="timeSlotsContainer">
                        <label for="input-1">Time Slot:</label>
                        <?php
                        $appTime = isset($appTime) ? $appTime : '';

                        $times = array("09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00");

                        foreach ($times as $time) {
                          // Check if the combination of appDate and appTime already exists in the database
                          $checkQuery = "SELECT COUNT(*) as count FROM appointment WHERE appDate = '$appDate' AND appTime = '$time' AND doctorID='$did'";
                          $checkResult = mysqli_query($con, $checkQuery);
                          $rowCount = mysqli_fetch_assoc($checkResult)['count'];

                          // Enable the time slot if the combination does not exist in the database
                          $isDisabled = ($rowCount > 0 || ($appDate == $currentDate && $time < $currentTime)) ? "disabled" : "";
                          // $isChecked = ($appTime == $time) ? "checked" : "";
                        ?>
                          <div class="icheck-material-white">
                            <input type="radio" id="checkbox<?php echo $time; ?>" name="appTime" value="<?php echo $time; ?>" <?php echo $isDisabled; ?> />
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
        function refreshPageDate() {
          // Get the selected date
          var selectedDate = document.getElementById('appDate').value;

          // Redirect to the same page with the selected date as a query parameter
          window.location.href = 'rebook-appointment.php?id=' + <?php echo $idapp; ?> + '&did=' + <?php echo $did; ?> + '&appDate=' + encodeURIComponent(selectedDate);
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