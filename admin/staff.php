<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['adminId'], $_SESSION['password'])) {

  if (isset($_POST['register'])) {
    $staffId = clean($_POST['staffId']);
    $staffName = clean($_POST['staffName']);
    $staffPhone = clean($_POST['staffPhone']);
    $staffEmail = clean($_POST['staffEmail']);
    $password = clean($_POST['password']);
    $staffAddress = clean($_POST['staffAddress']);

    $queryEmail = "SELECT staffEmail FROM staff WHERE staffEmail = '$staffEmail'";
    $resultEmail = mysqli_query($con, $queryEmail);

    $queryDoctor = "SELECT doctorEmail FROM doctor WHERE doctorEmail = '$staffEmail'";
    $resultDoctor = mysqli_query($con, $queryDoctor);

    $queryPatient = "SELECT patientEmail FROM patient WHERE patientEmail = '$staffEmail'";
    $resultPatient = mysqli_query($con, $queryPatient);

    if (mysqli_num_rows($resultEmail) == 0 && mysqli_num_rows($resultDoctor) == 0 && mysqli_num_rows($resultPatient) == 0) {

      $queryStaffId = "SELECT staffId FROM staff WHERE staffId = '$staffId'";
      $resultStaffId = mysqli_query($con, $queryStaffId);

      if (mysqli_num_rows($resultStaffId) == 0) {

        $insertQuery = "INSERT INTO staff (staffId, staffName, staffEmail, password, staffPhone, staffAddress)
                VALUES ('$staffId', '$staffName', '$staffEmail','$password', '$staffPhone', '$staffAddress')";

        if (mysqli_query($con, $insertQuery)) {

          $_SESSION['prompt'] = "New account staff registered.";
          header("location:staff.php");
          exit;
        } else {

          die("Error with the query");
        }
      } else {

        $_SESSION['errprompt'] = "Staff ID already exists.";
      }
    } else {

      $_SESSION['errprompt'] = "Email already exists.";
    }
  }




  if (isset($_GET['delete_id'])) {
    $delete_id = clean($_GET['delete_id']);

    // Perform the delete operation
    $query = "DELETE FROM staff WHERE id = '$delete_id'";
    if (mysqli_query($con, $query)) {
      $_SESSION['prompt'] = "Staff deleted successfully.";
    } else {
      $_SESSION['errprompt'] = "Error deleting staff.";
    }

    header("location: staff.php");
    exit;
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
            <div class="col-lg-4">
              <div class="card">
                <div class="card-body">
                  <div class="card-title">Add Staff</div>
                  <hr>
                  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                      <label for="input-1">Staff ID</label>
                      <input type="text" name="staffId" class="form-control" placeholder="Enter Staff ID" required>
                    </div>
                    <div class="form-group">
                      <label for="input-1">Staff Name</label>
                      <input type="text" name="staffName" class="form-control" placeholder="Enter Staff Name" required>
                    </div>
                    <div class="form-group">
                      <label for="input-2">Staff Email</label>
                      <input type="email" name="staffEmail" class="form-control" placeholder="Enter Staff Email Address" required>
                    </div>
                    <div class="form-group">
                      <label for="input-3">Staff Mobile</label>
                      <input type="number" name="staffPhone" class="form-control" placeholder="Enter Staff Mobile Number" required>
                    </div>
                    <div class="form-group">
                      <label for="input-3">Staff Address</label>
                      <textarea name="staffAddress" class="form-control" placeholder="Enter Staff Address" required></textarea>
                    </div>
                    <div class="form-group">
                      <label for="input-4">Password</label>
                      <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
                    </div>
                    <div class="form-group">
                      <input type="submit" class="btn btn-primary px-5" name="register" value="Submit">
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="col-lg-8">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">List of Staff</h5>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                      <thead>
                        <tr>
                          <!-- <th scope="col">#</th> -->
                          <th scope="col">Staff ID</th>
                          <th scope="col">Staff Details</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        // $count = 1;
                        $query = "SELECT * from staff ";

                        if ($result = mysqli_query($con, $query)) {
                          while ($row = mysqli_fetch_assoc($result)) {
                            extract($row);
                        ?>
                            <tr>
                              <!-- <th scope="row"><?php echo $count; ?></th> -->
                              <td><?php echo $staffId; ?></td>
                              <td>
                                <small>Name :</small> <b><?php echo $staffName; ?></b><br>
                                <small>Phone :</small> <b><?php echo $staffPhone; ?></b><br>
                                <small>Email :</small> <b><?php echo $staffEmail; ?></b><br>
                                <small>Address :</small> <b><?php echo $staffAddress; ?></b><br>
                              </td>
                              <td>
                                <!-- <button type="submit" class="btn btn-warning"><i class="icon-pencil"></i> Edit</button> -->
                                <a href="view-staff.php?id=<?php echo $id; ?>" class="btn btn-light"><i class="icon-eye"></i> View</a>
                                <a href="edit-staff.php?id=<?php echo $id; ?>" class="btn btn-warning"><i class="icon-pencil"></i> Edit</a>
                                <a href="staff.php?delete_id=<?php echo $id; ?>" class="btn btn-danger" onclick="return confirmDelete();"><i class="icon-trash"></i> Delete</a>
                              </td>
                            </tr>
                        <?php
                            // $count++;
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


      <script>
        function confirmDelete() {
          return confirm("Are you sure you want to delete this staff?");
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