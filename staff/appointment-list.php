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
			$subject = "Appointment Approved";
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

		header("location: appointment-list.php");
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

		header("location: appointment-list.php");
		exit;
	}
?>

	<!DOCTYPE html>
	<html>
	<?php include "head.php"; ?>

	<body class="bg-theme bg-theme9">
		<div id="wrapper">
			<?php
			include "sidebar.php";
			include "header.php";
			?>

			<div class="content-wrapper">
				<div class="container-fluid">

					<!-- Appointments -->
					<div class="card mt-3">
						<div class="card-body">
							<div class="card-title">Appointments</div>

							<!-- Alerts -->
							<?php
							if (isset($_SESSION['errprompt'])) {
								showError();
							} elseif (isset($_SESSION['prompt'])) {
								showPrompt();
							}
							?>

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
								<table id="appTable" class="table table-bordered table-hover table-sm">
									<thead class="bg-light">
										<tr>
											<th scope="col">ID</th>
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
										$sql = "SELECT a.*, b.patientName,b.patientEmail,c.doctorName,d.name_dependent 
												FROM appointment a 
												JOIN patient b ON a.patientID = b.id 
												JOIN doctor c ON a.doctorID = c.id 
												LEFT JOIN dependent d ON a.dependentID = d.id_dependent
												ORDER BY a.appDate DESC, a.appTime";

										// No errors with SQL
										if ($result = mysqli_query($con, $sql)) {
											$currentDate = null;

											// Get current timestamp
											$datenow = date("Y-m-d");
											$timenow = date('H:i:s');

											// Has appointment
											if (mysqli_num_rows($result) > 0) {
												while ($row = mysqli_fetch_assoc($result)) {
													extract($row);

													// Format ID
													$formattedAppId = sprintf('A%03d', $appId);

													// Format time
													$formattedTime = date('h:i A', strtotime($appTime));

													// Format day
													$formattedDate = date('j F Y', strtotime($appDate)) . "<br><b>" . date('l', strtotime($appDate)) . "</b>";
													$formattedDateEmail = date('j F Y', strtotime($appDate)) . " - <b>" . date('l', strtotime($appDate)) . "</b>";

													// Render logic
													if ($status == "pending") { ?>
														<tr>
															<th scope='row' style="vertical-align: middle;"><?= $formattedAppId ?></th>
															<td style="vertical-align: middle;"><?= $appDate != $currentDate ? $formattedDate : "" ?></td>
															<td style="vertical-align: middle;"><?= $formattedTime ?></td>
															<td style="vertical-align: middle;"><?= $doctorName ?></td>
															<td style="vertical-align: middle;"><?= $patientName ?></td>
															<td style="vertical-align: middle;"><?= ($name_dependent ? $name_dependent : "N/A") ?></td>
															<td style="width: 7.5rem; vertical-align: middle;"><span class="badge badge-warning" style="width: 6.5rem; line-height: inherit; padding: 9px 19px;">PENDING</span></td>
															<td style="vertical-align: middle;">N/A</td>
															<td style="vertical-align: middle;"><?php
																								if ($appDate >= $datenow && ($appDate > $datenow || $appTime >= $timenow)) {
																									// Approve Button
																									echo "<a href='appointment-list.php?app_id={$appId}&patientEmail={$patientEmail}&appDate={$formattedDateEmail}&appTime={$formattedTime}&doctorName={$doctorName}' class='btn btn-success mr-2' onclick='return confirm(`Are you sure you want to approve this appointment?`);'><i class='fa fa-check-circle'></i></a>";
																									// Update Button
																									echo "<a href='edit-appointment.php?id={$appId}&did={$doctorID}&appDate={$appDate}' class='btn btn-warning mr-2'><i class='fa fa-pencil'></i></a>";
																								}
																								?><a href="appointment-list.php?delete_id=<?= $appId ?>" class='btn btn-danger' onclick='return confirm("Are you sure you want to delete this appointment?");'><i class='fa fa-trash'></i></a>
															</td style="vertical-align: middle;">
														</tr><?php
															} elseif ($status == "approved") { ?>
														<tr>
															<th scope='row' style="vertical-align: middle;"><?= $formattedAppId ?></th>
															<td style="vertical-align: middle;"><?= $appDate != $currentDate ? $formattedDate : "" ?></td>
															<td style="vertical-align: middle;"><?= $formattedTime ?></td>
															<td style="vertical-align: middle;"><?= $doctorName ?></td>
															<td style="vertical-align: middle;"><?= $patientName ?></td>
															<td style="vertical-align: middle;"><?= ($name_dependent ? $name_dependent : "N/A") ?></td>
															<td style="width: 7.5rem; vertical-align: middle;"><span class="badge badge-success" style="width: 6.5rem; line-height: inherit; padding: 9px 19px;">APPROVED</span></td>
															<td style="vertical-align: middle;">N/A</td>
															<td style="vertical-align: middle;">N/A</td>
														</tr>
													<?php } else { ?>
														<tr>
															<th scope='row' style="vertical-align: middle;"><?= $formattedAppId ?></th>
															<td style="vertical-align: middle;"><?= $appDate != $currentDate ? $formattedDate : "" ?></td>
															<td style="vertical-align: middle;"><?= $formattedTime ?></td>
															<td style="vertical-align: middle;"><?= $doctorName ?></td>
															<td style="vertical-align: middle;"><?= $patientName ?></td>
															<td style="vertical-align: middle;"><?= ($name_dependent ? $name_dependent : "N/A") ?></td>
															<td style="width: 7.5rem; vertical-align: middle;"><span class="badge badge-primary" style="width: 6.5rem; line-height: inherit; padding: 9px 19px;">DONE</span></td>
															<td style="vertical-align: middle;">
																<?= $receipt ? "<a href='$receipt' target='_blank' class='btn btn-light'><i class='icon-eye'></i></a>" : "N/A" ?>
															</td>
															<td style="vertical-align: middle;"><a href='upload-receipt.php?id=<?= $appId ?>' class='btn btn-primary'><i class='fa fa-upload'></i></a></td>
														</tr>
										<?php }
														}
													}
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
			</div>
		</div>

		<!-- Sidebar Overlay -->
		<div class="overlay toggle-menu"></div>

		<!-- Back To Top Button -->
		<a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>

		<?php include "footer.php"; ?>

		<script>
			$(document).ready(function() {
				let table = $('#appTable').DataTable({
					"dom": 'rt',
					"order": [],
					columnDefs: [{
						orderable: false,
						targets: [7, 8]
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
					table.column(6).search(status).draw();
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