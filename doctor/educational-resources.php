<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['doctorId'], $_SESSION['password'])) {

  if (isset($_GET['delete_id'])) {
    $delete_id = clean($_GET['delete_id']);

    // Perform the delete operation
    $query = "DELETE FROM educational WHERE id_educational = '$delete_id'";
    if (mysqli_query($con, $query)) {
      $_SESSION['prompt'] = "Educational Resources deleted successfully.";
    } else {
      $_SESSION['errprompt'] = "Error deleting Educational Resources.";
    }

    header("location: educational-resources.php");
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


            <div class="col-lg-12">
              <a href="new-educational.php" class="btn btn-primary px-3 mb-3"><i class='icon-plus'></i> New Educational Resources</a>
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">List of Educational Resources</h5>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Created By</th>
                          <th scope="col">Title</th>
                          <th scope="col">Tags</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $count = 1;
                        $query = "SELECT a.*,b.doctorName,a.doctorID as eduDID FROM educational a JOIN doctor b WHERE a.doctorID=b.id";

                        $result = mysqli_query($con, $query);

                        if ($result) {
                          if (mysqli_num_rows($result) > 0) {

                            while ($row = mysqli_fetch_assoc($result)) {
                              extract($row);

                              // $tagsData = '[{"value":"Diabetes"},{"value":"Flu"},{"value":"Fever"}]';
                              $tagsData = $tags;
                              // Decode the JSON string into a PHP array
                              $tagsArray = json_decode($tagsData, true);



                              $viewButton = "<a href='{$document}' target='_blank' class='btn btn-light'><i class='icon-eye'></i> View Document </a> ";

                              $editButton = "<a href='edit-educational.php?id_educational={$id_educational}' class='btn btn-warning'><i class='icon-eye'></i> Edit</a> ";

                              $deleteButton = "<a href='educational-resources.php?delete_id={$id_educational}' class='btn btn-danger' onclick='return confirmDelete();'><i class='icon-trash'></i> </a>";


                              echo "<tr>";
                              echo "<th scope='row'>" . $count . "</th>";
                              echo "<td>" . $doctorName . "</td>";
                              echo "<td>" . $title . "</td>";
                              if ($tagsArray !== null) {
                                // Iterate through the array and display the values
                                $tagValues = array_column($tagsArray, 'value');
                                echo "<td>" . implode(' ', array_map(function ($tag) {
                                  return '<span class="badge badge-dark">' . $tag . '</span>';
                                }, $tagValues)) . "</td>";
                              } else {
                                echo "Error decoding JSON data.";
                              }
                              echo "<td>";
                              echo $viewButton;
                              if ($eduDID == $did) {
                                echo $editButton;
                                echo $deleteButton;
                              }
                              echo "</td>";
                              echo "</tr>";

                              $count++;
                            }
                          } else {
                            echo "<tr>";
                            echo "<td colspan='5' style='text-align: center; color: red;'>No records found</td>";
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
          return confirm("Are you sure you want to delete this resource?");
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