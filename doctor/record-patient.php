<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['doctorId'], $_SESSION['password'])) {
  $idapp = $_GET['id'];
  $queryCheck = "SELECT * FROM appointment a JOIN records b ON a.appId = b.appId WHERE a.appId = ?";

  // Use prepared statement
  if ($stmt = mysqli_prepare($con, $queryCheck)) {
    // Bind the parameter
    mysqli_stmt_bind_param($stmt, "i", $idapp);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Store the result
    $result = mysqli_stmt_get_result($stmt);

    // Check if there are rows
    if (mysqli_num_rows($result) > 0) {
      echo "<script type=\"text/javascript\">
            alert(\"This patient already has a remark/record for this date and time.\");
            window.location = \"patients.php\";
            </script>";
    } else {
      // Continue with your logic if the patient hasn't been recorded for this appointment
    }

    // Close the statement
    mysqli_stmt_close($stmt);
  } else {
    echo "Error in prepared statement: " . mysqli_error($con);
  }

  if (isset($_POST['save'])) {

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
      // Insert record
      $insertQuery = "INSERT INTO records (appID, diagnosis, clarification, clinical_progress, care_plan) VALUES (?, ?,?,?,?)";
      $insertStmt = mysqli_prepare($con, $insertQuery);
      mysqli_stmt_bind_param($insertStmt, "sssss", $id, $diagnosis, $clarification, $clinical_progress, $care_plan);
      mysqli_stmt_execute($insertStmt);

      // Update appointment status
      $updateQuery = "UPDATE appointment SET status = ? WHERE appId = ?";
      $updateStmt = mysqli_prepare($con, $updateQuery);
      mysqli_stmt_bind_param($updateStmt, "ss", $status, $id);
      mysqli_stmt_execute($updateStmt);

      // Commit the transaction
      mysqli_commit($con);

      $_SESSION['prompt'] = "Record patient inserted successfully.";
      header("location:view-record.php?id=" . $pid);
      exit;
    } catch (Exception $e) {
      // Rollback the transaction on error
      mysqli_rollback($con);

      $_SESSION['errprompt'] = "Error inserting patient record: " . $e->getMessage();
      header("location:record-patient.php?id=" . $id);
      exit;
    } finally {
      // Close the prepared statements
      mysqli_stmt_close($insertStmt);
      mysqli_stmt_close($updateStmt);

      // Close the database connection
      mysqli_close($con);
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

          $query = "SELECT a.*,b.patientName,b.patientPhone,b.id AS pid, c.name_dependent  FROM 
          appointment a JOIN patient b 
          LEFT JOIN dependent c ON a.dependentID = c.id_dependent
          WHERE a.appId=$idapp AND a.patientID=b.id";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);
            // Format the time in 12-hour format
            $formattedTime = date('h:i A', strtotime($appTime));

            // Format the date and display the day
            $formattedDate = date('d/m/Y', strtotime($appDate));
          ?>
            <div class="row mt-3">
              <div class="col-lg-4">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Add New Record </div>
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
                        <textarea name="diagnosis" class="form-control" required></textarea>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Clarification</label>
                        <textarea name="clarification" class="form-control"></textarea>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Clinical Progress</label>
                        <textarea name=" clinical_progress" class="form-control"></textarea>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Care Plan</label>
                        <textarea name="care_plan" class="form-control"></textarea>
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


            <div class="col-lg-8">
              <div class="card">
                <div class="card-body">
                  <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                    <li class="nav-item">
                      <a href="javascript:void();" data-target="#records" data-toggle="pill" class="nav-link active"><i class="fa fa-book"></i> <span class="hidden-xs"> <?php echo $patientName ?>'s Medical Record</span></a>
                    </li>
                  </ul>
                  <div class="tab-content p-3">
                    <div class="tab-pane active" id="records">
                      <div class="tab-pane active" id="profile">

                        <div class="row">
                          <div class="col-md-12">
                            <h5 class="mt-2 mb-3"><span class="fa fa-clock-o ion-clock float-right"></span> Records History</h5>
                            <div class="table-responsive">
                              <table class="table table-hover table-striped">
                                <thead>
                                  <tr>
                                    <th scope="col">Record's Details</th>
                                    <th scope="col">Appointment Details</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                  $count = 1;
                                  $query = "SELECT a.*, b.patientName, c.diagnosis,c.clarification,c.clinical_progress,c.care_plan,
                                  d.doctorName FROM appointment a 
                                JOIN patient b ON a.patientID = b.id 
                                JOIN records c ON a.appId=c.appId
                                JOIN doctor d ON a.doctorID=d.id
                                WHERE a.patientID='$pid'
                                ORDER BY c.id_record DESC";

                                  if ($result = mysqli_query($con, $query)) {
                                    $currentDate = null;

                                    while ($row = mysqli_fetch_assoc($result)) {
                                      extract($row);

                                      // Format the time in 12-hour format
                                      $formattedTime = date('h:i A', strtotime($appTime));

                                      // Format the date and display the day
                                      $formattedDate = date('d/m/Y', strtotime($appDate)) . " (" . date('l', strtotime($appDate)) . ")";
                                  ?>
                                      <tr>
                                        <td>
                                          <strong>
                                            Diagnosis: <?php echo ($diagnosis ? $diagnosis : "None") ?><br>
                                            Clarification: <?php echo ($clarification ? $clarification : "None") ?><br>
                                            Clinical Progress:<?php echo ($clinical_progress ? $clinical_progress : "None") ?><br>
                                            Care Plan: <?php echo ($care_plan ? $care_plan : "None") ?>
                                          </strong>
                                        </td>
                                        <td>
                                          Doctor : <?php echo $doctorName ?><br>
                                          Specialization : <?php echo $doctorName ?><br>
                                          Appointment Date : <?php echo $formattedDate ?><br>
                                          Time : <?php echo $formattedTime ?><br>

                                        </td>
                                      </tr>
                                </tbody>

                            <?php
                                    }
                                  } else {
                                    die("Error with the query in the database");
                                  }
                            ?>
                              </table>
                            </div>
                          </div>
                        </div>
                        <!--/row-->
                      </div>


                    </div>
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