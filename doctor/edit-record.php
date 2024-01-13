<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['doctorId'], $_SESSION['password'])) {
  $idapp = $_GET['id'];

  if (isset($_POST['update'])) {

    // Assuming you have a clean function for input sanitation
    $id = clean($_POST['id']);
    $diagnosis = clean($_POST['diagnosis']);
    $clarification = clean($_POST['clarification']);
    $clinical_progress = clean($_POST['clinical_progress']);
    $care_plan = clean($_POST['care_plan']);
    $pid = clean($_POST['patientID']);
    $status = 'done';

    // Start a transaction
    mysqli_begin_transaction($con);

    try {
      // Update appointment status
      $updateQuery = "UPDATE records SET diagnosis = ?,clarification = ?,clinical_progress = ?,care_plan = ? WHERE appId = ?";
      $updateStmt = mysqli_prepare($con, $updateQuery);
      mysqli_stmt_bind_param($updateStmt, "sssss", $diagnosis, $clarification, $clinical_progress, $care_plan, $id);
      mysqli_stmt_execute($updateStmt);

      // Commit the transaction
      mysqli_commit($con);

      $_SESSION['prompt'] = "Record patient update successfully.";
      header("location:view-record.php?id=" . $pid);
      exit;
    } catch (Exception $e) {
      // Rollback the transaction on error
      mysqli_rollback($con);

      $_SESSION['errprompt'] = "Error update patient record: " . $e->getMessage();
      header("location:remark-patient.php?id=" . $id);
      exit;
    } finally {
      // Close the prepared statements
      mysqli_stmt_close($updateStmt);

      // Close the database connection
      mysqli_close($con);
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

          $query = "SELECT a.*,b.patientName,b.patientPhone,b.id AS pid, c.name_dependent,
          d.diagnosis,d.clarification,d.clinical_progress,d.care_plan
            FROM appointment a 
          JOIN patient b 
          LEFT JOIN dependent c ON a.dependentID = c.id_dependent
          JOIN records d
          WHERE a.appId=$idapp AND a.patientID=b.id AND d.appId=a.appId";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);
            // Format the time in 12-hour format
            $formattedTime = date('h:i A', strtotime($appTime));

            // Format the date and display the day
            $formattedDate = date('d/m/Y', strtotime($appDate));
          ?>
            <div class="row mt-3">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Edit Record </div>
                    <hr>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id" value="<?php echo $idapp ?>">
                      <input type="hidden" name="patientID" value="<?php echo $pid ?>">
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
                        <input type="text" name="appDate" disabled class="form-control" value="<?php echo $formattedDate; ?>">
                      </div>

                      <div class="form-group">
                        <label for="input-1">Time Slot</label>
                        <input type="text" name="appDate" disabled class="form-control" value="<?php echo $formattedTime; ?>">
                      </div>

                      <div class="form-group">
                        <label for="input-1">Diagnosis</label>
                        <textarea name="diagnosis" class="form-control" value="<?php echo $diagnosis; ?>" required><?php echo $diagnosis; ?></textarea>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Clarification</label>
                        <textarea name="clarification" class="form-control" value="<?php echo $clarification; ?>"><?php echo $clarification; ?></textarea>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Clinical Progress</label>
                        <textarea name=" clinical_progress" class="form-control" value="<?php echo $clinical_progress; ?>"><?php echo $clinical_progress; ?></textarea>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Care Plan</label>
                        <textarea name="care_plan" class="form-control" value="<?php echo $care_plan; ?>"><?php echo $care_plan; ?></textarea>
                      </div>

                      <div class="form-group">
                        <a href="appointment.php" class="btn btn-secondary px-3">Cancel</a>
                        <input type="submit" class="btn btn-primary px-4" name="update" value="Update">
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