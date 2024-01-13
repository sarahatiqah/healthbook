<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['id'], $_SESSION['password'])) {

  if (isset($_POST['save'])) {
    $id = clean($_POST['id_dependent']);
    $name_dependent = clean($_POST['name_dependent']);
    $relationship = clean($_POST['relationship']);


    // Continue with the rest of your update logic
    $updateQuery = "UPDATE dependent SET
        name_dependent = '$name_dependent',
        relationship = '$relationship'
        WHERE id_dependent = '$id'";

    if (mysqli_query($con, $updateQuery)) {
      $_SESSION['prompt'] = "Dependent information updated successfully.";
      header("location:dependent.php");
      exit;
    } else {
      $_SESSION['errprompt'] = "Error updating dependent information: " . mysqli_error($con);
      header("location:edit-dependent.php?id=" . $id);
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
          $iddependent = $_GET['id'];
          $query = "SELECT * from dependent WHERE id_dependent=$iddependent";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);

          ?>
            <div class="row mt-3">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Edit Dependent</div>
                    <hr>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id_dependent" value="<?php echo $iddependent ?>">
                      <div class="form-group">
                        <label for="input-1">Dependent Name</label>
                        <input type="text" name="name_dependent" class="form-control" placeholder="Enter Dependent Name" value="<?php echo $name_dependent ?>" required>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Relationship</label>
                        <input type="text" name="relationship" class="form-control" placeholder="Enter Relationship" value="<?php echo $relationship ?>" required>
                      </div>

                      <div class="form-group">
                        <a href="dependent.php" class="btn btn-secondary px-3">Cancel</a>
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