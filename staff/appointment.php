<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

// Import PHPMailer into global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../vendor/autoload.php';

if (isset($_SESSION['staffId'], $_SESSION['password'])) {
  $filterStatus = isset($_GET['filter']) && $_GET['filter'] == 'pending';

  if (isset($_GET['app_id']) && isset($_GET['patientEmail']) && isset($_GET['appDate']) && isset($_GET['appTime'])  && isset($_GET['doctorName'])) {
    $app_id = clean($_GET['app_id']);
    $patientEmail = clean($_GET['patientEmail']);
    $appDate = clean($_GET['appDate']);
    $appTime = clean($_GET['appTime']);
    $doctorName = clean($_GET['doctorName']);
    $status = 'approved';

    // Perform the update operation
    $query = "UPDATE appointment SET status='$status' WHERE appId = '$app_id'";



    if (mysqli_query($con, $query)) {
      $_SESSION['prompt'] = "Appointment has been approved and email already sent to $patientEmail.";

      // Send password reset email
      $subject = "APPOINTMENT APPROVED";
      $message = "Your appointment has been approved: <br>Date: " . $appDate . "<br>Time: " . $appTime . "<br>Doctor: " . $doctorName;

      $mail = new PHPMailer(true);

      try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'healthboook@gmail.com';
        $mail->Password = 'kslxejryqhxdcxsk';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('healthboook@gmail.com', 'HealthBook');
        $mail->addAddress($patientEmail);
        $mail->addReplyTo('healthboook@gmail.com');

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();

        $_SESSION['result'] = 'Message has been sent';
        $_SESSION['status'] = 'ok';
      } catch (Exception $e) {
        $_SESSION['result'] = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        $_SESSION['status'] = 'error';
      }
    } else {
      $_SESSION['errprompt'] = "Error updating appointment status: " . mysqli_error($con);
    }


    // Redirect to the appropriate page
    $redirectUrl = $filterStatus ? "appointment.php?filter=pending" : "appointment.php";
    header("Location: $redirectUrl");
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

    // Redirect to the appropriate page
    $redirectUrl = $filterStatus ? "appointment.php?filter=pending" : "appointment.php";
    header("Location: $redirectUrl");
    exit;
  }
?>
  <!DOCTYPE html>
  <html lang="en">
  <?php include "head.php"; ?>
  <style>
  .action-button {
      margin-right: 4px;
  }
  </style>

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
                  <h5 class="card-title">List of Appointments</h5>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Date</th>
                          <th scope="col">Time</th>
                          <th scope="col">Doctor</th>
                          <th scope="col">Patient</th>
                          <th scope="col">Dependent</th>
                          <th scope="col">Status</th>
                          <th scope="col">Receipt</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $count = 1;
                        $filterStatus = isset($_GET['filter']) && $_GET['filter'] == 'pending';
                        $currentDateTime = new DateTime();
                        $currentDate = $currentDateTime->format("Y-m-d");
                        $currentTime = $currentDateTime->format("H:i:s");

                        if ($filterStatus) {
                            $query = "SELECT a.*, b.patientName, b.patientEmail, c.doctorName, d.name_dependent 
                                      FROM appointment a 
                                      JOIN patient b ON a.patientID = b.id 
                                      JOIN doctor c ON a.doctorID = c.id 
                                      LEFT JOIN dependent d ON a.dependentID = d.id_dependent
                                      WHERE a.status = 'pending' 
                                      AND (a.appDate > '$currentDate' OR (a.appDate = '$currentDate' AND a.appTime > '$currentTime'))
                                      ORDER BY a.appDate ASC, a.appTime ASC";
                        } else {
                            // Keep your existing query here for the non-filtered view
                            $query = "SELECT a.*, b.patientName, b.patientEmail, c.doctorName, d.name_dependent 
                                      FROM appointment a 
                                      JOIN patient b ON a.patientID = b.id 
                                      JOIN doctor c ON a.doctorID = c.id 
                                      LEFT JOIN dependent d ON a.dependentID = d.id_dependent
                                      ORDER BY a.appDate DESC, a.appTime";
                        }

                        $result = mysqli_query($con, $query);

                        if ($result) {
                          $currentDate = null;

                          if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                              extract($row);


                              $datenow = date("Y-m-d");
                              $currentime = date('H:i:s');


                              // Format the time in 12-hour format
                              $formattedTime = date('h:i A', strtotime($appTime));

                              // Format the date and display the day
                              $formattedDate = date('j F Y', strtotime($appDate)) . "<br><b>" . date('l', strtotime($appDate)) . "</b>";
                              $formattedDateEmail = date('j F Y', strtotime($appDate)) . " - <b>" . date('l', strtotime($appDate)) . "</b>";



                              if ($status == 'pending') {
                                $statusBadge = '<span class="badge badge-primary"><i class="fa fa-spinner"></i> PENDING</span>';
                                $approveButton = "<a href='appointment.php?app_id={$appId}&patientEmail={$patientEmail}&appDate={$formattedDateEmail}&appTime={$formattedTime}&doctorName={$doctorName}&filter=" . ($filterStatus ? "pending" : "all") . "' class='btn btn-success' onclick='return confirmApprove();'><i class='icon-check'></i> Approve</a> ";
                                $editButton = "<a href='edit-appointment.php?id={$appId}&appDate={$appDate}&did={$doctorID}&filter=" . ($filterStatus ? "pending" : "all") . "' class='btn btn-warning action-button'><i class='icon-pencil'></i> </a>";
                                $deleteButton = "<a href='appointment.php?delete_id={$appId}&filter=" . ($filterStatus ? "pending" : "all") . "' class='btn btn-danger action-button' onclick='return confirmDelete();'><i class='icon-trash'></i> </a>";
                                $receiptButton = '';
                              } elseif ($status == 'approved') {
                                $statusBadge = '<span class="badge badge-dark"><i class="fa fa-check"></i> APPROVED</span>';
                                $receiptButton = $recordButton = $approveButton = $editButton = $deleteButton = '';
                              } else {
                                $statusBadge = '<span class="badge badge-success"><i class="fa fa-check"></i> DONE</span>';
                                $receiptButton = $approveButton = $editButton = $deleteButton = '';
                                $receiptButton = "<a href='upload-receipt.php?id={$appId}' class='btn btn-primary'><i class='fa fa-dollar'></i> Upload Receipt</a> ";
                              }

                              // Check if the current date is different from the previous date
                              if ($appDate != $currentDate) {
                                // Display a new row for the date
                                echo "<tr>";
                                echo "<th scope='row'>" . $count . "</th>";
                                echo "<td>" . $formattedDate . "</td>";
                                echo "<td>" . $formattedTime . "</td>";
                                echo "<td>" . $doctorName . "</td>";
                                echo "<td>" . $patientName . "</td>";
                                echo "<td>" . ($name_dependent ? $name_dependent : "No Dependent") . "</td>";
                                echo "<td>" . $statusBadge . "</td>";
                                echo "<td>" . ($receipt ? "<a href='{$receipt}' target='_blank' class='btn btn-light'><i class='icon-eye'></i> View Receipt </a>" : "not upload yet") . "</td>";
                                echo "<td>";
                                echo $receiptButton;
                                if ($appDate >= $datenow && ($appDate > $datenow || $appTime >= $currentime)) {
                                  echo $approveButton;

                                  echo $editButton;
                                }
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
                                echo "<td>" . $doctorName . "</td>";
                                echo "<td>" . $patientName . "</td>";
                                echo "<td>" . ($name_dependent ? $name_dependent : "No Dependent") . "</td>";
                                echo "<td>" . $statusBadge . "</td>";
                                echo "<td>" . ($receipt ? "<a href='{$receipt}' target='_blank' class='btn btn-light'><i class='icon-eye'></i> View Receipt </a>" : "not upload yet") . "</td>";
                                echo "<td>";
                                echo $receiptButton;
                                if ($appDate >= $datenow && ($appDate > $datenow || $appTime >= $currentime)) {
                                  echo $approveButton;

                                  echo $editButton;
                                }
                                echo $deleteButton;
                                echo "</td>";
                                echo "</tr>";
                              }
                            }
                          } else {
                            echo "<tr>";
                            echo "<td colspan='9' style='text-align: center; color: red;'>No records found</td>";
                            echo "</tr>";
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