<?php

session_start();

require '../dbconnection.php';
require '../functions.php';

if (isset($_SESSION['id'], $_SESSION['password'])) {
?>
    <!DOCTYPE html>
    <html lang="en">

    <?php include "head.php"; ?>

    <body class="bg-theme bg-theme2">
        <!-- Scripts -->
        <script type="module" src="https://gradio.s3-us-west-2.amazonaws.com/4.13.0/gradio.js">
        </script>

        <!-- Wrapper -->
        <div id="wrapper mt-3">
            <?php
            include "sidebar.php";
            include "header.php";
            ?>

            <!-- Content -->
            <div class="content-wrapper mt-3">
                <!-- Container -->
                <div class="container-fluid">
                    <?php
                    if (isset($_SESSION['errprompt'])) {
                        showError();
                    } elseif (isset($_SESSION['prompt'])) {
                        showPrompt();
                    }
                    ?>
                    <div class="card">
                        <div class="card-body">
                            <gradio-app src="https://u2005371-chatbot.hf.space"></gradio-app>
                        </div>
                    </div>
                </div>
                <!-- Container -->
            </div>
            <!--Content-->

            <!--Back To Top Button-->
            <a href="javaScript:void();" class="back-to-top">
                <i class="fa fa-angle-double-up"></i>
            </a>
            <!--Back To Top Button-->

            <?php include "footer.php"; ?>
        </div>
        <!-- Wrapper -->
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