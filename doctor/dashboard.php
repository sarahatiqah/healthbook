<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['doctorId'], $_SESSION['password'])) {
?>
  <!DOCTYPE html>
  <html lang="en">

  <?php include "head.php"; ?>

  <body class="bg-theme bg-theme9">
    <div id="wrapper">

      <?php
      include "sidebar.php";
      include "header.php";

      $query = "SELECT COUNT(*) as total FROM appointment WHERE status='done' AND doctorID=$id";
      $result = mysqli_query($con, $query);
      $row = mysqli_fetch_assoc($result);
      $countAppointmentDone = $row['total'];

      $query2 = "SELECT COUNT(*) as total FROM appointment WHERE doctorID=$id";
      $result2 = mysqli_query($con, $query2);
      $row2 = mysqli_fetch_assoc($result2);
      $countAppointment = $row2['total'];

      $query3 = "SELECT COUNT(*) as total FROM appointment WHERE status='approved' AND doctorID=$id";
      $result3 = mysqli_query($con, $query3);
      $row3 = mysqli_fetch_assoc($result3);
      $countAppointmentApproved = $row3['total'];

      $query4 = "SELECT COUNT(*) as total FROM appointment WHERE status='pending' AND doctorID=$id";
      $result4 = mysqli_query($con, $query4);
      $row4 = mysqli_fetch_assoc($result4);
      $countAppointmentPending = $row4['total'];

      $currentDateTime = new DateTime();
      $currentDate = $currentDateTime->format("Y-m-d");
      $currentTime = $currentDateTime->format("H:i:s");
      $query5 = "SELECT COUNT(*) as total FROM appointment WHERE status='pending' AND (appDate > '$currentDate' OR (appDate = '$currentDate' AND appTime > '$currentTime')) AND doctorID=$id";
      $result5 = mysqli_query($con, $query5);
      $row5 = mysqli_fetch_assoc($result5);
      $countAppointmentPendingUpcoming = $row5['total'];
      ?>



      <div class="clearfix"></div>

      <div class="content-wrapper">
        <div class="container-fluid">

          <!--Start Dashboard Content-->

          <div class="card mt-3">
            <div class="card-content">
              <div class="row row-group m-0">
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                  <div class="card-body">
                    <h5 class="text-white mb-0"><?php echo $countAppointment ?> <span class="float-right"><i class="zmdi zmdi-calendar"></i></span></h5>

                    <p class="mb-0 text-white small-font">Total Appointment</p>
                  </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                  <div class="card-body">
                    <h5 class="text-white mb-0"><?php echo $countAppointmentDone ?> <span class="float-right"><i class="zmdi zmdi-badge-check"></i></span></h5>

                    <p class="mb-0 text-white small-font">Total Done Appointment</p>
                  </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                  <div class="card-body">
                    <h5 class="text-white mb-0"><?php echo $countAppointmentApproved ?> <span class="float-right"><i class="zmdi zmdi-case-check"></i></span></h5>

                    <p class="mb-0 text-white small-font">Total Approved Appointment</p>
                  </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                  <div class="card-body">
                    <h5 class="text-white mb-0"><?php echo $countAppointmentPending ?> <span class="float-right"><i class="zmdi zmdi-spinner"></i></span></h5>

                    <p class="mb-0 text-white small-font">Total Pending Appointment</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card mt-3">
						<div class="card-body">
							<div id='calendar'></div>
						</div>
					</div>
          <!--End Dashboard Content-->

          <!--start overlay-->
          <div class="overlay toggle-menu"></div>
          <!--end overlay-->

        </div>
        <!-- End container-fluid-->

      </div><!--End content-wrapper-->


      <style>
        /* Add your animation class */
        .modal.fade .modal-dialog {
          /* transform: translate(0, -50%); */
          transition: transform 0.5s ease-out;
        }
      </style>
      <!-- The Modal -->
      <div class="modal fade" id="myModal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
          <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title" style="color: black;">Notifications</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
              <p style="color: black;">You have <span style="color: red;"><?php echo $countAppointmentPendingUpcoming ?></span> new upcoming appointment to be approved.
                <a href="appointment.php?filter=pending" style="color: blue;">Click here for more details</a>
              </p>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

          </div>
        </div>
      </div>


      <!--Start Back To Top Button-->
      <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
      <!--End Back To Top Button-->


      <?php include "footer.php";


      $query = "SELECT a.appDate, a.appTime, b.patientName
FROM appointment a 
JOIN patient b
WHERE (a.status='approved' OR a.status='done') AND a.doctorID=$did AND a.patientid=b.id";



      $result = mysqli_query($con, $query);

      $events = array();

      while ($row = mysqli_fetch_assoc($result)) {
        $formattedTime = date('h:i A', strtotime($row['appTime']));

        // Map your database fields to FullCalendar properties
        $event = array(
          'title' =>   $formattedTime . ': ' . $row['patientName'],
          'start' => $row['appDate'] . 'T' . $row['appTime'], // Combine date and time
          'end' => $row['appDate'] . 'T' . $row['appTime'], // You may adjust this based on your needs
        );

        $events[] = $event;
      }
      ?>

      <script>
        $(document).ready(function() {
          // Add a delay of 1000 milliseconds (1 second) before showing the modal
          setTimeout(function() {
            // Add the 'animated' class to the modal for the transition effect
            $('#myModal').addClass('animated').modal('show');
          }, 1000);

          // Initialize FullCalendar
          $('#calendar').fullCalendar({
            header: {
              left: 'prev,next today',
              center: 'title',
              right: ''
            },
            defaultDate: moment().format('YYYY-MM-DD'),
            navLinks: true,
            eventLimit: true,
            editable: true, // Allow events to be dragged and resized
            selectable: true, // Allow users to select dates
            events: <?php echo json_encode($events); ?>,
            eventRender: function(event, element) {
              // Customize the event rendering
              element.find('.fc-time').css('display', 'none'); // Hide the time
              element.find('.fc-title').html(event.title); // Set the title
            }
          });
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
mysqli_close($con);

?>