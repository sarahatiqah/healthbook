<?php
session_start();

require '../dbconnection.php';
require '../functions.php';

if (!empty($recordId = $_GET['recordId'])) {
	$query = "SELECT records.*, 
				patient.icPatient as patientIc, patient.patientName, patient.patientGender, patient.patientRace, 
				doctor.doctorName AS providerName, specialization.name_specialization AS providerSpecialization 
				FROM `records` 
				JOIN patient ON patient.id = records.patient_id
				JOIN doctor ON doctor.id = records.doctor_id
				JOIN specialization ON doctor.specialization = specialization.id_specialization
				WHERE CONCAT('R', LPAD(record_id, 3, '0')) = '$recordId'";

	if ($result = mysqli_query($con, $query)) {
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			extract($row);
		} else {
			$_SESSION['errprompt'] = "Record with ID = $recordId is not found.";
			header("location:records.php");
			exit;
		}
	} else {
		$_SESSION['errprompt'] = "An error had occured during query of database.";
		header("location:records.php");
		exit;
	}
} else {
	$_SESSION['errprompt'] = "Please specify a record ID.";
	header("location:records.php");
	exit;
}

if (isset($_POST['save'])) {
	// Input sanitation
	$content = clean($_POST['content']);

	// Start a transaction
	mysqli_begin_transaction($con);

	try {
		// Insert record
		$insertQuery = "INSERT INTO `addendums`(`recordId`, `doctorId`, `content`) VALUES (?,?,?)";
		$insertStmt = mysqli_prepare($con, $insertQuery);
		mysqli_stmt_bind_param($insertStmt, "sss", $record_id, $doctor_id, $content);
		mysqli_stmt_execute($insertStmt);

		// Commit the transaction
		mysqli_commit($con);

		$_SESSION['prompt'] = "Addendum saved successfully.";
	} catch (Exception $e) {
		// Rollback the transaction
		mysqli_rollback($con);
		$_SESSION['errprompt'] = "An error occured: " . $e->getMessage();
	} finally {
		// Close the prepared statement
		mysqli_stmt_close($insertStmt);
	}
}

if (isset($_SESSION['doctorId'], $_SESSION['password'])) {
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
					<!-- Patient and Doctor Details -->
					<div class="row mt-3 mx-0 flex-nowrap">
						<div class="card w-100 mr-4">
							<div class="card-body">
								<div class="card-title">Patient Details</div>

								<div class="form-group">
									<label for="patientId">Patient ID</label>
									<div class="input-group">
										<input type="text" disabled class="form-control" id="patientId" value="<?php echo sprintf('P%03d', $patient_id); ?>">
									</div>
								</div>

								<div class="table-responsive">
									<table class="table table-sm table-bordered">
										<tbody>
											<tr>
												<th scope="row" class="bg-light w-25">Name</th>
												<td class="align-middle"><?php echo $patientName; ?></td>
											</tr>
											<tr>
												<th scope="row" class="bg-light">IC No.</th>
												<td class="align-middle"><?php echo $patientIc; ?></td>
											</tr>
											<tr>
												<th scope="row" class="bg-light">Gender</th>
												<td class="align-middle"><?php echo $patientGender; ?></td>
											</tr>
											<tr>
												<th scope="row" class="bg-light">Race</th>
												<td class="align-middle"><?php echo $patientRace; ?></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="card w-100">
							<div class="card-body">
								<div class="card-title">Provider Details</div>

								<div class="form-group">
									<label for="doctorId">Provider ID</label>
									<div class="input-group">
										<input type="text" disabled class="form-control" id="doctorId" value="<?php echo sprintf('D%03d', $doctor_id); ?>">
									</div>
								</div>

								<div class="table-responsive">
									<table class="table table-sm table-bordered">
										<tbody>
											<tr>
												<th scope="row" class="bg-light w-25">Name</th>
												<td class="align-middle"><?php echo $providerName; ?></td>
											</tr>
											<tr>
												<th scope="row" class="bg-light">Specialization</th>
												<td class="align-middle"><?php echo $providerSpecialization; ?></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<!-- Record Details -->
					<div class="card">
						<div class="card-body">
							<div class="d-flex flex-row justify-content-between">
								<span class="card-title"><?php echo $record_type == "clerking_note" ? "Clerking Note" : "Progress Note"; ?></span>
								<span><label>Date Created:</label> <?php echo date('j F Y', strtotime($created_at)); ?></span>
							</div>

							<!-- Alerts -->
							<?php
							if (isset($_SESSION['prompt'])) showPrompt();
							if (isset($_SESSION['errprompt'])) showError();

							if ($record_type == "clerking_note") {
								echo "<label>Diagnosis</label>
								<div>" . $diagnosis . "</div>
								<hr>";
							}
							?>

							<label>Note Clarification</label>
							<div><?php echo $note_clarification ?></div>
							<hr>

							<label>Clinical Progress</label>
							<div><?php echo $clinical_progress ?></div>
							<hr>

							<label>Care Plan</label>
							<div><?php echo $care_plan ?></div>
							<hr>

							<?php
							if (isset($record_id) && $result = mysqli_query($con, "SELECT addendums.*, doctor.doctorName AS addendumProvider, 
								specialization.name_specialization AS addendumSpecialty FROM `addendums` JOIN doctor ON doctor.id = addendums.doctorId 
								JOIN specialization ON id_specialization = doctor.specialization WHERE recordId = '$record_id'")) {
								if (mysqli_num_rows($result) > 0) {
									echo "<label>Addendums</label>";

									while ($row = mysqli_fetch_assoc($result)) {
										extract($row);
										// Format data
										$formattedDate = date('j F Y', strtotime($createdAt));
										echo "<div class='table-responsive pt-2'>
											<table class='table table-sm table-bordered'>
												<tbody>
													<tr>
														<th scope='row' class='bg-light w-25'>Date Created</th>
														<td class='align-middle'>" . $formattedDate . "</td>
													</tr>
													<tr>
														<th scope='row' class='bg-light'>Name</th>
														<td class='align-middle'>" . $addendumProvider . "</td>
													</tr>
													<tr>
														<th scope='row' class='bg-light'>Specialization</th>
														<td class='align-middle'>" . $addendumSpecialty . "</td>
													</tr>
												</tbody>
											</table>
										</div>
										<div style='margin-top: 12px;'>" . $content . "</div>
										<hr>";
									}
								}
							}
							?>

							<div class="d-flex justify-content-end">
								<button class="btn btn-primary mt-3" type="button" data-toggle="modal" data-target="#append-modal">Append</button>
							</div>
							<?php include "edit-record.php" ?>
						</div>
					</div>
				</div>
			</div>

			<!-- Sidebar Overlay -->
			<div class="overlay toggle-menu"></div>
		</div>

		<!-- Back To Top Button -->
		<a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
		<!-- Back To Top Button -->

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