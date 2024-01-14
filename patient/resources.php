<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['id'], $_SESSION['password'])) {
?>
  <!DOCTYPE html>
  <html lang="en">
  <?php include "head.php"; ?>

  <body class="bg-theme bg-theme9">

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
          ?>
          <div class="row mt-3">


            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">List of Resources</h5>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Created By</th>
                          <th scope="col">Title</th>
                          <th scope="col">Tags</th>
                          <th scope="col">Date Uploaded</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $count = 1;

                        $sqlMetrics = "SELECT medical_issues FROM health_metrics WHERE patientId='" . $_SESSION['id'] . "'";
                        $resultMetrics = mysqli_query($con, $sqlMetrics);

                        $sqlRecords = "SELECT a.diagnosis FROM records a JOIN appointment b WHERE a.app_id=b.appId AND b.patientId='" . $_SESSION['id'] . "'";
                        $resultRecords = mysqli_query($con, $sqlRecords);
                        $issues = "";
                        echo 'Your diagnosis is: <b>';
                        while ($rowRecords = mysqli_fetch_assoc($resultRecords)) {
                          echo '<span class="badge badge-dark">';
                          echo strtoupper($rowRecords['diagnosis']) . '</span> ';
                          $issues .= strtoupper($rowRecords['diagnosis']) . ",";
                        }

                        // die($issues);
                        echo '</b><br><br>';
                        $sqlEducational = "SELECT a.*, b.doctorName, a.doctorID as eduDID 
                              FROM educational a 
                              JOIN doctor b ON a.doctorID = b.id"; // Add the ON condition for the JOIN
                        $resultEducational = mysqli_query($con, $sqlEducational);

                        if ($resultEducational) {
                          if (mysqli_num_rows($resultEducational) > 0) {
                            while ($rowMetrics = mysqli_fetch_assoc($resultMetrics)) {
                              $medicalIssues = $rowMetrics['medical_issues'];
                              echo 'Your medical issues is: <b><span class="badge badge-dark">' . strtoupper($medicalIssues) . '</span></b><br><br>';

                              $issues .= strtoupper($medicalIssues);


                              // Process both medical issues and tag values using a common function
                              // $medicalIssuesArray = processString($medicalIssues);
                              $medicalIssuesArray = array_map('strtolower', preg_split('/[,.]+/', $issues));
                              // $medicalIssuesArray = array_map('strtolower', preg_split('/[,.]+/', $medicalIssues));


                              mysqli_data_seek($resultEducational, 0);
                              while ($rowEducational = mysqli_fetch_assoc($resultEducational)) {
                                $tags = json_decode($rowEducational['tags'], true);
                                $tagValues = array_map('strtolower', array_column($tags, 'value'));

                                // Process tag values using the same function
                                // $tagValues = processString(implode(' ', $tagValues));


                                // Find common elements
                                // $commonElements = array_intersect($medicalIssuesArray, $tagValues);

                                $matchingElements = [];

                                foreach ($medicalIssuesArray as $medicalIssue) {
                                  foreach ($tagValues as $tagValue) {
                                    if (stripos($medicalIssue, $tagValue) !== false) {
                                      $matchingElements[] = $tagValue;
                                    }
                                  }
                                }

                                $viewButton = "<a href='{$rowEducational['document']}' target='_blank' class='btn btn-light'><i class='icon-eye'></i> View Document </a>";
                                $timestamp = strtotime($rowEducational['date_update']);
                                $newDateFormat = date("d/m/Y h:i A", $timestamp);

                                if (!empty($matchingElements)) {
                                  echo "<tr>";
                                  echo "<th scope='row'>" . $count . "</th>";
                                  echo "<td>" . $rowEducational['doctorName'] . "</td>";
                                  echo "<td>" . $rowEducational['title'] . "</td>";
                                  echo "<td>" . implode(' ', array_map(function ($matchingElements) {
                                    return '<span class="badge badge-secondary">' . $matchingElements . '</span>';
                                  }, $matchingElements)) . "</td>";
                                  echo "<td>" . $newDateFormat . "</td>";
                                  echo "<td>" . $viewButton . "</td>";
                                  echo "</tr>";

                                  $count++;
                                }
                              }
                            }
                          } else {
                            echo "<tr>";
                            echo "<td colspan='6' style='text-align: center; color: red;'>No matching records found</td>";
                            echo "</tr>";
                          }
                        } else {
                          die("Error with the query in the database");
                        }


                        ?>


                      </tbody>
                    </table>

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