<!DOCTYPE html>
<html lang="en">

<?php
include "dbconnection.php";
include "functions.php";
include "head.php";

// Import PHPMailer into global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

if (isset($_POST['forgot'])) {
	$email_reset = $_POST['email_reset'];

	// Check if the email exists in the patient table
	$res_patient = mysqli_query($con, "SELECT * FROM patient WHERE patientEmail='$email_reset'");
	$row_patient = mysqli_fetch_array($res_patient);

	// Check if the email exists in the staff table
	$res_staff = mysqli_query($con, "SELECT * FROM staff WHERE staffEmail='$email_reset'");
	$row_staff = mysqli_fetch_array($res_staff);

	// Check if the email exists in the doctor table
	$res_doctor = mysqli_query($con, "SELECT * FROM doctor WHERE doctorEmail='$email_reset'");
	$row_doctor = mysqli_fetch_array($res_doctor);

	if (!isset($row_patient['patientEmail']) && !isset($row_staff['staffEmail']) && !isset($row_doctor['doctorEmail'])) {
		$_SESSION['errprompt'] = "Email address not found.";
	} else {
		$newpassword = uniqid('healthbook');

		// Update patient password
		if (isset($row_patient['patientEmail'])) {
			$query_patient = "UPDATE patient SET password='$newpassword' WHERE patientEmail ='$email_reset'";
			$result_patient = mysqli_query($con, $query_patient);
		}

		// Update staff password
		if (isset($row_staff['staffEmail'])) {
			$query_staff = "UPDATE staff SET password='$newpassword' WHERE staffEmail ='$email_reset'";
			$result_staff = mysqli_query($con, $query_staff);
		}

		// Update doctor password
		if (isset($row_doctor['doctorEmail'])) {
			$query_doctor = "UPDATE doctor SET password='$newpassword' WHERE doctorEmail ='$email_reset'";
			$result_doctor = mysqli_query($con, $query_doctor);
		}

		// Send password reset email
		$subject = "Reset Password";
		$message = "Here is your new password:<br>New Password: " . $newpassword;

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
			$mail->addAddress($email_reset);
			$mail->addReplyTo('healthboook@gmail.com');

			// Content
			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body = $message;

			$mail->send();

			$_SESSION['result'] = 'Message has been sent';
			$_SESSION['status'] = 'ok';
		} catch (Exception $e) {
			$_SESSION['result'] = 'Message could not be sent. Error: ' . $mail->ErrorInfo;
			$_SESSION['status'] = 'error';
		}

		if (isset($result_patient) || isset($result_staff) || isset($result_doctor)) {
			$_SESSION['prompt'] = "Password reset successful. Please check your email.";
		} else {
			$_SESSION['errprompt'] = "Database Error: " . mysqli_error($con);
		}
	}

	mysqli_close($con); // Close the database connection
}
?>

<body class="bg-theme bg-theme9 vh-100 d-flex align-items-center">
	<div class="card card-authentication1 mx-auto p-2">
		<div class="card-body">
			<!-- Title -->
			<div class="text-center">
				<img src="assets/images/logo-icon.svg" class="w-50" alt="logo">
			</div>
			<div class="card-title text-center py-3">Reset Your Password</div>

			<!-- Alerts -->
			<?php
			if (isset($_SESSION['prompt'])) {
				showPrompt();
			}
			if (isset($_SESSION['errprompt'])) {
				showError();
			}
			?>

			<!-- Subtitle -->
			<p class="pb-2">Please enter your email address. If the email address exists, you will receive an email with the new password.</p>

			<!-- Form -->
			<form method="POST" action="">
				<div class="form-group">
					<label for="email_reset" class="">Email</label>
					<input type="text" name="email_reset" class="form-control input-shadow" placeholder="Enter Email" required>
				</div>

				<div class="pt-3">
					<input type="submit" class="btn btn-primary btn-block" value="Reset Password" name="forgot">
				</div>
			</form>
		</div>

		<div class="card-footer text-center border-0 pt-0">
			<h6>Return to <a href="index.php" class="link">sign in</a></h6>
		</div>
	</div>

	<!-- Bootstrap Core -->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/popper.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
</body>

</html>

<?php

unset($_SESSION['prompt']);
unset($_SESSION['errprompt']);

?>