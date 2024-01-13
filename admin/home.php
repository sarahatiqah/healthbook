<?php
ob_start(); // Start output buffering
session_start();

require '../dbconnection.php';
require '../functions.php';
// Include the TCPDF library
require_once('../assets/TCPDF/tcpdf.php');

if (isset($_SESSION['adminId'], $_SESSION['password'])) {
?>
  <!DOCTYPE html>
  <html lang="en">

  <?php include "head.php"; ?>

  <body class="bg-theme bg-theme2">

    <!-- Start wrapper-->
    <div id="wrapper">

      <?php
      include "sidebar.php";
      include "header.php";

      $query = "SELECT COUNT(*) as total FROM appointment WHERE status='done'";
      $result = mysqli_query($con, $query);
      $row = mysqli_fetch_assoc($result);
      $countAppointmentDone = $row['total'];

      $query2 = "SELECT COUNT(*) as total FROM appointment";
      $result2 = mysqli_query($con, $query2);
      $row2 = mysqli_fetch_assoc($result2);
      $countAppointment = $row2['total'];

      $query3 = "SELECT COUNT(*) as total FROM appointment WHERE status='approved'";
      $result3 = mysqli_query($con, $query3);
      $row3 = mysqli_fetch_assoc($result3);
      $countAppointmentApproved = $row3['total'];

      $query4 = "SELECT COUNT(*) as total FROM appointment WHERE status='pending'";
      $result4 = mysqli_query($con, $query4);
      $row4 = mysqli_fetch_assoc($result4);
      $countAppointmentPending = $row4['total'];

      $query5 = "SELECT COUNT(*) as total FROM patient ";
      $result5 = mysqli_query($con, $query5);
      $row5 = mysqli_fetch_assoc($result5);
      $countPatients = $row5['total'];

      $query6 = "SELECT COUNT(*) as total FROM doctor";
      $result6 = mysqli_query($con, $query6);
      $row6 = mysqli_fetch_assoc($result6);
      $countDoctor = $row6['total'];

      $query7 = "SELECT COUNT(*) as total FROM staff";
      $result7 = mysqli_query($con, $query7);
      $row7 = mysqli_fetch_assoc($result7);
      $countStaff = $row7['total'];

      $query8 = "SELECT COUNT(*) as total FROM dependent";
      $result8 = mysqli_query($con, $query8);
      $row8 = mysqli_fetch_assoc($result8);
      $countDependents = $row8['total'];

      $queryMale = "SELECT COUNT(*) as total FROM patient WHERE patientGender='Male'";
      $resultMale = mysqli_query($con, $queryMale);
      $rowMale = mysqli_fetch_assoc($resultMale);
      $countMale = $rowMale['total'];

      $queryFemale = "SELECT COUNT(*) as total FROM patient WHERE patientGender='Female'";
      $resultFemale = mysqli_query($con, $queryFemale);
      $rowFemale = mysqli_fetch_assoc($resultFemale);
      $countFemale = $rowFemale['total'];


      function generatePDF()
      {
        // Use global $con to access the database connection
        global $con;

        // Create a new TCPDF instance
        $pdf = new TCPDF();

        // Add title under the top line


        // Add a landscape-oriented page
        $pdf->AddPage('P');

        // Set font
        $dateExport = $_POST['dateExport'];
        $yearExport = $_POST['yearExport'];
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Report for HealthBook Patients on ' . $dateExport . '/' . $yearExport, 0, 1, 'C');
        $pdf->SetCreator('HealthBook');
        $pdf->SetAuthor('HealthBook');
        $pdf->SetTitle('Report HealthBook');
        $pdf->SetSubject('Report HealthBook');
        $pdf->SetKeywords('Report, PDF, HealthBook');
        // $dateExport = $_POST['dateExport'];

        $query = "SELECT 
        COUNT(DISTINCT appId) AS totalAppCount,
        SUM(CASE WHEN appCount = 1 THEN 1 ELSE 0 END) AS newPatient,
        SUM(CASE WHEN appCount > 1 THEN 1 ELSE 0 END) AS repeatPatient,
        appDate
    FROM (
        SELECT 
            MIN(appId) AS appId,
            patientId,
            appDate,
            (SELECT COUNT(appId) FROM appointment WHERE patientId = subquery.patientId) AS appCount
        FROM 
            appointment subquery
        WHERE 
            MONTH(appDate) = $dateExport AND YEAR(appDate) = $yearExport AND status='done'
        GROUP BY 
            patientId, appDate
    ) AS subquery
    GROUP BY 
        appDate";



        $result = $con->query($query);

        // Set auto page break mode to true
        $pdf->SetAutoPageBreak(true, 10);

        // Process data and add to PDF
        if ($result->num_rows > 0) {
          // Define the table headers
          $pdf->SetFont('helvetica', 'B', 12);
          // Set document information to empty strings

          // Set styles for table headers

          $pdf->SetFillColor(242, 242, 242);
          $pdf->SetTextColor(0);
          $pdf->SetDrawColor(0, 0, 0); // Set the border color to black
          $pdf->SetLineWidth(0.2); // Set the border width
          $pdf->SetX(10);
          $pdf->SetFont('helvetica', 'B', 12);
          $pdf->Cell(35, 10, 'Total Patients', 1, 0, 'C', 1);
          $pdf->Cell(55, 10, 'Appointment Date', 1, 0, 'C', 1);
          $pdf->Cell(45, 10, 'New Patients', 1, 0, 'C', 1);
          $pdf->Cell(45, 10, 'Repeat Patients', 1, 0, 'C', 1);
          $pdf->Ln(); // Move to the next line

          // Set Y position for the title
          $pdf->SetY(30); // Adjusted Y position


          // Reset font for the data rows
          $pdf->SetFont('helvetica', '', 12);

          while ($row = $result->fetch_assoc()) {
            // $formattedTime = date('h:i A', strtotime($row['appTime']));

            // Format the date and display the day
            $formattedDate = date('d/m/Y', strtotime($row['appDate']));
            $formattedDate = date('d/m/Y', strtotime($row['appDate'])) . " (" . date('l', strtotime($row['appDate'])) . ")";
            // Add data to the PDF table
            $pdf->SetX(10);
            $pdf->Cell(35, 10, $row['totalAppCount'], 1, 0, 'C');
            $pdf->Cell(55, 10, $formattedDate, 1, 0, 'C');
            $pdf->Cell(45, 10, $row['newPatient'], 1, 0, 'C'); // Add border to this cell
            $pdf->Cell(45, 10, $row['repeatPatient'], 1, 0, 'C');
            $pdf->Ln(); // Move to the next line
          }
        } else {
          // $_SESSION['prompt'] = "No record on this month.";
          // header("location: home.php");

          echo "<script type = \"text/javascript\">
                                    alert(\"No record on this month and year.\");
                                    window.location = (\"home.php\")
                                    </script>";
        }

        // Output the PDF to the browser
        $pdf->Output('exported_report.pdf', 'I');
      }


      if (isset($_GET['export'])) {
        ob_end_clean(); // Discard the output buffer
        generatePDF();
      }

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
          <!--Start Dashboard Content-->

          <form method="post" action="?export=true">
            <div class="d-flex justify-content-end">

              <select name="dateExport" class="btn btn-light px-3 mb-3 mr-2">
                <option value="01">January</option>
                <option value="02">February</option>
                <option value="03">March</option>
                <option value="04">April</option>
                <option value="05">May</option>
                <option value="06">June</option>
                <option value="07">July</option>
                <option value="08">August</option>
                <option value="09">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
              </select>
              <select name="yearExport" class="btn btn-light px-3 mb-3 mr-2">
                <option value="2023">2023</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
                <option value="2026">2026</option>
                <option value="2027">2027</option>
                <option value="2028">2028</option>
                <option value="2029">2029</option>
                <option value="2030">2030</option>
                <option value="2031">2031</option>
                <option value="2032">2032</option>
                <option value="2033">2033</option>
                <option value="2034">2034</option>
              </select>
              <button type="submit" class="btn btn-primary px-3 mb-3"><i class='icon-print'></i> Export Report</button>
            </div>
          </form>

          <div class="card mt-3">


            <div class="card-content">
              <div class="row row-group m-0">
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                  <div class="card-body">
                    <h5 class="text-white mb-0"><?php echo $countAppointment ?> <span class="float-right"><i class="zmdi zmdi-calendar"></i></span></h5>

                    <p class="mb-0 text-white small-font">Total Appointment</p>
                  </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                  <div class="card-body">
                    <h5 class="text-white mb-0"><?php echo $countAppointmentDone ?> <span class="float-right"><i class="zmdi zmdi-badge-check"></i></span></h5>

                    <p class="mb-0 text-white small-font">Total Done Appointment</p>
                  </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                  <div class="card-body">
                    <h5 class="text-white mb-0"><?php echo $countAppointmentApproved ?> <span class="float-right"><i class="zmdi zmdi-case-check"></i></span></h5>

                    <p class="mb-0 text-white small-font">Total Approved Appointment</p>
                  </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                  <div class="card-body">
                    <h5 class="text-white mb-0"><?php echo $countAppointmentPending ?> <span class="float-right"><i class="zmdi zmdi-spinner"></i></span></h5>

                    <p class="mb-0 text-white small-font">Total Pending Appointment</p>
                  </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                  <div class="card-body">
                    <h5 class="text-white mb-0"><?php echo $countStaff ?> <span class="float-right"><i class="fa fa-user"></i> </span></h5>

                    <p class="mb-0 text-white small-font">Total Staff</p>
                  </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                  <div class="card-body">
                    <h5 class="text-white mb-0"><?php echo $countDoctor ?> <span class="float-right"><i class="fa fa-user-md"></i> </span></h5>

                    <p class="mb-0 text-white small-font">Total Doctor</p>
                  </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                  <div class="card-body">
                    <h5 class="text-white mb-0"><?php echo $countPatients ?> <span class="float-right"><i class="fa fa-users"></i></span></h5>

                    <p class="mb-0 text-white small-font">Total Patients</p>
                  </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3 border-light">
                  <div class="card-body">
                    <h5 class="text-white mb-0"><?php echo $countDependents ?> <span class="float-right"><i class="fa fa-users"></i></span></h5>

                    <p class="mb-0 text-white small-font">Total Dependents</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12 col-lg-4 col-xl-4">
              <div class="card">
                <div class="card-header">Number of Patient by Gender
                </div>
                <div class="card-body">
                  <div class="chart-container-2">
                    <canvas id="chart2"></canvas>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table align-items-center">
                    <tbody>
                      <tr>
                        <td><i class="fa fa-circle mr-2" style="color:#42FF33"></i> Male</td>
                        <td><?php echo $countMale ?></td>
                      </tr>
                      <tr>
                        <td><i class="fa fa-circle mr-2" style="color:#EC33FF"></i> Female</td>
                        <td><?php echo $countFemale ?></td>
                      </tr>

                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="col-12 col-lg-8 col-xl-8">
              <div class="card">
                <div class="card-header">Number of Patients by Race
                </div>
                <div class="card-body">
                  <!-- <ul class="list-inline">
                    <li class="list-inline-item"><i class="fa fa-circle mr-2 text-white"></i>New Visitor</li>
                    <li class="list-inline-item"><i class="fa fa-circle mr-2 text-light"></i>Old Visitor</li>
                  </ul> -->
                  <div style="width: 100%; margin: auto;">
                    <canvas id="myBarChart"></canvas>
                  </div>
                </div>

              </div>
            </div>
          </div><!--End Row-->

          <div class="row">
            <div class="col-12 col-lg-12">
              <div class="card">
                <div class="card-header">Number of Appointment by Doctor
                </div>
                <div class="table-responsive">
                  <div class="table-responsive">
                    <table class="table align-items-center table-flush table-borderless">
                      <thead>
                        <tr>
                          <th>Doctor ID</th>
                          <th>Name</th>
                          <th>Specialization</th>
                          <th>Number of Appointments</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $queryDoctor = "SELECT COUNT(a.doctorID) as amount, a.doctorID, b.doctorName, c.name_specialization
                        FROM appointment a
                        JOIN doctor b ON a.doctorID = b.id
                        JOIN specialization c ON b.specialization = c.id_specialization
                        GROUP BY a.doctorID, b.doctorName, c.name_specialization ORDER BY amount DESC";
                        $resultDoctor = mysqli_query($con, $queryDoctor);

                        while ($rowDoctor = mysqli_fetch_assoc($resultDoctor)) {
                        ?>
                          <tr>
                            <td><?php echo $rowDoctor['doctorID']; ?></td>
                            <td><?php echo $rowDoctor['doctorName']; ?></td>
                            <td><?php echo $rowDoctor['name_specialization']; ?></td>
                            <td><?php echo $rowDoctor['amount']; ?></td>
                          </tr>
                        <?php
                        }
                        ?>
                      </tbody>
                    </table>

                  </div>
                </div>
              </div>
            </div>
          </div><!--End Row-->

          <!--End Dashboard Content-->

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
        $(function() {
          "use strict";

          var ctx = document.getElementById("chart2").getContext('2d');
          var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
              labels: ["Male", "Female"],
              datasets: [{
                backgroundColor: [
                  "#42FF33",
                  "#EC33FF"
                ],
                data: [<?php echo $countMale; ?>, <?php echo $countFemale; ?>],
                borderWidth: [0, 0]
              }]
            },
            options: {
              maintainAspectRatio: false,
              legend: {
                position: "bottom",
                display: false,
                labels: {
                  fontColor: '#ddd',
                  boxWidth: 15
                }
              },
              tooltips: {
                displayColors: false
              }
            }
          });
        });
      </script>



      <?php
      $sqlRace = "SELECT patientRace, COUNT(id) as total FROM patient GROUP BY patientRace";
      $resultRace = mysqli_query($con, $sqlRace);

      if (!$resultRace) {
        die("Query failed: " . mysqli_error($con));
      }

      $patientRace = [];
      $totalRace = [];

      while ($rowRace = mysqli_fetch_array($resultRace)) {
        $patientRace[] = $rowRace['patientRace'];
        $totalRace[] = $rowRace['total'];
      }

      // Debugging statements
      // echo 'Patient Race: ' . json_encode($patientRace) . '<br>';
      // echo 'Total Race: ' . json_encode($totalRace) . '<br>';
      ?>

      <script>
        // PHP data to JavaScript
        var labels = <?php echo json_encode($patientRace); ?>;
        var data = <?php echo json_encode($totalRace); ?>;

        // Set the global default font color for Chart.js
        Chart.defaults.color = '#fff';

        // Get the canvas element
        var ctx = document.getElementById('myBarChart').getContext('2d');

        // Create the bar chart
        var myBarChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'Total',
              backgroundColor: [
                "#5969ff",
                "#ff407b",
                "#25d5f2",
                "#ffc750",
                "#2ec551",
                "#7040fa",
                "#ff004e"
              ],
              data: <?php echo json_encode($totalRace); ?>,
            }]
          },
          options: {
            legend: {
              display: true,
              position: 'bottom',
              labels: {
                fontFamily: 'Circular Std Book',
                fontSize: 14,
              }
            },
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
      </script>







      <!-- <script type="text/javascript">
        var ctx = document.getElementById("chartjs_bar").getContext('2d');
        var myChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: <?php echo json_encode($patientRace); ?>,
            datasets: [{
              backgroundColor: [
                "#5969ff",
                "#ff407b",
                "#25d5f2",
                "#ffc750",
                "#2ec551",
                "#7040fa",
                "#ff004e"
              ],
              data: <?php echo json_encode($totalRace); ?>,
            }]
          },
          options: {
            legend: {
              display: true,
              position: 'bottom',
              labels: {
                fontColor: '#71748d',
                fontFamily: 'Circular Std Book',
                fontSize: 14,
              }
            },
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
      </script> -->



  </body>

  </html>
<?php


} else {
  header("location:../index.php");
  exit;
}

unset($_SESSION['prompt']);
mysqli_close($con);

?>