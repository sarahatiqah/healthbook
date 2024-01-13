<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['id'], $_SESSION['password'])) {
  $did = isset($_GET['did']) ? $_GET['did'] : '';

  if (isset($_POST['save'])) {
    $appDate = clean($_POST['appDate']);
    $appTime = clean($_POST['appTime']);
    $patientID = clean($_POST['patientID']);
    // Set dependentID to null if not provided
    $dependentID = isset($_POST['dependentID']) ? clean($_POST['dependentID']) : null;
    $doctorID = clean($_POST['doctorID']);
    $id_specialization = clean($_POST['id_specialization']);


    $checkQuery = "SELECT * FROM appointment WHERE appDate = ? AND appTime = ? AND patientId = ?";
    $checkStmt = mysqli_prepare($con, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "sss", $appDate, $appTime, $patientID);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);

    if (mysqli_num_rows($checkResult) > 0) {
      $_SESSION['errprompt'] = "Appointment with the selected date, time, and doctor already exists.";
      //   header("location:new-appointment.php");
      header("location:new-appointment.php?did=" . urlencode($doctorID) . "&specialization=" . urlencode($id_specialization) . "&appDate=" . urlencode($appDate));
      exit;
    }

    $insertQuery = "INSERT INTO appointment (patientId, appDate, appTime, doctorID, dependentID) VALUES (?, ?, ?, ?, ?)";
    $insertStmt = mysqli_prepare($con, $insertQuery);
    mysqli_stmt_bind_param($insertStmt, "sssss", $patientID, $appDate, $appTime, $doctorID, $dependentID);

    if (mysqli_stmt_execute($insertStmt)) {
      $_SESSION['prompt'] = "New appointment added successfully.";
      header("location:appointment.php");
      exit;
    } else {
      $_SESSION['errprompt'] = "Error adding a new appointment: " . mysqli_error($con);
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

          ?>
          <div class="row mt-3">
            <div class="col-lg-4">
              <div class="card">
                <div class="card-body">
                  <div class="card-title">Booking Appointment</div>
                  <hr>
                  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" onsubmit="return validateForm();">
                    <input type="hidden" name="patientID" value="<?php echo $_SESSION['id'] ?>">
                    <input type="hidden" name="doctorID" value="<?php echo $did ?>">

                    <div class="form-group">
                      <label for="input-1">Doctor</label>
                      <select class="form-control" name="doctorID" id="doctorID" required onchange="refreshPage()">
                        <option value="" selected disabled>Select Doctor</option>

                        <?php
                        $query = "SELECT * FROM doctor";
                        $result = mysqli_query($con, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                          $id = $row['id'];
                          $doctorName = $row['doctorName'];
                          $specialization = $row['specialization'];
                          $selected = (isset($_GET['did']) && $_GET['did'] == $id) ? 'selected' : '';

                          // Add data-specialization attribute to store specialization
                          echo '<option value="' . $id . '" data-specialization="' . $specialization . '" ' . $selected . '>' . $doctorName . '</option>';
                        }
                        ?>
                      </select>
                    </div>

                    <div class="form-group">
                      <?php
                      $selectedDoctorID = isset($_GET['did']) ? $_GET['did'] : '';
                      $selectedSpecialization = '';
                      $name_specialization = ''; // Initialize the variable

                      if ($selectedDoctorID) {
                        $checkQuery = "SELECT * FROM doctor WHERE id = '$selectedDoctorID'";
                        $checkResult = mysqli_query($con, $checkQuery);

                        if ($checkResult) {
                          $rowDoctor = mysqli_fetch_assoc($checkResult);
                          $selectedSpecialization = $rowDoctor['specialization'];

                          $checkQuery = "SELECT * FROM specialization WHERE id_specialization = '$selectedSpecialization'";
                          $checkResult = mysqli_query($con, $checkQuery);

                          if ($checkResult) {
                            $rowSpecialization = mysqli_fetch_assoc($checkResult);
                            $name_specialization = $rowSpecialization['name_specialization']; // Assign the value
                          }
                        }
                      }
                      ?>
                      <label for="input-1">Specialization</label>
                      <input type="text" class="form-control" id="specialization" name="specialization" value="<?php echo $name_specialization; ?>" disabled />
                      <input type="hidden" name="id_specialization" value="<?php echo $rowDoctor['specialization']; ?>" disabled />
                    </div>

                    <?php
                    // Calculate the current date
                    $currentDate = date('Y-m-d');
                    $appTime = isset($appTime) ? $appTime : '';
                    $appDate = (isset($_GET['appDate']) && $_GET['appDate'] !== null) ? $_GET['appDate'] : date('Y-m-d');
                    ?>

                    <?php if (!empty($did)) : ?>

                      <div class="form-group" id="appointmentDateContainer">
                        <label for="input-1">Appointment Date</label>
                        <input type="date" name="appDate" id="appDate" class="form-control" value="<?php echo $appDate; ?>" min="<?php echo $currentDate; ?>" required onchange="refreshPageDate()">
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
                        <label for="input-1">Dependent</label>
                        <select class="form-control" id="dependentSelector" required>
                          <option value="" selected disabled>Select Dependent</option>
                          <option value="yes">Yes</option>
                          <option value="no">No</option>
                        </select>
                      </div>

                      <div class="form-group" id="dependentListContainer">
                        <label for="input-1">Dependent List</label>
                        <select class="form-control" name="dependentID">
                          <option value="" selected disabled>Choose Dependent</option>
                          <?php
                          $query = "SELECT * FROM dependent WHERE patientId='" . $_SESSION['id'] . "'";
                          $result = mysqli_query($con, $query);
                          while ($row = mysqli_fetch_assoc($result)) {
                            $id_dependent = $row['id_dependent'];
                            $name_dependent = $row['name_dependent'];
                            echo '<option value="' . $id_dependent . '">' . $name_dependent . '</option>';
                          }
                          ?>
                        </select>
                      </div>
                    <?php endif; ?>





                    <div class="form-group">
                      <a href="appointment.php" class="btn btn-secondary px-3">Cancel</a>
                      <input type="submit" class="btn btn-primary px-4" name="save" value="Book" onclick="return confirmBooking()">
                    </div>
                  </form>
                </div>
              </div>
            </div>

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
        // Function to be executed when the page loads
        $(document).ready(function() {
          // Initially hide the dependent list
          $("#dependentListContainer").hide();

          // Attach change event to the "Dependent" dropdown
          $("#dependentSelector").change(function() {
            // Check the selected value
            var selectedValue = $(this).val();

            // Show/hide dependent list based on the selected value
            if (selectedValue === "yes") {
              $("#dependentListContainer").show();
            } else {
              $("#dependentListContainer").hide();
              // Reset the selected value in the dependent list when "No" is selected
              $("#dependentListContainer select").val("");
            }
          });

          // Get the selected doctor element
          var selectedDoctor = $('#doctorID');

          // Get the date input element
          var appDateInput = $('#appDate');

          // Disable the date input if no doctor is initially selected
          appDateInput.prop('disabled', selectedDoctor.val() === '');

          // Handle dependent list visibility on page load
          var selectedDependentValue = $("#dependentSelector").val();
          if (selectedDependentValue === "yes") {
            $("#dependentListContainer").show();
          }
        });

        function refreshPage() {
          // Get the selected doctor's specialization
          var selectedDoctor = $('#doctorID');
          var selectedSpecialization = selectedDoctor.find(':selected').data('specialization');

          // Set the selected specialization to the disabled input field
          $('#specialization').val(selectedSpecialization);

          // Get the date input element
          var appDateInput = $('#appDate');

          // Toggle the disabled state of the date input based on whether a doctor is selected
          appDateInput.prop('disabled', selectedDoctor.val() === '');

          // Redirect to the same page with the selected doctor's ID and specialization as query parameters
          window.location.href = 'new-appointment.php?did=' + selectedDoctor.val() + '&specialization=' + selectedSpecialization;
        }

        function refreshPageDate() {
          // Get the selected date
          var selectedDoctor = $('#doctorID');
          var selectedSpecialization = selectedDoctor.find(':selected').data('specialization');
          var selectedDate = $('#appDate').val();

          // Redirect to the same page with the selected date as a query parameter
          window.location.href = 'new-appointment.php?did=' + selectedDoctor.val() + '&specialization=' + selectedSpecialization + '&appDate=' + selectedDate;
        }

        function validateForm() {
          // Get the selected value of the "Dependent" dropdown
          var dependentValue = $("#dependentSelector").val();

          // Check if dependent is set to "Yes"
          if (dependentValue === "yes") {
            // Get the selected value of the dependent list
            var dependentListValue = $("#dependentListContainer select").val();

            // Check if the dependent list is not selected
            if (!dependentListValue) {
              alert("Please select a dependent from the list.");
              return false; // Prevent form submission
            }
          }

          // Get all radio buttons with the name 'appTime'
          var checkboxes = $("input[name='appTime']");
          var isChecked = false;

          // Check if at least one checkbox is checked
          checkboxes.each(function() {
            if ($(this).prop('checked')) {
              isChecked = true;
              return false; // Exit the loop early if a checkbox is checked
            }
          });

          // Display an alert if no checkbox is checked
          if (!isChecked) {
            alert("Please select a time slot.");
            return false; // Prevent form submission
          }

          return true; // Continue with form submission
        }
      </script>

      <script>
        function confirmBooking() {
          // Display a confirmation dialog
          var confirmed = confirm("Are you sure you want to book this appointment?");

          // Return true if the user clicks OK, otherwise, return false
          return confirmed;
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