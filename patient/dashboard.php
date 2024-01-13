<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

// Get appointments
$currentDateTime = new DateTime();
$currentDate = $currentDateTime->format("Y-m-d");
$currentTime = $currentDateTime->format("H:i:s");
$query = "SELECT COUNT(*) as total 
			FROM appointment 
			WHERE status='approved' 
			AND (appDate > '$currentDate' OR (appDate = '$currentDate' AND appTime > '$currentTime'))
			AND patientId='" . $_SESSION['id'] . "'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
$upcomingAppointmentCount = $row['total'];

// Get calendar data
$query = "SELECT a.appDate, a.appTime, b.doctorName, a.patientId
			FROM appointment a
			JOIN doctor b ON a.doctorID = b.id
			WHERE (a.status = 'approved' OR a.status = 'done') 
			AND a.patientId='" . $_SESSION['id'] . "'";
$result = mysqli_query($con, $query);
$events = array();
while ($row = mysqli_fetch_assoc($result)) {
	$formattedTime = date('h:i A', strtotime($row['appTime']));

	// Map database fields to FullCalendar properties
	$event = array(
		'title' =>   $formattedTime . ': ' . $row['doctorName'],
		'start' => $row['appDate'] . 'T' . $row['appTime'], // Combine date & time
		'end' => $row['appDate'] . 'T' . $row['appTime'], // Adjust based on needs
	);
	$events[] = $event;
}

if (isset($_SESSION['id'], $_SESSION['password'])) {
?>
	<!DOCTYPE html>
	<html lang="en">

	<?php include "head.php"; ?>

	<body class="bg-theme bg-theme9">
		<div id="wrapper">
			<!-- Shell -->
			<?php
			include "sidebar.php";
			include "header.php";
			?>

			<div class="content-wrapper">
				<div class="container-fluid">
					<?php
					if (isset($_SESSION['errprompt'])) {
						showError();
					} elseif (isset($_SESSION['prompt'])) {
						showPrompt();
					}
					?>

					<!-- Content -->
					<div class="card mt-3">
						<div class="card-body">
							<div id='calendar'></div>
						</div>
					</div>
				</div>
			</div>

			<!-- Notification Modal -->
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
							<p style="color: black;">You have <span style="color: red;"><?php echo $upcomingAppointmentCount ?></span> new upcoming appointment to be attend.
								<a href="appointment.php" style="color: blue;">Click here for more details</a>
							</p>
						</div>

						<!-- Modal Footer -->
						<div class="modal-footer">
							<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>

			<!-- Back To Top Button -->
			<a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>

			<!-- Sidebar Overlay -->
			<div class="overlay toggle-menu"></div>

			<?php include "footer.php"; ?>
		</div>

		<script>
			$(document).ready(function() {
				// Add a delay of 1000 millisecond (1 second) before showing the modal
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
					editable: true, // Allow events to be dragged & resized
					selectable: true, // Allow the user to select the dates
					events: <?php echo json_encode($events); ?>,
					eventRender: function(event, element) {
						// Customize the event rendering
						element.find('.fc-time').css('display', 'none'); // Hide time
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
unset($_SESSION['errprompt']);
mysqli_close($con);

?>