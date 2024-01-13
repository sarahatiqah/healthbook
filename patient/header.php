<?php
$query = "SELECT * from patient WHERE id='" . $_SESSION['id'] . "' ";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
extract($row);
?>

<script>
    function changeIcon() {
        const iconElement = document.getElementById('icon');
        const isRight = iconElement.classList.contains('fa-chevron-right');
        iconElement.classList.toggle('fa-chevron-right', !isRight);
        iconElement.classList.toggle('fa-chevron-left', isRight);
    }
</script>

<header class="topbar-nav">
    <nav class="navbar navbar-expand fixed-top">
        <ul class="navbar-nav mr-auto align-items-center">
            <li class="nav-item">
                <a class="nav-link toggle-menu" href="javascript:void();" onclick="changeIcon()">
                    <i id="icon" class="fa fa-chevron-left menu-icon"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav align-items-center right-nav-link">
            <li class="nav-item">
                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" style="font-size: inherit;" href="javaScript:void();">
                    <i class="icon-menu"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li class="dropdown-item-text user-details pb-3">
                        <div class="media">
                            <div class="media-body">
                                <h6 class="mt-2 user-title"><?php echo $patientName ?></h6>
                                <p class="user-subtitle"><?php echo $patientEmail ?></p>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown-divider"></li>
                    <li class="dropdown-item pt-3"><a href="../logout.php"><i class="fa fa-sign-out mr-2"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>