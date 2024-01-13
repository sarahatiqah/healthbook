<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['id'], $_SESSION['password'])) {

  if (isset($_POST['save'])) {
    $id = clean($_POST['id_health']);
    $height = clean($_POST['height']);
    $weight = clean($_POST['weight']);
    $medical_issues = clean($_POST['medical_issues']);
    $allergies = clean($_POST['allergies']);
    $current_medication = clean($_POST['current_medication']);


    // Continue with the rest of your update logic
    $updateQuery = "UPDATE health_metrics SET
        height = '$height',
        weight = '$weight',
         medical_issues = '$medical_issues',
          allergies = '$allergies',
           current_medication = '$current_medication'
        WHERE id_health = '$id'";

    if (mysqli_query($con, $updateQuery)) {
      $_SESSION['prompt'] = "Health Metrics information updated successfully.";
      header("location:health-metrics.php");
      exit;
    } else {
      $_SESSION['errprompt'] = "Error updating Health Metrics information: " . mysqli_error($con);
      header("location:edit-health-metrics.php?id=" . $id);
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
          $idhealth = $_GET['id'];
          $query = "SELECT * from health_metrics WHERE id_health=$idhealth";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);

          ?>
            <div class="row mt-3">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Edit My Health Metrics</div>
                    <hr>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id_health" value="<?php echo $idhealth ?>">
                      <div class="form-group">
                        <label for="input-1">Height</label>
                        <input type="text" name="height" class="form-control" placeholder="Enter Height in CM" value="<?php echo $height ?>" required pattern="^\d+(\.\d+)?$">
                      </div>
                      <div class="form-group">
                        <label for="input-1">Weight</label>
                        <input type="text" name="weight" class="form-control" placeholder="Enter Weight in KG" value="<?php echo $weight ?>" required pattern="^\d+(\.\d+)?$">
                      </div>
                      <div class="form-group">
                        <label for="input-1">Medical Issues</label>
                        <textarea name="medical_issues" class="form-control" placeholder="Enter Medical Issues" value="<?php echo $medical_issues ?>" required><?php echo $medical_issues ?></textarea>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Allergies</label>
                        <textarea name="allergies" class="form-control" placeholder="Enter Allergies" value="<?php echo $allergies ?>" required><?php echo $allergies ?></textarea>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Current Medication</label>
                        <textarea name="current_medication" class="form-control" placeholder="Enter Current Medication" value="<?php echo $current_medication ?>" required><?php echo $current_medication ?></textarea>
                      </div>

                      <div class="form-group">
                        <a href="health-metrics.php" class="btn btn-secondary px-3">Cancel</a>
                        <input type="submit" class="btn btn-primary px-4" name="save" value="Save">
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