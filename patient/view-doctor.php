<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['id'], $_SESSION['password'])) {



?>
  <!DOCTYPE html>
  <html lang="en">
  <?php include "head.php"; ?>
  <style>
  .table-responsive td,
  .table-responsive th {
    white-space: normal;
    word-wrap: break-word;
  }
  </style>

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
          $did = $_GET['did'];
          $query = "SELECT a.*,b.name_specialization FROM doctor a JOIN specialization b WHERE a.id=$did AND a.specialization=b.id_specialization";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);




            $query2 = "SELECT COUNT(*) as total FROM appointment WHERE patientID='" . $_SESSION['id'] . "' AND doctorID='$did'";
            $result2 = mysqli_query($con, $query2);
            $row2 = mysqli_fetch_assoc($result2);
            $countApp = $row2['total'];
          ?>
            <div class="row mt-3">
              <div class="col-lg-4">
                <div class="card profile-card-2">
                  <div class="card-img-block">
                    <img class="img-fluid" src="../assets/images/bannerprofile.jpg">
                  </div>
                  <div class="card-body pt-5">
                    <img src="../assets/images/doctor.jpg" class="profile">
                    <h5 class="card-title"><?php echo $doctorName ?></h5>
                    <p class="card-text">Phone Number : <?php echo $doctorPhone ?></p>
                    <p class="card-text">Email : <?php echo $doctorEmail ?></p>
                    <p class="card-text">Specialization : <?php echo $name_specialization ?></p>

                  </div>

                  <div class="card-body border-top border-light">
                    <div class="media align-items-center">
                      <div class="media-body text-left ml-3">
                        <div class="progress-wrapper">
                          <p>Appointment With <?php echo $doctorName ?> <span class="float-right"><?php echo $countApp ?></span></p>
                          <!-- <div class="progress" style="height: 5px;">
                            <div class="progress-bar" style="width:65%"></div>
                          </div> -->
                        </div>
                      </div>
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



              <div class="col-lg-8">
                <div class="card">
                  <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                      <li class="nav-item">
                        <a href="javascript:void();" data-target="#totalApp" data-toggle="pill" class="nav-link active"><i class="fa fa-user-md"></i> <span class="hidden-xs">Total Appointment With <?php echo $doctorName ?></span></a>
                      </li>
                      <li class="nav-item">
                        <a href="javascript:void();" data-target="#review" data-toggle="pill" class="nav-link"><i class="fa fa-star"></i> <span class="hidden-xs">Review's <?php echo $doctorName ?></span></a>
                      </li>
                    </ul>
                    <div class="tab-content p-3">

                      <div class="tab-pane active" id="totalApp">

                        <div class="table-responsive">
                          <table class="table table-sm table-bordered">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Date</th>
                                <th scope="col">Time</th>
                                <th scope="col">Patient</th>
                                <th scope="col">Status</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $count = 1;
                              $query = "SELECT a.*, b.patientName FROM appointment a 
                          JOIN patient b ON a.patientID = b.id 
                          WHERE a.doctorID='$did' AND a.patientID='" . $_SESSION['id'] . "'
                          ORDER BY a.appDate DESC, a.appTime";

                              $result = mysqli_query($con, $query);

                              if ($result) {
                                if (mysqli_num_rows($result) > 0) {
                                  $currentDate = null;

                                  while ($row = mysqli_fetch_assoc($result)) {
                                    extract($row);

                                    // Format the time in 12-hour format
                                    $formattedTime = date('h:i A', strtotime($appTime));

                                    // Format the date and display the day
                                    $formattedDate = date('j F Y', strtotime($appDate)) . "<br><b>" . date('l', strtotime($appDate)) . "</b>";

                                    // View button (always defined)
                                    $viewButton = "<a href='view-patient.php?id={$patientId}' class='btn btn-light'><i class='icon-eye'></i> View</a> ";

                                    // if ($status == 'pending') {
                                    //   $statusBadge = '<span class="badge badge-primary"><i class="fa fa-spinner"></i> PENDING</span>';
                                    //   $approveButton = "<a href='appointment.php?app_id={$appId}' class='btn btn-success' onclick='return confirmApprove();'><i class='icon-check'></i> Approve</a> ";
                                    //   $editButton = "<a href='edit-appointment.php?id={$appId}' class='btn btn-warning'><i class='icon-pencil'></i> Edit</a> ";
                                    //   $deleteButton = "<a href='appointment.php?delete_id={$appId}' class='btn btn-danger' onclick='return confirmDelete();'><i class='icon-trash'></i> Delete</a>";
                                    // } else {
                                    //   $statusBadge = '<span class="badge badge-success"><i class="fa fa-check"></i> DONE</span>';
                                    //   // If status is 'DONE', set $approveButton, $editButton, and $deleteButton to an empty string
                                    //   $approveButton = $editButton = $deleteButton = '';
                                    // }

                                    if ($status == 'pending') {
                                      $statusBadge = '<span class="badge badge-primary"><i class="fa fa-spinner"></i> PENDING</span>';
                                    } elseif ($status == 'approved') {
                                      $statusBadge = '<span class="badge badge-dark"><i class="fa fa-check"></i> APPROVED</span>';
                                    } else {
                                      $statusBadge = '<span class="badge badge-success"><i class="fa fa-check"></i> DONE</span>';
                                    }

                                    // Check if the current date is different from the previous date
                                    if ($appDate != $currentDate) {
                                      // Display a new row for the date
                                      echo "<tr>";
                                      echo "<th scope='row'>" . $count . "</th>";
                                      echo "<td>" . $formattedDate . "</td>";
                                      echo "<td>" . $formattedTime . "</td>";
                                      echo "<td>" . $patientName . "</td>";
                                      echo "<td>" . $statusBadge . "</td>";
                                      echo "</tr>";

                                      $count++;
                                      $currentDate = $appDate;
                                    } else {
                                      // Display additional time slots for the same date in the same row
                                      echo "<tr>";
                                      echo "<th scope='row'></th>";
                                      echo "<td></td>";
                                      echo "<td>" . $formattedTime . "</td>";
                                      echo "<td>" . $patientName . "</td>";
                                      echo "<td>" . $statusBadge . "</td>";
                                      echo "</tr>";
                                    }
                                  }
                                } else {
                                  echo "<tr>";
                                  echo "<td colspan='5' style='text-align: center; color: red;'>No records found</td>";
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


                      <div class="tab-pane" id="review">

                        <div class="table-responsive">
                          <table class="table table-sm table-bordered">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Review from Patient</th>
                                <th scope="col">Rating from Patient</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $count = 1;
                              $query = "SELECT b.post,b.rating FROM appointment a 
                                      JOIN reviews b ON a.appId = b.appID 
                                      JOIN doctor c ON a.doctorID = c.id 
                                      WHERE c.id='$did' ORDER BY b.id_review DESC";

                              $result = mysqli_query($con, $query);

                              if ($result) {
                                if (mysqli_num_rows($result) > 0) {
                                  while ($row = mysqli_fetch_assoc($result)) {
                                    extract($row);

                                    if ($rating == 5) {
                                      $ratingstar = "5 ★★★★★";
                                    } elseif ($rating == 4) {
                                      $ratingstar = "4 ★★★★";
                                    } elseif ($rating == 3) {
                                      $ratingstar = "3 ★★★";
                                    } elseif ($rating == 2) {
                                      $ratingstar = "2 ★★";
                                    } elseif ($rating == 1) {
                                      $ratingstar = "1 ★";
                                    }

                                    // Display a new row for the review
                                    echo "<tr>";
                                    echo "<th scope='row'>" . $count . "</th>";
                                    echo "<td>" . $post . "</td>";
                                    echo "<td>" . $ratingstar . "</td>";
                                    echo "</tr>";

                                    $count++;
                                  }
                                } else {
                                  echo "<tr>";
                                  echo "<td colspan='3' style='text-align: center; color: red;'>No records found</td>";
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
                </div>
              </div>

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