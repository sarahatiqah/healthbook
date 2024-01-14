<?php
session_start();

require '../dbconnection.php';
require '../functions.php';

if (!empty($patientId = $_GET['patientId']) && !empty($appId = $_GET['appointmentId'])) {
    $query = "SELECT patient.*, CONCAT('P', LPAD(patient.id, 3, '0')) AS patientId, 
                appointment.appType
                FROM patient
                JOIN appointment ON CONCAT('A', LPAD(appId, 3, '0')) = '$appId'
                WHERE CONCAT('P', LPAD(patient.id, 3, '0')) = '$patientId';";

    if ($result = mysqli_query($con, $query)) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            extract($row);
        } else {
            $_SESSION['errprompt'] = "Patient with ID = $patientId is not found.";
            header("location:records.php?patientId=$patientId");
            exit;
        }
    } else {
        $_SESSION['errprompt'] = "An error had occured during query of database.";
        header("location:records.php?patientId=$patientId");
        exit;
    }
} else {
    $_SESSION['errprompt'] = "Please specify a patient ID.";
    header("location:records.php");
    exit;
}

if (isset($_POST['save'])) {
    // Input sanitation
    $recordType = clean($_POST['recordType']) == "Clerking Note" ? "clerking_note" : "progress_note";
    $diagnosis = clean($_POST['diagnosis']);
    $noteClarification = clean($_POST['noteClarification']);
    $clinicalProgress = clean($_POST['clinicalProgress']);
    $carePlan = clean($_POST['carePlan']);
    sscanf($appId, 'A%03d', $cleanedAppId);
    sscanf($patientId, 'P%03d', $cleanedPatientId);
    sscanf($_SESSION['doctorId'], 'D%03d', $cleanedDoctorId);
    $appStatus = 'done';

    // Start a transaction
    mysqli_begin_transaction($con);

    try {
        // Insert record
        $insertQuery = "INSERT INTO records (app_id, patient_id, doctor_id, record_type, diagnosis, note_clarification, clinical_progress, care_plan) VALUES (?,?,?,?,?,?,?,?)";
        $insertStmt = mysqli_prepare($con, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "ssssssss", $cleanedAppId, $cleanedPatientId, $cleanedDoctorId, $recordType, $diagnosis, $noteClarification, $clinicalProgress, $carePlan);
        mysqli_stmt_execute($insertStmt);

        // Update appointment status
        $updateQuery = "UPDATE appointment SET status = ? WHERE appId = ?";
        $updateStmt = mysqli_prepare($con, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "ss", $appStatus, $cleanedAppId);
        mysqli_stmt_execute($updateStmt);

        // Commit the transaction
        mysqli_commit($con);

        $_SESSION['prompt'] = "Medical record saved successfully.";
        header("location:records.php?patientId=$patientId");
        exit;
    } catch (Exception $e) {
        // Rollback the transaction
        mysqli_rollback($con);
        $_SESSION['errprompt'] = "An error occured: " . $e->getMessage();
    } finally {
        // Close the prepared statements
        if (isset($insertStmt)) {
            mysqli_stmt_close($insertStmt);
        }
        if (isset($updateStmt)) {
            mysqli_stmt_close($updateStmt);
        }
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
                                        <input type="text" disabled class="form-control" id="patientId" value="<?php echo $patientId; ?>">
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
                                                <td class="align-middle"><?php echo $icPatient; ?></td>
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
                                <div class="card-title">Doctor Details</div>

                                <div class="form-group">
                                    <label for="doctorId">Doctor ID</label>
                                    <div class="input-group">
                                        <input type="text" disabled class="form-control" id="doctorId" value="<?php echo $doctorId; ?>">
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <tbody>
                                            <tr>
                                                <th scope="row" class="bg-light w-25">Name</th>
                                                <td class="align-middle"><?php echo $doctorName; ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row" class="bg-light">Specialization</th>
                                                <td class="align-middle"><?php echo $name_specialization; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Create Record Form -->
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Create Record</div>
                            <!-- Alerts -->
                            <?php
                            if (isset($_SESSION['errprompt'])) {
                                showError();
                            }
                            ?>
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="recordType">Type</label>
                                    <div class="input-group">
                                        <input required type="text" hidden class="form-control" name="recordType" id="recordType" value="<?php echo $appType == "new" ? "Clerking Note" : "Progress Note"; ?>">
                                        <input required type="text" disabled class="form-control" name="recordType" id="recordType" value="<?php echo $appType == "new" ? "Clerking Note" : "Progress Note"; ?>">
                                    </div>
                                </div>

                                <?php
                                if ($appType == "new") {
                                    echo "<div class='form-group'>
                                        <label for='diagnosis'>Diagnosis</label>
                                        <div class='input-group'><textarea required class='form-control' name='diagnosis' id='diagnosis' placeholder='Enter diagnosis'>" . (isset($_POST['diagnosis']) ? $_POST['diagnosis'] : "") . "</textarea></div>
                                    </div>";
                                }
                                ?>

                                <div class="form-group">
                                    <label for="noteClarification">Note Clarification</label>
                                    <div class="input-group">
                                        <textarea required type="text" class="form-control" name="noteClarification" id="noteClarification" placeholder="Enter note clarification"><?php echo isset($_POST['noteClarification']) ? $_POST['noteClarification'] : ""; ?></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="clinicalProgress">Clinical Progress</label>
                                    <div class="input-group">
                                        <textarea required type="text" class="form-control" name="clinicalProgress" id="clinicalProgress" placeholder="Enter clinical progress"><?php echo isset($_POST['clinicalProgress']) ? $_POST['clinicalProgress'] : ""; ?></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="carePlan">Care Plan</label>
                                    <div class="input-group">
                                        <textarea required type="text" class="form-control" name="carePlan" id="carePlan" placeholder="Enter care plan"><?php echo isset($_POST['carePlan']) ? $_POST['carePlan'] : ""; ?></textarea>
                                    </div>
                                </div>

                                <div class="pt-3 d-flex justify-content-between"><a class="btn btn-light" href="records.php?patientId=<?= $patientId ?>">Cancel</a><button class="btn btn-primary" type="submit" name="save">Submit</button></div>
                            </form>
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
mysqli_close($con);
?>