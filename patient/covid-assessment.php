<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['id'], $_SESSION['password'])) {

  // Insertion logic
  if (isset($_POST['save'])) {
    $table = 'assessment_data';

    $data = array(
      'symptoms' => isset($_POST['checkSymptoms']) ? mysqli_real_escape_string($con, $_POST['checkSymptoms']) : '',
      'type_of_symptoms' => isset($_POST['type_of_symptoms']) ? implode(', ', array_map(
        function ($value) use ($con) {
          return mysqli_real_escape_string($con, $value);
        },
        $_POST['type_of_symptoms']
      )) : '',
      'contact' => isset($_POST['contact']) ? mysqli_real_escape_string($con, $_POST['contact']) : '',
      'travel' => isset($_POST['travel']) ? mysqli_real_escape_string($con, $_POST['travel']) : '',
      'exposure' => isset($_POST['exposure']) ? mysqli_real_escape_string($con, $_POST['exposure']) : '',
      'hygiene' => isset($_POST['hygiene']) ? mysqli_real_escape_string($con, $_POST['hygiene']) : '',
      'symptom_duration' => isset($_POST['symptomDuration']) ? mysqli_real_escape_string($con, $_POST['symptomDuration']) : '',
      'assessmentResult' => isset($_POST['assessmentResult']) ? mysqli_real_escape_string($con, $_POST['assessmentResult']) : '',
      'patientID' => isset($_SESSION['id']) ? mysqli_real_escape_string($con, $_SESSION['id']) : '',
    );

    // Build the SQL query
    $columns = implode(", ", array_keys($data));
    $values = "'" . implode("', '", $data) . "'";
    $query = "INSERT INTO $table ($columns) VALUES ($values)";

    // Perform the insertion
    if (mysqli_query($con, $query)) {
      $_SESSION['prompt'] = "Data covid assessment inserted successfully.";
    } else {
      $_SESSION['errprompt'] = "Error inserting data: " . mysqli_error($con);
    }

    // Redirect or perform other actions after insertion
    header("location: covid.php");
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
            <div class="col-lg-5">
              <div class="card">
                <div class="card-body">
                  <div class="card-title">COVID-19 Self-Assessment</div>
                  <hr>
                  <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" id="covidAssessmentForm" onsubmit="submitForm(event)">
                    <input type="hidden" name="assessmentResult" id="assessmentResultInput">
                    <div class="form-group">
                      <label for="symptoms">Do you have symptoms?</label><br>
                      <input type="radio" name="checkSymptoms" id="checkSymptomsYes" value="yes" required onclick="toggleSymptomsSection()"> Yes <br>
                      <input type="radio" name="checkSymptoms" id="checkSymptomsNo" value="no" required onclick="toggleSymptomsSection()"> No
                      <div class="error-message" id="checkSymptomsError"></div>
                    </div>

                    <div class="form-group" id="typeOfSymptomsSection" style="display: none;">
                      <label for="symptoms">Type of symptoms?</label><br>
                      <input type="checkbox" name="type_of_symptoms[]" value="fever"> Fever <br>
                      <input type="checkbox" name="type_of_symptoms[]" value="cough"> Cough <br>
                      <input type="checkbox" name="type_of_symptoms[]" value="shortnessOfBreath"> Shortness of breath
                      <div class="error-message" id="symptomsError"></div>
                    </div>

                    <div class="form-group">
                      <label for="contact">Have you been in close contact with someone who tested positive for COVID-19?</label><br>
                      <input type="radio" name="contact" value="yes" required> Yes <br>
                      <input type="radio" name="contact" value="no" required> No
                      <div class="error-message" id="contactError"></div>
                    </div>

                    <div class="form-group">
                      <label for="travel">Have you traveled to a high-risk area in the last 14 days?</label><br>
                      <input type="radio" name="travel" value="yes" required> Yes <br>
                      <input type="radio" name="travel" value="no" required> No
                      <div class="error-message" id="travelError"></div>
                    </div>

                    <div class="form-group">
                      <label for="exposure">Have you been exposed to crowded places recently?</label><br>
                      <input type="radio" name="exposure" value="yes" required> Yes <br>
                      <input type="radio" name="exposure" value="no" required> No
                      <div class="error-message" id="exposureError"></div>
                    </div>

                    <div class="form-group">
                      <label for="hygiene">Are you practicing good hygiene, including frequent handwashing?</label><br>
                      <input type="radio" name="hygiene" value="yes" required> Yes <br>
                      <input type="radio" name="hygiene" value="no" required> No
                      <div class="error-message" id="hygieneError"></div>
                    </div>

                    <div class="form-group">
                      <label for="symptomDuration">How long have you experienced symptoms (if any)?</label><br>
                      <select name="symptomDuration" class="form-control" required>
                        <option value="Not Applicable">Not Applicable</option>
                        <option value="1-3 days">1-3 days</option>
                        <option value="4-7 days">4-7 days</option>
                        <option value="More than 7 days">More than 7 days</option>
                      </select>
                      <div class="error-message" id="symptomDurationError"></div>
                    </div>


                    <div class="form-group">
                      <input type="submit" class="btn btn-primary px-5" name="save" value="Submit">
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
        // Add event listener to the radio buttons
        document.getElementById('checkSymptomsYes').addEventListener('change', toggleSymptomsSection);
        document.getElementById('checkSymptomsNo').addEventListener('change', toggleSymptomsSection);

        // function toggleSymptomsSection() {
        //   var typeOfSymptomsSection = document.getElementById('typeOfSymptomsSection');
        //   var feverCheckbox = document.querySelector('input[name="type_of_symptoms[]"][value="fever"]');
        //   var coughCheckbox = document.querySelector('input[name="type_of_symptoms[]"][value="cough"]');
        //   var shortnessOfBreathCheckbox = document.querySelector('input[name="type_of_symptoms[]"][value="shortnessOfBreath"]');

        //   if (!document.getElementById('checkSymptomsYes').checked) {
        //     feverCheckbox.checked = false;
        //     coughCheckbox.checked = false;
        //     shortnessOfBreathCheckbox.checked = false;
        //   }

        //   typeOfSymptomsSection.style.display = document.getElementById('checkSymptomsYes').checked ? 'block' : 'none';
        // }


        function toggleSymptomsSection() {
          // Get the typeOfSymptomsSection element
          var typeOfSymptomsSection = document.getElementById('typeOfSymptomsSection');

          // Check the value of the radio button
          var checkSymptomsYes = document.getElementById('checkSymptomsYes').checked;

          // Toggle the display property based on the radio button's checked state
          typeOfSymptomsSection.style.display = checkSymptomsYes ? 'block' : 'none';

          // Clear the checkboxes when "No" is selected
          if (!checkSymptomsYes) {
            clearSymptomCheckboxes();
          }
        }

        function clearSymptomCheckboxes() {
          // Get all checkboxes for symptoms
          var symptomCheckboxes = document.querySelectorAll('input[name="type_of_symptoms[]"]');

          // Uncheck all checkboxes
          symptomCheckboxes.forEach(function(checkbox) {
            checkbox.checked = false;
          });
        }

        function submitForm(event) {
          clearErrorMessages();
          var symptoms = Array.from(document.querySelectorAll('input[name="type_of_symptoms[]"]:checked')).map(symptom => symptom.value);
          var contact = document.querySelector('input[name="contact"]:checked') ? document.querySelector('input[name="contact"]:checked').value : '';
          var travel = document.querySelector('input[name="travel"]:checked') ? document.querySelector('input[name="travel"]:checked').value : '';
          var exposure = document.querySelector('input[name="exposure"]:checked') ? document.querySelector('input[name="exposure"]:checked').value : '';
          var hygiene = document.querySelector('input[name="hygiene"]:checked') ? document.querySelector('input[name="hygiene"]:checked').value : '';
          var symptomDuration = document.querySelector('select[name="symptomDuration"]').value;

          var isValid = validateForm(symptoms, contact, travel, exposure, hygiene, symptomDuration);

          if (isValid) {
            var result = assessCOVID(symptoms, contact, travel, exposure, hygiene, symptomDuration);
            document.getElementById('assessmentResultInput').value = result; // Set the value of the hidden input
            // Continue with form submission
          } else {
            event.preventDefault(); // Prevent the form from submitting if validation fails
          }
        }

        function clearErrorMessage(id) {
          document.getElementById(id).innerText = '';
        }

        function validateForm(symptoms, contact, travel, exposure, hygiene, symptomDuration) {
          var isValid = true;

          // Validate Symptoms only if the user has symptoms
          var checkSymptomsYes = document.getElementById('checkSymptomsYes').checked;
          if (checkSymptomsYes && symptoms.length === 0) {
            document.getElementById('symptomsError').innerText = 'Please select at least one symptom.';
            document.getElementById('symptomsError').style.color = 'red';
            isValid = false;
          }

          // Validate Contact
          if (!contact) {
            document.getElementById('contactError').innerText = 'Please select an option.';
            isValid = false;
          }

          // Validate Travel
          if (!travel) {
            document.getElementById('travelError').innerText = 'Please select an option.';
            isValid = false;
          }

          // Validate Exposure
          if (!exposure) {
            document.getElementById('exposureError').innerText = 'Please select an option.';
            isValid = false;
          }

          // Validate Hygiene
          if (!hygiene) {
            document.getElementById('hygieneError').innerText = 'Please select an option.';
            isValid = false;
          }

          // Validate Symptom Duration
          // if (symptomDuration === 'none') {
          //   document.getElementById('symptomDurationError').innerText = 'Please select a duration.';
          //   document.getElementById('symptomDurationError').style.color = 'red'; // Set error message color to red
          //   isValid = false;
          // }

          return isValid;
        }


        function clearErrorMessages() {
          document.getElementById('symptomsError').innerText = '';
          document.getElementById('contactError').innerText = '';
          document.getElementById('travelError').innerText = '';
          document.getElementById('exposureError').innerText = '';
          document.getElementById('hygieneError').innerText = '';
          document.getElementById('symptomDurationError').innerText = '';
        }

        function assessCOVID(symptoms, contact, travel, exposure, hygiene, symptomDuration) {
          // Check if the user has selected specific symptoms or had close contact
          if (symptoms.length > 0 || contact === 'yes') {
            // Check for specific symptoms
            var specificSymptoms = symptoms.join(', ');
            // return `Based on your responses, it is recommended to consult with a healthcare professional and consider getting tested for COVID-19. You mentioned experiencing: ${specificSymptoms}.`;
            return `Based on your responses, it is recommended to consult with a healthcare professional and consider getting tested for COVID-19.`;
          } else {
            // Check for other factors
            if (travel === 'yes' && exposure === 'yes') {
              return 'Based on your responses, you have traveled to a high-risk area and have been exposed to crowded places. It is recommended to monitor your health and consider getting tested.';
            } else if (travel === 'yes') {
              return 'Based on your responses, you have traveled to a high-risk area. It is recommended to monitor your health and practice self-isolation.';
            } else if (exposure === 'yes') {
              return 'Based on your responses, you have been exposed to crowded places. It is recommended to monitor your health and practice self-isolation.';
            } else {
              return 'Based on your responses, you are not exhibiting significant symptoms or risk factors. Continue to practice good hygiene and monitor your health.';
            }
          }
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