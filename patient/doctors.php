<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['id'], $_SESSION['password'])) {

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


            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">List of Doctors</h5>
                  <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="mb-3">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="searchName">Search by Name:</label>
                          <input type="text" class="form-control" name="searchName" id="searchName" value="<?php echo isset($_GET['searchName']) ? htmlspecialchars($_GET['searchName']) : ''; ?>">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="filterSpecialization">Filter by Specialization:</label>
                          <select class="form-control" name="filterSpecialization" id="filterSpecialization">
                            <option value="" <?php echo empty($_GET['filterSpecialization']) ? 'selected' : ''; ?>>All Specializations</option>
                            <?php
                            // Fetch specialization options from the database
                            $specializationQuery = "SELECT * FROM specialization";
                            $specializationResult = mysqli_query($con, $specializationQuery);

                            if ($specializationResult && mysqli_num_rows($specializationResult) > 0) {
                              while ($specializationRow = mysqli_fetch_assoc($specializationResult)) {
                                $specializationId = $specializationRow['id_specialization'];
                                $specializationName = $specializationRow['name_specialization'];
                                $selected = ($_GET['filterSpecialization'] == $specializationId) ? 'selected' : '';

                                echo "<option value='{$specializationId}' {$selected}>{$specializationName}</option>";
                              }
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
                          <th scope="col">#</th>
                          <th scope="col">Name</th>
                          <th scope="col">Specialization</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $count = 1;
                        $query = "SELECT a.*, b.name_specialization, a.id as did FROM doctor a JOIN specialization b ON a.specialization = b.id_specialization";

                        // Apply search by name filter
                        if (!empty($_GET['searchName'])) {
                          $searchName = mysqli_real_escape_string($con, $_GET['searchName']);
                          $query .= " WHERE a.doctorName LIKE '%$searchName%'";
                        }

                        // Apply specialization filter
                        if (!empty($_GET['filterSpecialization'])) {
                          $filterSpecialization = mysqli_real_escape_string($con, $_GET['filterSpecialization']);
                          $query .= (empty($_GET['searchName'])) ? " WHERE" : " AND";
                          $query .= " a.specialization = '$filterSpecialization'";
                        }

                        $result = mysqli_query($con, $query);



                        if ($result) {
                          if (mysqli_num_rows($result) > 0) {
                            $currentDate = null;

                            while ($row = mysqli_fetch_assoc($result)) {
                              extract($row);

                              $viewButton = "<a href='view-slot.php?id={$did}' class='btn btn-light'><i class='icon-eye'></i> View Slot</a> ";
                              $profileButton = "<a href='view-doctor.php?did={$did}' class='btn btn-primary'><i class='icon-eye'></i> View Profile</a>";

                              // Display a new row for the date
                              echo "<tr>";
                              echo "<th scope='row'>" . $count . "</th>";
                              echo "<td>" . $doctorName . "</td>";
                              echo "<td>" . $name_specialization . "</td>";
                              echo "<td>";
                              echo $viewButton;
                              echo $profileButton;
                              echo "</td>";
                              echo "</tr>";

                              $count++;
                            }
                          } else {
                            echo "<tr>";
                            echo "<td colspan='4' style='text-align: center; color: red;'>No records found</td>";
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
          return confirm("Are you sure you want to delete this appointment?");
        }

        function confirmApprove() {
          return confirm("Are you sure you want to approve this appointment?");
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