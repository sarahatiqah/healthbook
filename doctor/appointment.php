<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

// Import PHPMailer into global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../vendor/autoload.php';

if (isset($_SESSION['doctorId'], $_SESSION['password'])) {

  // Add logic to handle the "pending" filter
  $filterStatus = isset($_GET['filter']) && $_GET['filter'] == 'pending';
  
  // Get current date and time for filtering
  $currentDateTime = new DateTime();
  $currentDate = $currentDateTime->format("Y-m-d");
  $currentTime = $currentDateTime->format("H:i:s");


  if (isset($_GET['app_id']) && isset($_GET['patientEmail']) && isset($_GET['appDate']) && isset($_GET['appTime']) && isset($_GET['doctorName'])) {
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
                  <h5 class="card-title">Appointments</h5>

                  <div class="d-flex flex-row justify-content-between mb-2 mt-4">
                    <!-- Selection -->
                    <div class="d-flex flex-row align-items-center">
                      Show
                      <select class="custom-select custom-select-sm shadow-none mx-2" style="appearance: auto;" id="pageLength">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                      </select>
                      entries
                    </div>

                    <div class="d-flex flex-row align-items-center" style="width: 10rem;">
                      <label for="filter" class="mb-0 mr-2">Status</label>
                      <select class="custom-select custom-select-sm shadow-none" style="appearance: auto;" id="filter" name="filter">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="done">Done</option>
                      </select>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="appTable">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Date</th>
                          <th scope="col">Time</th>
                          <th scope="col">Patient</th>
                          <th scope="col">Status</th>
                          <th scope="col">Dependent</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $count = 1;

                        $baseQuery = "SELECT a.*, b.patientName, b.patientEmail, c.name_dependent FROM appointment a 
                        JOIN patient b ON a.patientID = b.id 
                        LEFT JOIN dependent c ON a.dependentID = c.id_dependent
                        WHERE a.doctorID='$did'";

                        if ($filterStatus) {
                          $baseQuery .= " AND a.status='pending' AND (a.appDate > '$currentDate' OR (a.appDate = '$currentDate' AND a.appTime > '$currentTime'))";
                          $baseQuery .= " ORDER BY a.appDate ASC, a.appTime"; // Ascending order for filtered results
                        } else {
                          $baseQuery .= " ORDER BY a.appDate DESC, a.appTime"; // Descending order for all results
                        }
                        
                        // Execute the query
                        $result = mysqli_query($con, $baseQuery);

                        if ($result) {
                          $currentDate = null;

                          if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                              extract($row);
                              $datenow = date("Y-m-d");
                              $currentime = date('H:i:s');

                              // Format IDs
                              $formattedPatientId = sprintf('P%03d', $patientId);
                              $formattedAppId = sprintf('A%03d', $appId);

                              // Format the time in 12-hour format
                              $formattedTime = date('h:i A', strtotime($appTime));

                              // Format the date and display the day
                              $formattedDate = date('j F Y', strtotime($appDate)) . "<br><b>" . date('l', strtotime($appDate)) . "</b>";
                              $formattedDateEmail = date('j F Y', strtotime($appDate)) . " - <b>" . date('l', strtotime($appDate)) . "</b>";

                              // View button (always defined)
                              $viewButton = "<a href='view-patient.php?id={$patientId}' class='btn btn-light'><i class='icon-eye'></i> </a> ";

                              if ($status == 'pending') {
                                $statusBadge = '<span class="badge badge-warning" style="width: 6.5rem; line-height: inherit; padding: 9px 19px;">PENDING</span>';
                                $approveButton = "<a href='appointment.php?app_id={$appId}&patientEmail={$patientEmail}&appDate={$formattedDateEmail}&appTime={$formattedTime}&doctorName={$doctorName}" . ($filterStatus ? "&filter=pending" : "") . "' class='btn btn-success' onclick='return confirmApprove();'><i class='fa fa-check-circle'></i></a> ";
                                $editButton = "<a href='edit-appointment.php?id={$appId}&appDate={$appDate}&returnUrl=" . urlencode(($filterStatus ? "appointment.php?filter=pending" : "appointment.php")) . "' class='btn btn-warning action-button'><i class='icon-pencil'></i> </a>";
                                $deleteButton = "<a href='appointment.php?delete_id={$appId}" . ($filterStatus ? "&filter=pending" : "") . "' class='btn btn-danger action-button' onclick='return confirmDelete();'><i class='icon-trash'></i> </a>";
                                $rebookButton =  $recordButton = '';
                              } elseif ($status == 'approved') {
                                $statusBadge = '<span class="badge badge-success" style="width: 6.5rem; line-height: inherit; padding: 9px 19px;">APPROVED</span>';
                                $rebookButton = $recordButton = $approveButton = $editButton = $deleteButton = '';
                                $recordButton = "<a href='create-record.php?patientId=$formattedPatientId&appointmentId=$formattedAppId' class='btn btn-secondary'><i class='fa fa-user-md'></i> Add Record</a> ";
                              } else {
                                $statusBadge = '<span class="badge badge-primary" style="width: 6.5rem; line-height: inherit; padding: 9px 19px;">DONE</span>';
                                $rebookButton = "<a href='rebook-appointment.php?id={$appId}&did={$doctorID}' class='btn btn-primary'><i class='zmdi zmdi-archive'></i> Rebook </a> ";
                                $recordButton = $approveButton = $editButton = $deleteButton = '';
                              }

                              // Display a new row for the date
                              echo "<tr>";
                              echo "<th scope='row' class='align-middle'>" . $count . "</th>";
                              echo "<td class='align-middle'>" . $formattedDate . "</td>";
                              echo "<td class='align-middle'>" . $formattedTime . "</td>";
                              echo "<td class='align-middle'>" . $patientName . "</td>";
                              echo "<td class='align-middle' style='width: 7.5rem;'>" . $statusBadge . "</td>";
                              echo "<td class='align-middle'>" . ($name_dependent ? $name_dependent : "No Dependent") . "</td>";
                              echo "<td class='align-middle'>";
                              echo $viewButton;
                              echo $rebookButton;
                              echo $recordButton;
                              if ($appDate >= $datenow && ($appDate > $datenow || $appTime >= $currentime)) {
                                echo $approveButton;

                                echo $editButton;
                              }
                              echo $deleteButton;
                              echo "</td>";
                              echo "</tr>";

                              $count++;
                              $currentDate = $appDate;
                            }
                          } else {
                            echo "<tr>";
                            echo "<td colspan='7' style='text-align: center; color: red;'>No records found</td>";
                            echo "</tr>";
                          }
                        } else {
                          die("Error with the query in the database");
                        }
                        ?>
                      </tbody>
                    </table>

                  </div>
                  <!-- Pagination -->
                  <nav aria-label="Page navigation" class="mt-4 d-flex flex-row justify-content-between align-items-center">
                    <!-- Information -->
                    <span>
                      Showing <span id="numRecords">0</span>
                      to <span id="totalRecords">0</span>
                      of <span id="allRecords">0</span> results
                    </span>

                    <!-- Navigation -->
                    <ul class="pagination mb-0">
                      <li class="page-item">
                        <button class="page-link" aria-label="Previous" id='previousButton'>
                          <span aria-hidden="true">&laquo;</span>
                        </button>
                      </li>

                      <li class="page-item">
                        <button class="page-link" aria-label="Next" id="nextButton">
                          <span aria-hidden="true">&raquo;</span>
                        </button>
                      </li>
                    </ul>
                  </nav>
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

      <script>
        $(document).ready(function() {
          let table = $('#appTable').DataTable({
            "dom": 'rt',
            "order": [],
            columnDefs: [{
              orderable: false,
              targets: [5, 6]
            }],
          });
          let info = table.page.info();

          // Update information on page change
          table.on('draw', function() {
            var pageInfo = table.page.info();
            $('#numRecords').text(pageInfo.start + 1);
            $('#totalRecords').text(pageInfo.end);
            $('#allRecords').text(pageInfo.recordsDisplay);
            $('#previousButton').prop('disabled', pageInfo.page === 0);
            $('#nextButton').prop('disabled', pageInfo.page === pageInfo.pages - 1);
          });

          $('#nextButton').on('click', function() {
            table.page('next').draw('page');
          });

          $('#previousButton').on('click', function() {
            table.page('previous').draw('page');
          });

          $('#filter').on('change', function() {
            let status = $(this).val();
            table.column(4).search(status).draw();
          });

          $('#pageLength').on('change', function() {
            var pageLength = $(this).val();
            table.page.len(pageLength).draw();
          });

          table.draw();
        });
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