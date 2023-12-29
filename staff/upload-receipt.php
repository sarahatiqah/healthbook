<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['staffId'], $_SESSION['password'])) {
  $idapp = $_GET['id'];



  if (isset($_POST['save'])) {
    $appID = clean($_POST['id']);
    $targetDirectory = "../patient/receipt/";  // Change this to your desired directory
    $targetFile = $targetDirectory . basename($_FILES["receipt"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if file already exists
    // if (file_exists($targetFile)) {
    //   $_SESSION['errprompt'] = "File already exists.";
    //   header("location:edit-educational.php?id_educational=$ide"); 
    //   exit;
    // }

    // Check file size
    if ($_FILES["receipt"]["size"] > 5000000) {  // Adjust the file size limit if needed
      $_SESSION['errprompt'] = "File is too large. Maximum 5MB";
      header("location:upload-receipt.php?id=$appID");
      exit;
    }

    // Allow certain file formats
    if ($imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx") {
      $_SESSION['errprompt'] = "Only PDF, DOC, and DOCX files are allowed.";
      header("location:upload-receipt.php?id=$appID");
      exit;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
      $_SESSION['errprompt'] = "Error uploading file.";
      header("location:upload-receipt.php?id=$appID");
      exit;
    } else {
      if (move_uploaded_file($_FILES["receipt"]["tmp_name"], $targetFile)) {
        // File uploaded successfully, now update the database
        $updateQuery = "UPDATE appointment SET receipt=? WHERE appId=?";
        $updateStmt = mysqli_prepare($con, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "ss", $targetFile, $appID);

        if (mysqli_stmt_execute($updateStmt)) {
          $_SESSION['prompt'] = "Receipt upload successfully.";
          header("location:appointment.php");
          exit;
        } else {
          $_SESSION['errprompt'] = "Error updating receipt: " . mysqli_error($con);
          header("location:upload-receipt.php?id=$appID");
          exit;
        }
      } else {
        $_SESSION['errprompt'] = "Error uploading file.";
        header("location:upload-receipt.php?id=$appID");
        exit;
      }
    }
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

          $query = "SELECT a.*,b.patientName,b.patientPhone, c.name_dependent,d.* FROM appointment a 
          JOIN patient b 
          JOIN records d
          LEFT JOIN dependent c ON a.dependentID = c.id_dependent
          WHERE a.appId=$idapp AND a.patientID=b.id AND d.appId=$idapp";

          if ($result = mysqli_query($con, $query)) {
            $row = mysqli_fetch_assoc($result);
            extract($row);

            $formattedTime = date('h:i A', strtotime($appTime));

            // Format the date and display the day
            $formattedDate = date('j F Y', strtotime($appDate)) . " (" . date('l', strtotime($appDate)) . ")";
          ?>
            <div class="row mt-3">
              <div class="col-lg-4">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Upload Receipt</div>
                    <hr>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id" value="<?php echo $idapp ?>">
                      <div class="form-group">
                        <label for="input-1">Patient Name</label>
                        <input type="text" class="form-control" value="<?php echo $patientName ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Patient Phone</label>
                        <input type="number" class="form-control" value="<?php echo $patientPhone ?>" disabled>
                      </div>

                      <div class="form-group">
                        <label for="input-1">Patient's Dependent</label>
                        <input type="text" class="form-control" value="<?php echo ($name_dependent ? $name_dependent : "No Dependent") ?>" disabled>
                      </div>

                      <div class="form-group">
                        <label for="input-1">Appointment Date</label>
                        <input type="text" class="form-control" value="<?php echo $formattedDate ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Time Slot</label>
                        <input type="text" class="form-control" value="<?php echo $formattedTime ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Diagnosis</label>
                        <input type="text" class="form-control" value="<?php echo $diagnosis ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Clarification</label>
                        <input type="text" class="form-control" value="<?php echo $clarification ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Clinical Progress</label>
                        <input type="text" class="form-control" value="<?php echo $clinical_progress ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Care Plan</label>
                        <input type="text" class="form-control" value="<?php echo $care_plan ?>" disabled>
                      </div>
                      <div class="form-group">
                        <label for="input-1">Current Receipt: </label>
                        <?php if ($receipt != NULL) { ?>
                          <a href='<?php echo $receipt ?>' target='_blank' class='btn btn-dark'><i class='icon-eye'></i> View</a>
                        <?php  } else {

                          echo "<span style='color: red;'>Receipt not uploaded yet</span>";
                        } ?>

                      </div>
                      <div class="form-group">
                        <label for="input-1">Upload Receipt</label>
                        <input type="file" class="form-control" name="receipt" required>
                      </div>

                      <div class="form-group">
                        <a href="appointment.php" class="btn btn-secondary px-3">Cancel</a>
                        <input type="submit" class="btn btn-primary px-4" name="save" value="Upload">
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