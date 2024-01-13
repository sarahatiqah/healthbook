<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['doctorId'], $_SESSION['password'])) {

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

          ?>
          <div class="row mt-3">
            <?php
            $patientID = $_GET['id'];
            $query = "SELECT * FROM  patient WHERE  id='$patientID'";

            if ($result = mysqli_query($con, $query)) {
              $row = mysqli_fetch_assoc($result);
              extract($row);
            ?>
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                      <li class="nav-item">
                        <a href="javascript:void();" data-target="#records" data-toggle="pill" class="nav-link active"><i class="fa fa-book"></i> <span class="hidden-xs"> <?php echo $patientName ?>'s Medical Record</span></a>
                      </li>
                    </ul>
                    <div class="tab-content p-3">
                      <div class="tab-pane active" id="records">

                        <div class="row">
                          <h5 class="mb-3">Patient Profile</h5>

                          <div class="col-md-12">
                            <p>
                              IC Number: <span class="badge badge-light"><?php echo $icPatient ?></span><br>
                              Name: <span class="badge badge-light"><?php echo $patientName ?></span><br>
                              Phone Number: <span class="badge badge-light"><?php echo $patientPhone ?></span><br>
                              Email: <span class="badge badge-light"><?php echo $patientEmail ?></span><br>
                              Gender: <span class="badge badge-light"><?php echo $patientGender ?></span><br>
                              Race: <span class="badge badge-light"><?php echo $patientRace ?></span><br>
                              Address: <span class="badge badge-light"><?php echo $patientAddress ?></span><br>

                            </p>
                          </div>
                        <?php

                      } else {
                        die("Error with the query in the database");
                      }
                        ?>
                        <div class="col-md-12">
                          <h5 class="mt-2 mb-3"><span class="fa fa-clock-o ion-clock float-right"></span> Records History</h5>

                          <div class="table-responsive">
                            <table class="table table-hover table-striped">
                              <thead>
                                <tr>
                                  <th scope="col">Dependent</th>
                                  <th scope="col">Record's Details</th>
                                  <th scope="col">Appointment Details</th>
                                  <th scope="col">Action</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                $count = 1;
                                $query = "SELECT a.*, b.patientName, c.diagnosis,c.clarification,c.clinical_progress,c.care_plan,
                                d.doctorName,e.name_dependent FROM appointment a 
                                JOIN patient b ON a.patientID = b.id 
                                JOIN records c ON a.appId = c.appId
                                JOIN doctor d ON a.doctorID = d.id
                                LEFT JOIN dependent e ON a.dependentID = e.id_dependent
                                WHERE a.patientID = '$patientID'
                                ORDER BY c.id_record DESC";

                                $result = mysqli_query($con, $query);

                                if ($result) {
                                  $currentDate = null;

                                  if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                      extract($row);

                                      // Format the time in 12-hour format
                                      $formattedTime = date('h:i A', strtotime($appTime));

                                      // Format the date and display the day
                                      $formattedDate = date('d/m/Y', strtotime($appDate)) . " (" . date('l', strtotime($appDate)) . ")";

                                      if ($doctorID == $did) {
                                        $editButton = "<a href='edit-record.php?id={$appId}&did={$doctorID}' class='btn btn-warning'><i class='icon-pencil'></i> Edit</a> ";
                                      } else {
                                        $editButton = '';
                                      }
                                ?>
                                      <tr>
                                        <td><?php echo ($name_dependent ? $name_dependent : "No Dependent") ?></td>
                                        <td>
                                          <strong>
                                            Diagnosis: <?php echo ($diagnosis ? $diagnosis : "None") ?><br>
                                            Clarification: <?php echo ($clarification ? $clarification : "None") ?><br>
                                            Clinical Progress:<?php echo ($clinical_progress ? $clinical_progress : "None") ?><br>
                                            Care Plan: <?php echo ($care_plan ? $care_plan : "None") ?>
                                          </strong>
                                        </td>
                                        <td>
                                          Doctor: <?php echo $doctorName ?><br>
                                          Specialization: <?php echo $name_specialization ?><br>
                                          Appointment Date: <?php echo $formattedDate ?><br>
                                          Time: <?php echo $formattedTime ?><br>
                                        </td>
                                        <td>
                                          <?php echo $editButton ?>
                                        </td>
                                      </tr>
                                    <?php
                                    }
                                  } else {
                                    ?>
                                    <tr>
                                      <td colspan="4" style="color: red; text-align: center;">No records found</td>
                                    </tr>
                                <?php
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
                        <!--/row-->
                      </div>


                    </div>
                  </div>
                </div> <a href="patients.php" class="btn btn-secondary px-3 btn-sm">Back</a>
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