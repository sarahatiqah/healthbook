<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['adminId'], $_SESSION['password'])) {


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
          $iddoc = $_GET['id'];
          $query = "SELECT a.*,b.name_specialization from doctor a JOIN specialization b WHERE a.specialization=b.id_specialization AND a.id=$iddoc";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);

          ?>
            <div class="row mt-3">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">View Doctor</div>
                    <hr>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id" value="<?php echo $iddoc ?>">
                      <div class="form-group">
                        <label for="input-1">Doctor ID</label>
                        <input type="text" name="doctorId" class="form-control" placeholder="Enter Doctor ID" value="<?php echo $doctorId ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Doctor Name</label>
                        <input type="text" name="doctorName" class="form-control" placeholder="Enter Doctor Name" value="<?php echo $doctorName ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-2">Doctor Email</label>
                        <input type="email" name="doctorEmail" class="form-control" placeholder="Enter Doctor Email Address" value="<?php echo $doctorEmail ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-3">Doctor Mobile</label>
                        <input type="number" name="doctorPhone" class="form-control" placeholder="Enter Doctor Mobile Number" value="<?php echo $doctorPhone ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-3">Doctor Specialization</label>
                        <select class="form-control" name="specialization" disabled>
                          <option value="" selected disabled>Select Specialization</option>

                          <?php
                          $query = "SELECT * FROM specialization";
                          $result = mysqli_query($con, $query);
                          while ($row = mysqli_fetch_assoc($result)) {
                            $id_specialization = $row['id_specialization'];
                            $name_specialization = $row['name_specialization'];

                            // Check if the current faculty value matches the option, if yes, set it as selected
                            $selected = ($specialization == $id_specialization) ? "selected" : "";

                            echo '<option value="' . $id_specialization . '" ' . $selected . '>' . $name_specialization . '</option>';
                          }
                          ?>

                        </select>
                      </div>
                      <div class="form-group">
                        <a href="doctors.php" class="btn btn-secondary px-3">Back</a>
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