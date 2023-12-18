<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['doctorId'], $_SESSION['password'])) {

  if (isset($_GET['app_id'])) {
    $app_id = clean($_GET['app_id']);
    $status = 'done';

    // Perform the update operation
    $query = "UPDATE appointment SET status='$status' WHERE appId = '$app_id'";

    if (mysqli_query($con, $query)) {
      $_SESSION['prompt'] = "Appointment status updated successfully.";
    } else {
      $_SESSION['errprompt'] = "Error updating appointment status: " . mysqli_error($con);
    }

    header("location: appointment.php");
    exit;
  }



  if (isset($_GET['delete_id'])) {
    $delete_id = clean($_GET['delete_id']);

    // Perform the delete operation
    $query = "DELETE FROM appointment WHERE appId = '$delete_id'";
    if (mysqli_query($con, $query)) {
      $_SESSION['prompt'] = "Appointment Slot deleted successfully.";
    } else {
      $_SESSION['errprompt'] = "Error deleting appointment.";
    }

    header("location: appointment.php");
    exit;
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


            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">List of My Appointment</h5>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Date</th>
                          <th scope="col">Time</th>
                          <th scope="col">Patient</th>
                          <th scope="col">Status</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $count = 1;
                        $query = "SELECT a.*, b.patientName FROM appointment a 
          JOIN patient b ON a.patientID = b.id 
          WHERE a.doctorID='" . $_SESSION['id'] . "' 
          ORDER BY a.appDate DESC, a.appTime";

                        if ($result = mysqli_query($con, $query)) {
                          $currentDate = null;

                          while ($row = mysqli_fetch_assoc($result)) {
                            extract($row);

                            // Format the time in 12-hour format
                            $formattedTime = date('h:i A', strtotime($appTime));

                            // Format the date and display the day
                            $formattedDate = date('j F Y', strtotime($appDate)) . "<br><b>" . date('l', strtotime($appDate)) . "</b>";

                            // View button (always defined)
                            $viewButton = "<a href='view-patient.php?id={$patientId}' class='btn btn-dark'><i class='icon-eye'></i> View</a> ";

                            if ($status == 'pending') {
                              $statusBadge = '<span class="badge badge-primary"><i class="fa fa-spinner"></i> PENDING</span>';
                              $approveButton = "<a href='appointment.php?app_id={$appId}' class='btn btn-success' onclick='return confirmApprove();'><i class='icon-check'></i> Approve</a> ";
                              $editButton = "<a href='edit-appointment.php?id={$appId}' class='btn btn-warning'><i class='icon-pencil'></i> Edit</a> ";
                              $deleteButton = "<a href='appointment.php?delete_id={$appId}' class='btn btn-danger' onclick='return confirmDelete();'><i class='icon-trash'></i> Delete</a>";
                            } else {
                              $statusBadge = '<span class="badge badge-success"><i class="fa fa-check"></i> DONE</span>';
                              // If status is 'DONE', set $approveButton, $editButton, and $deleteButton to an empty string
                              $approveButton = $editButton = $deleteButton = '';
                            }

                            // Check if the current date is different from the previous date
                            if ($appDate != $currentDate) {
                              // Display a new row for the date
                              echo "<tr>";
                              echo "<th scope='row'>" . $count . "</th>";
                              echo "<td>" . $formattedDate . "</td>";
                              echo "<td>" . $formattedTime . "</td>";
                              echo "<td>" . $patientName . "</td>";
                              echo "<td>" . $statusBadge . "</td>";
                              echo "<td>";
                              echo $viewButton;
                              echo $approveButton;
                              echo $editButton;
                              echo $deleteButton;
                              echo "</td>";
                              echo "</tr>";

                              $count++;
                              $currentDate = $appDate;
                            } else {
                              // Display additional time slots for the same date in the same row
                              echo "<tr>";
                              echo "<th scope='row'></th>";
                              echo "<td></td>";
                              echo "<td>" . $formattedTime . "</td>";
                              echo "<td>" . $patientName . "</td>";
                              echo "<td>" . $statusBadge . "</td>";
                              echo "<td>";
                              echo $viewButton;
                              echo $approveButton;
                              echo $editButton;
                              echo $deleteButton;
                              echo "</td>";
                              echo "</tr>";
                            }
                          }
                        } else {
                          die("Error with the query in the database");
                        }
                        ?>



                      </tbody>
                    </table>
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


      <script>
        function confirmDelete() {
          return confirm("Are you sure you want to delete this appointment?");
        }

        function confirmApprove() {
          return confirm("Are you sure you want to approve this appointment?");
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