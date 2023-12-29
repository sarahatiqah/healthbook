<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['id'], $_SESSION['password'])) {


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

          $query = "SELECT * FROM assessment_data WHERE patientID='" . $_SESSION['id'] . "' ORDER BY date_assessment ASC";
          $result = mysqli_query($con, $query);
          $count = 0;
          $ass = "";
          while ($row = mysqli_fetch_assoc($result)) {

            $count++;
            $timestamp = strtotime($row['date_assessment']);
            $newDateFormat = date("d/m/Y h:i A", $timestamp);
            $ass = $row['assessmentResult'];
          ?>
            <!-- <tr>
              <td><?php echo $row['symptoms']; ?></td>
              <td>
                Type of Symptoms: <span class="badge badge-danger"><?php echo ($row['type_of_symptoms'] ? $row['type_of_symptoms'] : "None"); ?></span><br>
                Contact: <span class="badge badge-danger"><?php echo $row['contact']; ?></span><br>
                Travel: <span class="badge badge-danger"><?php echo $row['travel']; ?></span><br>
                Exposure: <span class="badge badge-danger"><?php echo $row['exposure']; ?></span><br>
                Hygiene: <span class="badge badge-danger"><?php echo $row['hygiene']; ?></span><br>
                Symptom Duration: <span class="badge badge-danger"><?php echo $row['symptom_duration']; ?></span>
              </td>
              <td style="word-wrap: break-word; white-space: pre-line;"><?php echo $row['assessmentResult']; ?></td>

              <td><?php echo $newDateFormat; ?></td>
            </tr> -->

          <?php
          }



          ?>
          <div class="row mt-3">
            <div class="col-lg-12">
              <a href="covid-assessment.php" class="btn btn-primary px-3 mb-3"><i class='icon-plus'></i> New Assessment</a>
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">My Health Metrics</h5>
                  <h5 class="card-title">Current Status:
                    <?php
                    if ($count == 0) {

                      echo ' <span class="badge badge-light">N/A (No records! Please take an assessment now!)</span>';
                    } else {
                      if (strstr($ass, "COVID") !== false) {
                        echo ' <span class="badge badge-danger">POSITIVE</span>';
                      } else {
                        echo ' <span class="badge badge-success">NEGATIVE</span>';
                      }
                    }
                    ?>
                  </h5>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                      <thead>
                        <tr>
                          <th scope="col">Have Symptom?</th>
                          <th scope="col">Details</th>
                          <th scope="col">Result</th>
                          <th scope="col">Date Assessment</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $query = "SELECT * FROM assessment_data WHERE patientID='" . $_SESSION['id'] . "' ORDER BY date_assessment DESC";
                        $result = mysqli_query($con, $query);

                        while ($row = mysqli_fetch_assoc($result)) {

                          $timestamp = strtotime($row['date_assessment']);
                          $newDateFormat = date("d/m/Y h:i A", $timestamp);
                        ?>
                          <tr>
                            <td><?php echo $row['symptoms']; ?></td>
                            <td>
                              Type of Symptoms: <span class="badge badge-dark"><?php echo ($row['type_of_symptoms'] ? $row['type_of_symptoms'] : "None"); ?></span><br>
                              Contact: <span class="badge badge-dark"><?php echo $row['contact']; ?></span><br>
                              Travel: <span class="badge badge-dark"><?php echo $row['travel']; ?></span><br>
                              Exposure: <span class="badge badge-dark"><?php echo $row['exposure']; ?></span><br>
                              Hygiene: <span class="badge badge-dark"><?php echo $row['hygiene']; ?></span><br>
                              Symptom Duration: <span class="badge badge-dark"><?php echo $row['symptom_duration']; ?></span>
                            </td>
                            <td style="word-wrap: break-word; white-space: pre-line;"><?php echo $row['assessmentResult']; ?></td>

                            <td><?php echo $newDateFormat; ?></td>
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