<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['adminId'], $_SESSION['password'])) {


  if (isset($_POST['save'])) {
    $id = clean($_POST['id']);
    $adminId = clean($_POST['adminId']);
    $adminName = clean($_POST['adminName']);
    $password = clean($_POST['password']);

    // Check if the email is being updated
    $currentAdminIdQuery = "SELECT adminId FROM admin WHERE id = '$id'";
    $currentAdminIdResult = mysqli_query($con, $currentAdminIdQuery);
    $currentAdminIdRow = mysqli_fetch_assoc($currentAdminIdResult);
    $currentAdminId = $currentAdminIdRow['adminId'];

    if ($currentAdminId != $adminId) {
      // AdminId is being updated, check for uniqueness
      $AdminIdCheckQuery = "SELECT adminId FROM admin WHERE adminId = '$adminId'";
      $AdminIdCheckResult = mysqli_query($con, $AdminIdCheckQuery);

      if (mysqli_num_rows($AdminIdCheckResult) > 0) {
        $_SESSION['errprompt'] = "ID Admin already exists.";
        header("location:profile.php");
        exit;
      }
    }

    // Continue with the rest of your update logic
    $updateQuery = "UPDATE admin SET
        adminId = '$adminId',
        adminName = '$adminName',
        password = '$password'
        WHERE id = '$id'";

    if (mysqli_query($con, $updateQuery)) {
      $_SESSION['prompt'] = "Information updated successfully.";
      $_SESSION['adminId'] = $adminId;
      header("location:profile.php");
      exit;
    } else {
      $_SESSION['errprompt'] = "Error updating information: " . mysqli_error($con);
      header("location:profile.php");
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
          ?>
          <div class="row mt-3">


            <div class="col-lg-8">
              <div class="card">
                <div class="card-body">
                  <ul class="nav nav-tabs nav-tabs-primary top-icon nav-justified">
                    <li class="nav-item">
                      <a href="javascript:void();" data-target="#profile" data-toggle="pill" class="nav-link active"><i class="icon-user"></i> <span class="hidden-xs">Profile</span></a>
                      </l i>
                    <li class="nav-item">
                      <a href="javascript:void();" data-target="#edit" data-toggle="pill" class="nav-link"><i class="icon-note"></i> <span class="hidden-xs">Edit</span></a>
                    </li>
                  </ul>
                  <div class="tab-content p-3">
                    <div class="tab-pane active" id="profile">
                      <h5 class="mb-3">My Profile</h5>
                      <div class="row">
                        <div class="col-md-6">
                          <h6>ID Admin: <?php echo $adminId ?></h6>
                          <h6>Name: <?php echo $adminName ?></h6>

                        </div>


                      </div>
                      <!--/row-->
                    </div>


                    <div class="tab-pane" id="edit">
                      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                        <input name="id" type="hidden" value="<?php echo $id ?>">
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">ID Admin</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="adminId" type="text" value="<?php echo $adminId ?>" required>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Name</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="adminName" type="text" value="<?php echo $adminName ?>" required>
                          </div>
                        </div>



                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label">Password</label>
                          <div class="col-lg-9">
                            <input class="form-control" name="password" type="password" value="<?php echo $password ?>" required>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label class="col-lg-3 col-form-label form-control-label"></label>
                          <div class="col-lg-9">
                            <a href="profile.php" class="btn btn-secondary px-3">Cancel</a>
                            <input type="submit" class="btn btn-primary" name="save" value="Save Changes">
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

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