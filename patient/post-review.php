<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['id'], $_SESSION['password'])) {
  $idapp = $_GET['id'];


  if (isset($_POST['save'])) {

    // Assuming you have a clean function for input sanitation
    $id = clean($_POST['id']);
    $post = clean($_POST['post']);
    $doctorName = clean($_POST['doctorName']);
    $rating = clean($_POST['rating']);
    // $pid = clean($_POST['patientID']);

    // Start a transaction
    mysqli_begin_transaction($con);

    try {
      // Insert record
      $insertQuery = "INSERT reviews (appID, post,rating) VALUES (?, ?, ?)";
      $insertStmt = mysqli_prepare($con, $insertQuery);
      mysqli_stmt_bind_param($insertStmt, "ssi", $id, $post, $rating);
      mysqli_stmt_execute($insertStmt);


      // Commit the transaction
      mysqli_commit($con);

      $_SESSION['prompt'] = "Thankyou for reviewing $doctorName.";
      header("location:home.php");
      exit;
    } catch (Exception $e) {
      // Rollback the transaction on error
      mysqli_rollback($con);

      $_SESSION['errprompt'] = "Error inserting patient record: " . $e->getMessage();
      header("location:post-review.php?id=" . $id);
      exit;
    } finally {
      // Close the prepared statements
      mysqli_stmt_close($insertStmt);

      // Close the database connection
      mysqli_close($con);
    }
  }

?>
  <!DOCTYPE html>
  <html lang="en">
  <?php include "head.php"; ?>

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

          $query = "SELECT a.*,b.patientName,b.patientPhone,b.id AS pid, c.doctorName
          FROM appointment a 
          JOIN patient b 
          JOIN doctor c 
          WHERE a.appId=$idapp AND a.patientID=b.id AND a.doctorID=c.id";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);
            // Format the time in 12-hour format
            $formattedTime = date('h:i A', strtotime($appTime));

            // Format the date and display the day
            $formattedDate = date('d/m/Y', strtotime($appDate));
          ?>
            <div class="row mt-3">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Post Review to <?php echo $doctorName ?></div>
                    <hr>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id" value="<?php echo $idapp ?>">
                      <input type="hidden" name="patientID" value="<?php echo $pid ?>">
                      <input type="hidden" name="doctorName" value="<?php echo $doctorName ?>">
                      <div class="form-group">
                        <label for="input-1">Patient Name</label>
                        <input type="text" class="form-control" value="<?php echo $patientName ?>" disabled>
                      </div>

                      <div class="form-group">
                        <label for="input-1">Appointment Date</label>
                        <input type="text" name="appDate" disabled class="form-control" value="<?php echo $formattedDate; ?>">
                      </div>

                      <div class="form-group">
                        <label for="input-1">Time Slot</label>
                        <input type="text" name="appDate" disabled class="form-control" value="<?php echo $formattedTime; ?>">
                      </div>
                      <hr>
                      <h5>Detail's Doctor</h5>
                      <div class="form-group">
                        <label for="input-1">Doctor Name</label>
                        <input type="text" class="form-control" value="<?php echo $doctorName ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Review</label>
                        <textarea name="post" class="form-control" placeholder="Post a review for <?php echo $doctorName ?> service" required></textarea>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Rating</label>
                        <select name="rating" class="form-control" required>
                          <option selected disabled value="">Pick Rating</option>
                          <option value="5">5 ★★★★★</option>
                          <option value="4">4 ★★★★</option>
                          <option value="3">3 ★★★</option>
                          <option value="2">2 ★★</option>
                          <option value="1">1 ★</option>
                        </select>
                      </div>

                      <div class="form-group">
                        <a href="appointment.php" class="btn btn-secondary px-3">Cancel</a>
                        <input type="submit" class="btn btn-primary px-4" name="save" value="Post">
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <?php

          } else {
            die("Error with the query in the database");
          }
            ?>


            <div> <!--start overlay-->
              <div class="overlay toggle-menu"></div>
              <!--end overlay-->

            </div>
            <!-- End container-fluid-->

            </div><!--End content-wrapper-->
            <!--Start Back To Top Button-->
            <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
            <!--End Back To Top Button-->

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