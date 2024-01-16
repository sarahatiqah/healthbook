<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['adminId'], $_SESSION['password'])) {


  if (isset($_POST['register'])) {
    $doctorId = clean($_POST['doctorId']);
    $doctorName = clean($_POST['doctorName']);
    $doctorPhone = clean($_POST['doctorPhone']);
    $doctorEmail = clean($_POST['doctorEmail']);
    $password = clean($_POST['password']);
    $specialization = clean($_POST['specialization']);

    $queryEmail = "SELECT doctorEmail FROM doctor WHERE doctorEmail = '$doctorEmail'";
    $resultEmail = mysqli_query($con, $queryEmail);

    $queryPatient = "SELECT patientEmail FROM patient WHERE patientEmail = '$doctorEmail'";
    $resultPatient = mysqli_query($con, $queryPatient);

    $queryStaff = "SELECT staffEmail FROM staff WHERE staffEmail = '$doctorEmail'";
    $resultStaff = mysqli_query($con, $queryStaff);

    if ($_POST['specialization'] == 'other' && !empty($_POST['other_specialization'])) {
      $newSpecialization = clean($_POST['other_specialization']);
      $specCheckQuery = "SELECT id_specialization FROM specialization WHERE name_specialization = '$newSpecialization'";
      $specCheckResult = mysqli_query($con, $specCheckQuery);
    
      if (mysqli_num_rows($specCheckResult) == 0) {
          $insertSpecQuery = "INSERT INTO specialization (name_specialization) VALUES ('$newSpecialization')";
          mysqli_query($con, $insertSpecQuery);
          $specialization = mysqli_insert_id($con);
      } else {
          $specRow = mysqli_fetch_assoc($specCheckResult);
          $specialization = $specRow['id_specialization'];
      }
  } else {
      $specialization = clean($_POST['specialization']);
  }

    if (mysqli_num_rows($resultEmail) == 0 && mysqli_num_rows($resultPatient) == 0 && mysqli_num_rows($resultStaff) == 0) {

      $queryDoctorId = "SELECT doctorId FROM doctor WHERE doctorId = '$doctorId'";
      $resultDoctorId = mysqli_query($con, $queryDoctorId);

      if (mysqli_num_rows($resultDoctorId) == 0) {

        $insertQuery = "INSERT INTO doctor (doctorId, doctorName, doctorEmail, password, doctorPhone, specialization)
                VALUES ('$doctorId', '$doctorName', '$doctorEmail','$password', '$doctorPhone', '$specialization')";

        if (mysqli_query($con, $insertQuery)) {

          $_SESSION['prompt'] = "New account doctor registered.";
          header("location:doctors.php");
          exit;
        } else {

          die("Error with the query");
        }
      } else {

        $_SESSION['errprompt'] = "Doctor ID already exists.";
      }
    } else {

      $_SESSION['errprompt'] = "Email already exists.";
    }
  }

  if (isset($_GET['delete_id'])) {
    $delete_id = clean($_GET['delete_id']);

    try {
      // Perform the delete operation
      $query = "DELETE FROM doctor WHERE id = '$delete_id'";
      if (mysqli_query($con, $query)) {
        $_SESSION['prompt'] = "Doctor deleted successfully.";
      } else {
        throw new Exception("Error deleting doctor.");
      }
    } catch (Exception $e) {
      $_SESSION['errprompt'] = "Cannot delete doctor. It is referenced by other records.";
    }

    header("location: doctors.php");
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
          
            <!-- Add Doctor Modal Trigger Button -->
            <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#addDoctorModal">
              Add Doctor
            </button>

            <!-- Add Doctor Modal -->
            <div class="modal fade" id="addDoctorModal" tabindex="-1" role="dialog" aria-labelledby="addDoctorModalLabel" aria-hidden="true" data-backdrop="static">
              <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content" style="background-image: linear-gradient(45deg, #29323c, #485563); color: #b8c7ce;">
                  <div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                    <h5 class="modal-title" id="addDoctorModalLabel">Add Doctor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="modal-body">
                      <div class="form-group">
                                  <label for="input-1">Doctor ID</label>
                                  <input type="text" name="doctorId" class="form-control" placeholder="Enter Doctor ID" required>
                                </div>
                                <div class="form-group">
                                  <label for="input-1">Doctor Name</label>
                                  <input type="text" name="doctorName" class="form-control" placeholder="Enter Doctor Name" required>
                                </div>
                                <div class="form-group">
                                  <label for="input-2">Doctor Email</label>
                                  <input type="email" name="doctorEmail" class="form-control" placeholder="Enter Doctor Email Address" required>
                                </div>
                                <div class="form-group">
                                  <label for="input-3">Doctor Mobile</label>
                                  <input type="number" name="doctorPhone" class="form-control" placeholder="Enter Doctor Mobile Number" required>
                                </div>
                                <div class="form-group">
                                  <label for="input-3">Doctor Specialization</label>
                                  <select class="form-control" name="specialization" id="specialization" required>
                                    <option value="" selected disabled>Select Specialization</option>

                                    <?php
                                    $query = "SELECT * FROM specialization";
                                    $result = mysqli_query($con, $query);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                      $id_specialization = $row['id_specialization'];
                                      $name_specialization = $row['name_specialization'];

                                      echo '<option value="' . $id_specialization . '">' . $name_specialization . '</option>';
                                    }
                                    ?>
                                    <option value="other">Other</option>
                                  </select>
                                </div>
                                <div class="form-group" style="display:none;" id="other_spec_wrapper">
                                  <label for="other_specialization">New Specialization</label>
                                  <input type="text" class="form-control" name="other_specialization" id="other_specialization" placeholder="Enter new specialization">
                                </div>

                                <div class="form-group">
                                  <label for="input-4">Password</label>
                                  <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
                                </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid rgba(255, 255, 255, 0.2);">
                      <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary" name="register">Submit</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="row mt-3">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">List of Doctors</h5>
                  <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="mb-3">
                    <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="searchId">Search by ID:</label>
                        <input type="text" class="form-control" name="searchId" id="searchId" value="<?php echo isset($_GET['searchId']) ? htmlspecialchars($_GET['searchId']) : ''; ?>">
                      </div>
                    </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="filterSpecialization">Filter by Specialization:</label>
                          <select class="form-control" name="filterSpecialization" id="filterSpecialization">
                            <option value="">All Specializations</option>
                            <?php
                            // Fetch specialization options from the database
                            $specializationQuery = "SELECT * FROM specialization";
                            $specializationResult = mysqli_query($con, $specializationQuery);
                            while ($specializationRow = mysqli_fetch_assoc($specializationResult)) {
                              $specializationId = $specializationRow['id_specialization'];
                              $specializationName = $specializationRow['name_specialization'];
                              $selected = (isset($_GET['filterSpecialization']) && $_GET['filterSpecialization'] == $specializationId) ? 'selected' : '';
                              echo "<option value='{$specializationId}' {$selected}>{$specializationName}</option>";
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                  </form>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                      <thead>
                        <tr>
                          <!-- <th scope="col">#</th> -->
                          <th scope="col">Doctor ID</th>
                          <th scope="col">Doctor Details</th>
                          <th scope="col">Specialization</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        // $count = 1;
                        $query = "SELECT a.*, b.name_specialization FROM doctor a JOIN specialization b ON a.specialization = b.id_specialization";
                        $whereConditions = [];

                        if (!empty($_GET['searchId'])) {
                          $searchId = mysqli_real_escape_string($con, $_GET['searchId']);
                          $whereConditions[] = "a.doctorId LIKE '%$searchId%'";
                        }

                        if (!empty($_GET['filterSpecialization'])) {
                          $filterSpecialization = mysqli_real_escape_string($con, $_GET['filterSpecialization']);
                          $whereConditions[] = "a.specialization = '$filterSpecialization'";
                        }

                        if (!empty($whereConditions)) {
                          $query .= " WHERE " . implode(' AND ', $whereConditions);
                        }

                        $query .= " ORDER BY a.doctorId ASC";

                        $result = mysqli_query($con, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                          while ($row = mysqli_fetch_assoc($result)) {
                            extract($row);
                        ?>
                            <tr>
                              <!-- <th scope="row"><?php echo $count; ?></th> -->
                              <td><?php echo $doctorId; ?></td>
                              <td>
                                <small>Name :</small> <b><?php echo $doctorName; ?></b><br>
                                <small>Phone :</small> <b><?php echo $doctorPhone; ?></b><br>
                                <small>Email :</small> <b><?php echo $doctorEmail; ?></b>
                              </td>
                              <td><?php echo $name_specialization; ?></td>
                              <td>
                                <!-- <button type="submit" class="btn btn-warning"><i class="icon-pencil"></i> Edit</button> -->
                                <a href="view-doctor.php?id=<?php echo $id; ?>" class="btn btn-light"><i class="icon-eye"></i> View</a>
                                <a href="edit-doctor.php?id=<?php echo $id; ?>" class="btn btn-warning"><i class="icon-pencil"></i> Edit</a>
                                <a href="doctors.php?delete_id=<?php echo $id; ?>" class="btn btn-danger" onclick="return confirmDelete();"><i class="icon-trash"></i> Delete</a>
                              </td>
                            </tr>
                        <?php
                            // $count++;
                          }
                        } else {
                          echo "<tr><td colspan='4' style='text-align: center; color: red;'>No records found</td></tr>";
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
          return confirm("Are you sure you want to delete this doctor?");
        }

        document.getElementById('specialization').addEventListener('change', function() {
          var specInputWrapper = document.getElementById('other_spec_wrapper');
          var specInput = document.getElementById('other_specialization');
          
          if (this.value == 'other') {
            specInputWrapper.style.display = 'block';
            specInput.required = true;
          } else {
            specInputWrapper.style.display = 'none';
            specInput.required = false;
          }
        });
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