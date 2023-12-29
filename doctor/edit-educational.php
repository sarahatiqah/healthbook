<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['doctorId'], $_SESSION['password'])) {



  if (isset($_POST['save'])) {
    $title = clean($_POST['title']);
    $doctorID = clean($_POST['doctorID']);
    $tags = clean($_POST['tags']);

    // Assuming the educational resource ID is passed as a POST parameter named 'id_educational'
    $ide = clean($_POST['id_educational']);

    // Check if a new file has been uploaded
    if (!empty($_FILES["document"]["name"])) {
      $targetDirectory = "../educational-resources/";  // Change this to your desired directory
      $targetFile = $targetDirectory . basename($_FILES["document"]["name"]);
      $uploadOk = 1;
      $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

      // Check file size
      if ($_FILES["document"]["size"] > 5000000) {  // Adjust the file size limit if needed
        $_SESSION['errprompt'] = "File is too large. Maximum 5MB";
        header("location:edit-educational.php?id_educational=$ide");
        exit;
      }

      // Allow certain file formats
      if ($imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx") {
        $_SESSION['errprompt'] = "Only PDF, DOC, and DOCX files are allowed.";
        header("location:edit-educational.php?id_educational=$ide");
        exit;
      }

      // Check if $uploadOk is set to 0 by an error
      if ($uploadOk == 0) {
        $_SESSION['errprompt'] = "Error uploading file.";
        header("location:edit-educational.php?id_educational=$ide");
        exit;
      } else {
        if (move_uploaded_file($_FILES["document"]["tmp_name"], $targetFile)) {
          // File uploaded successfully, now update the database with the new file path
          $updateQuery = "UPDATE educational SET title=?, document=?, doctorID=?, tags=? WHERE id_educational=?";
          $updateStmt = mysqli_prepare($con, $updateQuery);
          mysqli_stmt_bind_param($updateStmt, "sssss", $title, $targetFile, $doctorID, $tags, $ide);

          if (mysqli_stmt_execute($updateStmt)) {
            $_SESSION['prompt'] = "Educational resource updated successfully.";
            header("location:educational-resources.php");
            exit;
          } else {
            $_SESSION['errprompt'] = "Error updating the resource: " . mysqli_error($con);
            header("location:edit-educational.php?id_educational=$ide");
            exit;
          }
        } else {
          $_SESSION['errprompt'] = "Error uploading file.";
          header("location:edit-educational.php?id_educational=$ide");
          exit;
        }
      }
    } else {
      // No new file uploaded, update the database without changing the file path
      $updateQuery = "UPDATE educational SET title=?, doctorID=?, tags=? WHERE id_educational=?";
      $updateStmt = mysqli_prepare($con, $updateQuery);
      mysqli_stmt_bind_param($updateStmt, "ssss", $title, $doctorID, $tags, $ide);

      if (mysqli_stmt_execute($updateStmt)) {
        $_SESSION['prompt'] = "Educational resource updated successfully.";
        header("location:educational-resources.php");
        exit;
      } else {
        $_SESSION['errprompt'] = "Error updating the resource: " . mysqli_error($con);
        header("location:edit-educational.php?id_educational=$ide");
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
          $id_educational = $_GET['id_educational'];
          $query1 = "SELECT * FROM educational WHERE id_educational=$id_educational";
          $result1 = mysqli_query($con, $query1);
          $row1 = mysqli_fetch_assoc($result1);
          extract($row1);
          ?>
          <div class="row mt-3">
            <div class="col-lg-4">
              <div class="card">
                <div class="card-body">
                  <div class="card-title">Edit Educational Resources</div>
                  <hr>
                  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" onsubmit="return validateForm();" enctype="multipart/form-data">
                    <input type="hidden" name="doctorID" value="<?php echo $did ?>">
                    <input type="hidden" name="id_educational" value="<?php echo $id_educational ?>">
                    <div class="form-group">
                      <label for="input-1">Title</label>
                      <input type="text" class="form-control" name="title" value="<?php echo $title ?>" required>
                    </div>
                    <div class="form-group">
                      <label for="input-1">Current Document</label>
                      <a href='<?php echo $document ?>' target='_blank' class='btn btn-dark'><i class='icon-eye'></i> View</a>
                    </div>
                    <div class="form-group">
                      <label for="input-1">Upload New Educational Resources</label>
                      <input type="file" class="form-control" name="document">
                    </div>
                    <style>
                      .tags-look .tagify__dropdown__item {
                        color: black !important;
                        /* Change the color to your desired color */
                      }
                    </style>

                    <div class="form-group">
                      <label for="input-1">Tags</label>
                      <input type="text" class="form-control" name="tags" id="tags" value="<?php echo htmlspecialchars($tags); ?>" placeholder="Add tags" data-mode="mix">
                      <small style="color:#fff">Note: Separate keywords with a comma, space bar, or enter key</small>
                    </div>
                    <div class="form-group">
                      <a href="educational-resources.php" class="btn btn-secondary px-3">Cancel</a>
                      <input type="submit" class="btn btn-primary px-4" name="save" value="Update" onclick="return confirmUpload()">
                    </div>
                  </form>
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
        // function confirmUpload() {
        //   // Display a confirmation dialog
        //   var confirmed = confirm("Are you sure you want to upload this resources?");

        //   // Return true if the user clicks OK, otherwise, return false
        //   return confirmed;
        // }

        document.addEventListener('DOMContentLoaded', function() {
          var input = document.getElementById('tags');
          var tagify = new Tagify(input, {
            enforceWhitelist: false,
            delimiters: ", ",
            whitelist: ["Diabetes", "Heart Attack", "Flu", "Fever"], // Update the whitelist array
            dropdown: {
              maxItems: 20,
              classname: "tags-look",
              enabled: 0,
            },
          });

          // Get the form element
          var form = document.querySelector('form');

          // Add an event listener for form submission
          form.addEventListener('submit', function(event) {
            // Check if the tags input has at least one tag
            if (tagify.value.length === 0) {
              // Prevent the form from submitting
              event.preventDefault();
              // Display an alert or any other handling for missing tags
              alert('Please add at least one tag.');
            }
          });
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