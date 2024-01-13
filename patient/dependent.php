<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['id'], $_SESSION['password'])) {


  if (isset($_POST['register'])) {
    $id = clean($_POST['id']);
    $name_dependent = clean($_POST['name_dependent']);
    $relationship = clean($_POST['relationship']);


    $query = "INSERT INTO dependent (name_dependent,relationship,patientId)
          VALUES ('$name_dependent', '$relationship', '$id')";

    if (mysqli_query($con, $query)) {

      $_SESSION['prompt'] = "New dependent registered.";
      header("location:dependent.php");
      exit;
    } else {

      die("Error with the query");
    }
  }


  if (isset($_GET['delete_id'])) {
    $delete_id = clean($_GET['delete_id']);

    try {
      // Perform the delete operation
      $query = "DELETE FROM dependent WHERE id_dependent = '$delete_id'";
      if (mysqli_query($con, $query)) {
        $_SESSION['prompt'] = "Dependent deleted successfully.";
      } else {
        throw new Exception("Error deleting dependent.");
      }
    } catch (Exception $e) {
      // Check if the error is due to a foreign key constraint
      $error = mysqli_error($con);
      if (strpos($error, 'foreign key constraint') !== false) {
        $_SESSION['errprompt'] = "Cannot delete dependent. It is referenced by other records.";
      } else {
        $_SESSION['errprompt'] = $e->getMessage(); // Use the exception message
      }
    }

    header("location: dependent.php");
    exit;
  }
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
          ?>
          <div class="row mt-3">
            <div class="col-lg-4">
              <div class="card">
                <div class="card-body">
                  <div class="card-title">Add Dependent</div>
                  <hr>
                  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $id ?>">
                    <div class="form-group">
                      <label for="input-1">Dependent Name</label>
                      <input type="text" name="name_dependent" class="form-control" placeholder="Enter Dependent Name" required>
                    </div>
                    <div class="form-group">
                      <label for="input-2">Relationship</label>
                      <input type="text" name="relationship" class="form-control" placeholder="Enter Relationship" required>
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
                  <h5 class="card-title">List of Dependents</h5>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Dependent Name</th>
                          <th scope="col">Relationship</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $count = 1;
                        $query = "SELECT * from dependent WHERE  patientId='" . $_SESSION['id'] . "'";

                        $result = mysqli_query($con, $query);

                        if ($result) {
                          if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                              extract($row);
                        ?>
                              <tr>
                                <th scope="row"><?php echo $count; ?></th>
                                <td><?php echo $name_dependent; ?></td>
                                <td><?php echo $relationship; ?></td>
                                <td>
                                  <a href="edit-dependent.php?id=<?php echo $id_dependent; ?>" class="btn btn-warning"><i class="icon-pencil"></i> Edit</a>
                                  <a href="dependent.php?delete_id=<?php echo $id_dependent; ?>" class="btn btn-danger" onclick="return confirmDelete();"><i class="icon-trash"></i> Delete</a>
                                </td>
                              </tr>
                        <?php
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
          return confirm("Are you sure you want to delete this dependent?");
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