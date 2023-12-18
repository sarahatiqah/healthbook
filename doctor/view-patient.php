<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['doctorId'], $_SESSION['password'])) {



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
          $id = $_GET['id'];
          $query = "SELECT a.*,b.* FROM appointment a JOIN patient b WHERE a.patientID=b.id AND a.patientID=$id";

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
                    <p class="card-text">Address : <?php echo $patientAddress ?></p>

                  </div>

                  <div class="card-body border-top border-light">
                    <div class="media align-items-center">
                      <div class="media-body text-left ml-3">
                        <div class="progress-wrapper">
                          <p>Total Appointment All <span class="float-right"><?php echo $countAppAll ?></span></p>
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
                          <p>Total Appointment With <?php echo $doctorName ?> <span class="float-right"><?php echo $countApp ?></span></p>
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