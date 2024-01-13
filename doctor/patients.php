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
          ?>
          <div class="row mt-3">


            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">List of Patients</h5>
                  <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="mb-3">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="searchName">Search by Name:</label>
                          <input type="text" class="form-control" name="searchName" id="searchName" value="<?php echo isset($_GET['searchName']) ? htmlspecialchars($_GET['searchName']) : ''; ?>">
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
                          <th scope="col">IC Number</th>
                          <th scope="col">Name</th>
                          <!-- <th scope="col">Phone</th>
                          <th scope="col">Email</th>
                          <th scope="col">Address</th> -->
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $count = 1;
                        $query = "SELECT * FROM patient";


                        // Apply search by name filter
                        if (!empty($_GET['searchName'])) {
                          $searchName = mysqli_real_escape_string($con, $_GET['searchName']);
                          $query .= " WHERE patientName LIKE '%$searchName%'";
                        }

                        $result = mysqli_query($con, $query);

                        if ($result) {
                          if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                              extract($row);

                              // View button (always defined)
                              $viewButton = "<a href='view-patient.php?id={$id}' class='btn btn-light'><i class='icon-eye'></i> View Profile</a> ";
                              $recordButton = "<a href='view-record.php?id={$id}' class='btn btn-info'><i class='fa fa-book'></i> View Records</a> ";

                              // Display a new row for the date
                              echo "<tr>";
                              echo "<th scope='row'>" . $count . "</th>";
                              echo "<td>" . $icPatient . "</td>";
                              echo "<td>" . $patientName . "</td>";
                              // echo "<td>" . $patientPhone . "</td>";
                              // echo "<td>" . $patientEmail . "</td>";
                              // echo "<td>" . $patientAddress . "</td>";
                              echo "<td>";
                              echo $viewButton;
                              echo $recordButton;
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