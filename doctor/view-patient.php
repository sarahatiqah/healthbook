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
          $id = $_GET['id'];
          $query = "SELECT * FROM patient WHERE id=$id";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);


            $query1 = "SELECT COUNT(*) as total FROM appointment WHERE patientID=$id";
            $result1 = mysqli_query($con, $query1);
            $row1 = mysqli_fetch_assoc($result1);
            $countAppAll = $row1['total'];

            $query2 = "SELECT COUNT(*) as total FROM appointment WHERE patientID=$id AND doctorID=$did";
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
                    <img src="../assets/images/profile.jpg" class="profile">
                    <h5 class="card-title"><?php echo $patientName ?></h5>
                    <p class="card-text">IC Number : <?php echo $icPatient ?></p>
                    <p class="card-text">Phone Number : <?php echo $patientPhone ?></p>
                    <p class="card-text">Email : <?php echo $patientEmail ?></p>
                    <p class="card-text">Gender : <?php echo $patientGender ?></p>
                    <p class="card-text">Race : <?php echo $patientRace ?></p>
                    <p class="card-text">Address : <?php echo $patientAddress ?></p>

                  </div>

                  <div class="card-body border-top border-light">
                    <div class="media align-items-center">
                      <div class="media-body text-left ml-3">
                        <div class="progress-wrapper">
                          <p>All Appointment <span class="float-right"><?php echo $countAppAll ?></span></p>
                          <!-- <div class="progress" style="height: 5px;">
                            <div class="progress-bar" style="width:65%"></div>
                          </div> -->
                        </div>
                      </div>
                    </div>
                    <hr>
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
                        <a href="javascript:void();" data-target="#dependent" data-toggle="pill" class="nav-link active"><i class="fa fa-users"></i> <span class="hidden-xs">Dependents</span></a>
                      </li>
                      <li class="nav-item">
                        <a href="javascript:void();" data-target="#totalAppAll" data-toggle="pill" class="nav-link "><i class="fa fa-calendar"></i> <span class="hidden-xs">Total Appointment All</span></a>
                      </li>
                      <li class="nav-item">
                        <a href="javascript:void();" data-target="#totalApp" data-toggle="pill" class="nav-link"><i class="fa fa-user-md"></i> <span class="hidden-xs">Total Appointment With <?php echo $doctorName ?></span></a>
                      </li>
                    </ul>
                    <div class="tab-content p-3">


                      <div class="tab-pane active" id="dependent">
                        <div class="table-responsive">
                          <table class="table table-sm table-bordered">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Dependent's Name</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $count = 1;
                              $query = "SELECT a.name_dependent, b.patientName
                              FROM dependent a 
                              JOIN patient b ON a.patientId = b.id
                              WHERE a.patientID='$id'";

                              if ($result = mysqli_query($con, $query)) {


                                if (mysqli_num_rows($result) > 0) {
                                  while ($row = mysqli_fetch_assoc($result)) {
                                    extract($row);

                                    // Display a new row for the date
                                    echo "<tr>";
                                    echo "<th scope='row'>" . $count . "</th>";
                                    echo "<td>" . $name_dependent . "</td>";
                                    echo "</tr>";

                                    $count++;
                                  }
                                } else {
                                  echo "<tr>";
                                  echo "<td colspan='2' style='text-align: center; color: red;'>No records found</td>";
                                  echo "</tr>";
                                }
                              } else {
                                die("Error with the query in the database");
                              }
                              ?>
                            </tbody>
                          </table>

                        </div>
                        <!--/row-->
                      </div>





                      <div class="tab-pane " id="totalAppAll">
                        <div class="table-responsive">
                          <table class="table table-sm table-bordered">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Date</th>
                                <th scope="col">Time</th>
                                <th scope="col">Patient</th>
                                <th scope="col">Doctor Details</th>
                                <th scope="col">Status</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $count = 1;
                              $query = "SELECT a.*, b.patientName, c.doctorName as dname, d.name_specialization 
                              FROM appointment a 
                              JOIN patient b ON a.patientID = b.id 
                              JOIN doctor c ON a.doctorID = c.id
                              JOIN specialization d ON c.specialization = d.id_specialization
                              WHERE a.patientID='$id'
                              ORDER BY a.appDate DESC, a.appTime";

                              if ($result = mysqli_query($con, $query)) {
                                $currentDate = null;

                                if (mysqli_num_rows($result) > 0) {
                                  while ($row = mysqli_fetch_assoc($result)) {
                                    extract($row);

                                    // Format the time in 12-hour format
                                    $formattedTime = date('h:i A', strtotime($appTime));

                                    // Format the date and display the day
                                    $formattedDate = date('j F Y', strtotime($appDate)) . "<br><b>" . date('l', strtotime($appDate)) . "</b>";

                                    // View button (always defined)
                                    $viewButton = "<a href='view-patient.php?id={$patientId}' class='btn btn-light'><i class='icon-eye'></i> View</a>";

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
                                      echo "<td>" . $dname . "<br>(" . $name_specialization . ")</td>";
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
                                      echo "<td>" . $dname . "<br>(" . $name_specialization . ")</td>";
                                      echo "<td>" . $statusBadge . "</td>";
                                      echo "</tr>";
                                    }
                                  }
                                } else {
                                  echo "<tr>";
                                  echo "<td colspan='6' style='text-align: center; color: red;'>No records found</td>";
                                  echo "</tr>";
                                }
                              } else {
                                die("Error with the query in the database");
                              }
                              ?>
                            </tbody>
                          </table>

                        </div>
                        <!--/row-->
                      </div>
                      <div class="tab-pane" id="totalApp">

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
              WHERE a.doctorID='" . $_SESSION['id'] . "' AND a.patientID='$id'
              ORDER BY a.appDate DESC, a.appTime";

                              $result = mysqli_query($con, $query);

                              if ($result) {
                                $currentDate = null;

                                if (mysqli_num_rows($result) > 0) {
                                  while ($row = mysqli_fetch_assoc($result)) {
                                    extract($row);

                                    // Format the time in 12-hour format
                                    $formattedTime = date('h:i A', strtotime($appTime));

                                    // Format the date and display the day
                                    $formattedDate = date('j F Y', strtotime($appDate)) . "<br><b>" . date('l', strtotime($appDate)) . "</b>";

                                    // View button (always defined)
                                    $viewButton = "<a href='view-patient.php?id={$patientId}' class='btn btn-light'><i class='icon-eye'></i> View</a> ";

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

                                      echo "</td>";
                                      echo "</tr>";
                                    }
                                  }
                                } else {
                              ?>
                                  <tr>
                                    <td colspan="5" style="text-align: center; color: red;">No records found</td>
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