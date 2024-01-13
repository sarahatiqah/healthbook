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
          $query = "SELECT * from staff WHERE id=$iddoc";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);

          ?>
            <div class="row mt-3">
              <div class="col-lg-4">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">View Staff</div>
                    <hr>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id" value="<?php echo $iddoc ?>">
                      <div class="form-group">
                        <label for="input-1">Staff ID</label>
                        <input type="text" name="staffId" class="form-control" placeholder="Enter Staff ID" value="<?php echo $staffId ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Staff Name</label>
                        <input type="text" name="staffName" class="form-control" placeholder="Enter Staff Name" value="<?php echo $staffName ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-2">Staff Email</label>
                        <input type="email" name="staffEmail" class="form-control" placeholder="Enter Staff Email Address" value="<?php echo $staffEmail ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-3">Staff Mobile</label>
                        <input type="number" name="staffPhone" class="form-control" placeholder="Enter Staff Mobile Number" value="<?php echo $staffPhone ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-3">Staff Address</label>
                        <textarea name="staffAddress" class="form-control" placeholder="Enter Staff Address" disabled><?php echo $staffAddress ?></textarea>
                      </div>
                      <div class="form-group">
                        <a href="staff.php" class="btn btn-secondary px-3">Back</a>
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