<?php
session_start();

require '../dbconnection.php';
require '../functions.php';

// Pagination config
$recordsPerPage = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;
$totalRecords = 0;
$totalPages = 1;

// Fetch stored filter
$idFilter = $_SESSION["filter"] ?? "";

// Reset pagination on filter
if (isset($_POST["filter"])) {
	$patientId = $_GET['patientId'] ?? null;
	$_SESSION["filter"] = $_POST["filter"];
	header("Location: records.php" . (isset($_GET['patientId']) ? "?patientId=$patientId" : ""));
	exit();
}

// Get patient details
if (!empty($_GET['patientId'])) {
	$formattedPatientId = mysqli_real_escape_string($con, $_GET['patientId']);
	$query = "SELECT * FROM patient WHERE CONCAT('P', LPAD(id, 3, '0')) = '$formattedPatientId'";

	if ($result = mysqli_query($con, $query)) {
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			extract($row);
		} else {
			$_SESSION['errprompt'] = "Patient with ID = $formattedPatientId is not found.";
		}
	} else {
		$_SESSION['errprompt'] = "An error had occured during query of database.";
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
					<!-- Patient Details -->
					<div class="card mt-3">
						<div class="card-body">
							<div class="card-title">Patient Details</div>

							<!-- Alerts -->
							<?php
							if (isset($_SESSION['errprompt'])) {
								showError();
							} elseif (isset($_SESSION['prompt'])) {
								showPrompt();
							}
							?>

							<form method="GET" action="">
								<div class="form-group">
									<label for="patientId">Patient ID</label>
									<div class="input-group">
										<input type="text" class="form-control shadow-none" placeholder="Enter patient ID" name="patientId" id="patientId" value="<?php echo isset($_GET['patientId']) ? htmlspecialchars($_GET['patientId']) : ''; ?>">
										<div class="input-group-append"><button class="btn btn-primary" type="submit">Submit</button></div>
									</div>
								</div>
							</form>

							<div class="table-responsive">
								<table class="table table-sm table-bordered">
									<tbody>
										<tr>
											<th scope="row" class="bg-light w-25">Name</th>
											<td class="align-middle"><?php echo isset($patientName) ? $patientName : "N/A"; ?></td>
										</tr>
										<tr>
											<th scope="row" class="bg-light">IC No.</th>
											<td class="align-middle"><?php echo isset($icPatient) ? $icPatient : "N/A"; ?></td>
										</tr>
										<tr>
											<th scope="row" class="bg-light">Gender</th>
											<td class="align-middle"><?php echo isset($patientGender) ? $patientGender : "N/A"; ?></td>
										</tr>
										<tr>
											<th scope="row" class="bg-light">Race</th>
											<td class="align-middle"><?php echo isset($patientRace) ? $patientRace : "N/A"; ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<!-- Appointments -->
					<div class="card mt-3">
						<div class="card-body">
							<div class="card-title">Upcoming Appointments</div>

							<div class="table-responsive">
								<table class="table table-bordered table-hover">
									<thead class="bg-light">
										<tr>
											<th scope="col">ID</th>
											<th scope="col">Date</th>
											<th scope="col">Time</th>
											<th scope="col" style="width:1.5rem;">Action</th>
										</tr>
									</thead>
									<tbody>
										<?php
										if (!empty($_GET['patientId']) && $result = mysqli_query($con, "SELECT *, CONCAT('A', LPAD(appId, 3, '0')) AS id FROM appointment WHERE CONCAT('P', LPAD(patientId, 3, '0')) = '$formattedPatientId' AND status = 'approved';")) {
											if (mysqli_num_rows($result) > 0) {
												while ($row = mysqli_fetch_assoc($result)) {
													extract($row);
													// Format IDs
													$formattedAppId = sprintf('A%03d', $appId);

													// Format date and time
													$formattedTime = date('h:i A', strtotime($appTime));
													$formattedDate = date('j F Y', strtotime($appDate));

													// Define create button
													$addBtn = "<a href='create-record.php?patientId=$formattedPatientId&appointmentId=$formattedAppId' class='btn btn-primary'><i class='fa fa-plus'></i></a>";

													echo "<tr><td class='align-middle'>$formattedAppId</td><td class='align-middle'>$formattedDate</td><td class='align-middle'>$formattedTime</td><td style='width:1.5rem;' class='align-middle'>$addBtn</td></tr>";
												}
											} else {
												if (isset($_SESSION['errprompt']))
													echo "<tr><td colspan='4'><h5 class='text-muted mt-3'><b>No Results Found</b></h5><p class='text-muted'>Enter a valid patient ID</p></td></tr>";
												else
													echo "<tr><td colspan='4'><h5 class='text-muted mt-3'><b>No Results Found</b></h5><p class='text-muted'>No appointment slated</p></td></tr>";
											}
										} else {
											echo "<tr><td colspan='4'><h5 class='text-muted mt-3'><b>No Results Found</b></h5><p class='text-muted'>Enter a valid patient ID</p></td></tr>";
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<!-- Medical Records -->
					<div class="card mt-3">
						<div class="card-body">
							<div class="card-title">Medical Records</div>

							<!-- Filter -->
							<form method="POST" action="" class="mb-4">
								<div class="form-group">
									<label class="sr-only" for="filter">Filter</label>
									<div class="input-group">
										<input type="text" class="form-control shadow-none" id="filter" name="filter" placeholder="Filter by ID" value="<?= $idFilter; ?>">
										<div class="input-group-append"><button class="btn btn-primary" type="submit">Filter</button></div>
									</div>
								</div>
							</form>

							<table class="table table-bordered table-hover">
								<thead class="bg-light">
									<tr>
										<th scope="col">ID</th>
										<th scope="col">Type</th>
										<th scope="col">Date</th>
										<th scope="col">Provider</th>
										<th scope="col" style="width:1.5rem;">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									if (!empty($_GET['patientId'])) {
										// Begin base sql
										$sql = "SELECT records.*, doctor.doctorName AS providerName FROM records JOIN doctor ON records.doctor_id = doctor.id WHERE CONCAT('P', LPAD(patient_id, 3, '0')) = '$formattedPatientId'";

										// Add ID filter
										if (!empty($idFilter)) $sql .= " AND CONCAT('R', LPAD(record_id, 3, '0')) = '$idFilter'";

										// Add pagination
										$sql .= " LIMIT $recordsPerPage OFFSET $offset;";

										if ($result = mysqli_query($con, $sql)) {
											if (mysqli_num_rows($result) > 0) {
												// Rendering logic
												while ($row = mysqli_fetch_assoc($result)) {
													// Fetch the total number of records for pagination
													$totalRecordsSql = "SELECT COUNT(*) as total FROM records WHERE CONCAT('P', LPAD(patient_id, 3, '0')) = '$formattedPatientId'";
													if (!empty($idFilter)) $totalRecordsSql .= " AND CONCAT('R', LPAD(record_id, 3, '0')) = '$idFilter'";
													$totalRecordsResult = $con->query($totalRecordsSql);
													$totalRecords = $totalRecordsResult->fetch_assoc()['total'];
													$totalPages = ceil($totalRecords / $recordsPerPage);

													extract($row);

													// Format data
													$formattedRecordId = sprintf('R%03d', $record_id);
													$formattedDate = date('j F Y', strtotime($created_at));
													$formattedType = $record_type == "clerking_note" ? "Clerking Note" : "Progress Note";

													// Define view button
													$viewBtn = "<a href='view-record.php?recordId=$formattedRecordId' class='btn btn-primary'><i class='fa fa-eye'></i></a>";

													echo "<tr><td class='align-middle'>$formattedRecordId</td><td class='align-middle'>$formattedType</td><td class='align-middle'>$formattedDate</td><td class='align-middle'>$providerName</td><td style='width:1.5rem;' class='align-middle'>$viewBtn</td></tr>";
												}
											} else {
												if (isset($_SESSION['errprompt']))
													echo "<tr><td colspan='6'><h5 class='text-muted mt-3'><b>No Results Found</b></h5><p class='text-muted'>Enter a valid patient ID</p></td></tr>";
												else
													echo "<tr><td colspan='6'><h5 class='text-muted mt-3'><b>No Results Found</b></h5><p class='text-muted'>No matched record yet</p></td></tr>";
											}
										} else {
											echo "<tr><td colspan='6'><h5 class='text-muted mt-3'><b>No Results Found</b></h5><p class='text-muted'>Enter a valid patient ID</p></td></tr>";
										}
									} else {
										echo "<tr><td colspan='6'><h5 class='text-muted mt-3'><b>No Results Found</b></h5><p class='text-muted'>Enter a valid patient ID</p></td></tr>";
									}
									?>
								</tbody>
							</table>

							<!-- Pagination -->
							<nav aria-label="Page navigation" class="mt-4 d-flex flex-row justify-content-between align-items-center">
								<span class="mb-3">
									Showing <?= min($totalRecords, ($page - 1) * $recordsPerPage + 1); ?>
									to <?= min($totalRecords, $page * $recordsPerPage); ?>
									of <?= $totalRecords; ?> results
								</span>
								<ul class="pagination">
									<li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
										<a class="page-link" href="records.php?patientId=<?= $formattedPatientId; ?>&page=<?= $page - 1; ?>" aria-label="Previous">
											<span aria-hidden="true">&laquo;</span>
										</a>
									</li>

									<li class="page-item <?= ($page >= $totalPages) ? 'disabled' : ''; ?>">
										<a class="page-link" href="records.php?patientId=<?= $formattedPatientId; ?>&page=<?= $page + 1; ?>" aria-label="Next">
											<span aria-hidden="true">&raquo;</span>
										</a>
									</li>
								</ul>
							</nav>
						</div>
					</div>
				</div>
			</div>
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
unset($_SESSION['filter']);
mysqli_close($con);
?>