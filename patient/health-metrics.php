<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['id'], $_SESSION['password'])) {


  if (isset($_POST['register'])) {
    $id = clean($_POST['id']);
    $height = clean($_POST['height']);
    $weight = clean($_POST['weight']);
    $medical_issues = clean($_POST['medical_issues']);
    $allergies = clean($_POST['allergies']);
    $current_medication = clean($_POST['current_medication']);


    $query = "INSERT INTO health_metrics (height,weight,medical_issues,allergies,current_medication,patientId)
          VALUES ('$height', '$weight', '$medical_issues','$allergies','$current_medication', '$id')";

    if (mysqli_query($con, $query)) {

      $_SESSION['prompt'] = "New health metrics registered.";
      header("location:health-metrics.php");
      exit;
    } else {

      die("Error with the query");
    }
  }



  if (isset($_GET['delete_id'])) {
    $delete_id = clean($_GET['delete_id']);

    // Perform the delete operation
    $query = "DELETE FROM health_metrics WHERE id_health = '$delete_id'";
    if (mysqli_query($con, $query)) {
      $_SESSION['prompt'] = "Health Metrics deleted successfully.";
    } else {
      $_SESSION['errprompt'] = "Error deleting Health Metrics.";
    }

    header("location: health-metrics.php");
    exit;
  }
?>
  <!DOCTYPE html>
  <html lang="en">
  <?php include "head.php"; ?>

  <body class="bg-theme bg-theme2">

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

          $query = "SELECT * FROM health_metrics WHERE patientId='" . $_SESSION['id'] . "'";
          $result = mysqli_query($con, $query);

          if ($result && mysqli_num_rows($result) > 0) {
          ?>
            <div class="row mt-3">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">My Health Metrics</h5>
                    <div class="table-responsive">
                      <table class="table table-sm table-bordered">
                        <thead>
                          <tr>
                            <th scope="col">Height (CM)</th>
                            <th scope="col">Weight (KG)</th>
                            <th scope="col">Medical Issues</th>
                            <th scope="col">Allergies</th>
                            <th scope="col">Current Medication</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          while ($row = mysqli_fetch_assoc($result)) {
                          ?>
                            <tr>
                              <td><?php echo $row['height']; ?></td>
                              <td><?php echo $row['weight']; ?></td>
                              <td><?php echo $row['medical_issues']; ?></td>
                              <td><?php echo $row['allergies']; ?></td>
                              <td><?php echo $row['current_medication']; ?></td>
                              <td>
                                <a href="edit-health-metrics.php?id=<?php echo $row['id_health']; ?>" class="btn btn-warning"><i class="icon-pencil"></i> Edit</a>
                                <a href="health-metrics.php?delete_id=<?php echo $row['id_health']; ?>" class="btn btn-danger" onclick="return confirmDelete();"><i class="icon-trash"></i> Delete</a>
                              </td>
                            </tr>
                          <?php
                          }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php
          } else {
          ?>
            <div class="row mt-3">
              <div class="col-lg-4">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Add New Health Metrics</div>
                    <hr>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id" value="<?php echo $_SESSION['id'] ?>">
                      <div class="form-group">
                        <label for="input-1">Height (CM)</label>
                        <input type="text" name="height" class="form-control" placeholder="Enter Height in CM" required pattern="^\d+(\.\d+)?$">
                      </div>
                      <div class="form-group">
                        <label for="input-2">Weight (KG)</label>
                        <input type="text" name="weight" class="form-control" placeholder="Enter Weight in KG" required pattern="^\d+(\.\d+)?$">
                      </div>
                      <div class="form-group">
                        <label for="input-2">Medical Issues</label>
                        <textarea name="medical_issues" class="form-control" placeholder="Enter Medical Issues" required></textarea>
                      </div>
                      <div class="form-group">
                        <label for="input-2">Allergies</label>
                        <textarea name="allergies" class="form-control" placeholder="Enter Allergies" required></textarea>
                      </div>
                      <div class="form-group">
                        <label for="input-2">Current Medication</label>
                        <textarea name="current_medication" class="form-control" placeholder="Enter Current Medication" required></textarea>
                      </div>
                      <div class="form-group">
                        <input type="submit" class="btn btn-primary px-5" name="register" value="Submit">
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          <?php
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


    <script>
      function confirmDelete() {
        return confirm("Are you sure you want to delete this health metrics?");
      }
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