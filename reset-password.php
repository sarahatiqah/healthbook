<!DOCTYPE html>
<html lang="en">
<?php
include "dbconnection.php";
include "functions.php";
include "head.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

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

  // Check if the email exists in either the patient or staff table
  if (!isset($row_patient['patientEmail']) && !isset($row_staff['staffEmail']) && !isset($row_doctor['doctorEmail'])) {
?>
    <script type="text/javascript">
      alert('Email not found.');
    </script>
    <?php
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

    // Load composer's autoloader
    require 'vendor/autoload.php';

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
      $_SESSION['result'] = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
      $_SESSION['status'] = 'error';
    }

    if (isset($result_patient) || isset($result_staff) || isset($result_doctor)) {
    ?>
      <script type="text/javascript">
        alert('Done reset. Please check your email.');
        window.location.href = 'index.php';
      </script>
<?php
    } else {
      echo ("Error description: " . mysqli_error($con));
    }
  }
}
?>


<body class="bg-theme bg-theme2">

  <!-- Start wrapper-->
  <div id="wrapper">

    <div class="height-100v d-flex align-items-center justify-content-center">
      <div class="card card-authentication1 mb-0">
        <div class="card-body">
          <div class="card-content p-2">
            <div class="text-center">
              <img src="assets/images/logo-icon.png" alt="logo icon">
            </div>
            <!-- <div class="card-title text-uppercase pb-2">Reset Password</div> -->
            <div class="card-title text-uppercase text-center py-3">HealthBook | Reset Password</div>
            <p class="pb-2">Please enter your email address. You will receive a link to create a new password via email.</p>
            <form method="POST" action="">
              <div class="form-group">
                <label for="email_reset" class="">Email Address</label>
                <div class="position-relative has-icon-right">
                  <input type="text" name="email_reset" class="form-control input-shadow" placeholder="Email Address" required>
                  <div class="form-control-position">
                    <i class="icon-envelope-open"></i>
                  </div>
                </div>
              </div>

              <input type="submit" class="btn btn-primary btn-block mt-3" value="Reset Password" name="forgot">
            </form>
          </div>
        </div>
        <div class="card-footer text-center py-3">
          <p class="text-warning mb-0">Return to the <a href="index.php"> Sign In</a></p>
        </div>
      </div>
    </div>

    <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->



  </div><!--wrapper-->

  <!-- Bootstrap core JavaScript-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <!-- sidebar-menu js -->
  <script src="assets/js/sidebar-menu.js"></script>

  <!-- Custom scripts -->
  <script src="assets/js/app-script.js"></script>


</body>

</html>