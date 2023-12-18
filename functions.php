<?php
function clean($data)
{
    $data = trim($data);
    $data = stripslashes($data);

    return $data;
}

function showPrompt()
{
    // echo "<div class='alert alert-success alert-dismissible' role='alert'>" . $_SESSION['prompt'] . "</div>";

    echo "
    <div class='alert alert-success alert-dismissible' role='alert'>
        <button type='button' class='close' data-dismiss='alert'>&times;</button>
  
        <div class='alert-message'>
            <span>" . $_SESSION['prompt'] . "</span>
        </div>
    </div>";
}

function showError()
{
    // echo "<div class='alert alert-danger alert-dismissible' role='alert'>" . $_SESSION['errprompt'] . "</div>";

    echo "
    <div class='alert alert-danger alert-dismissible' role='alert'>
        <button type='button' class='close' data-dismiss='alert'>&times;</button>
        <div class='alert-icon'>
            <i class='icon-info'></i>
        </div>
        <div class='alert-message'>
            <span>" . $_SESSION['errprompt'] . "</span>
        </div>
    </div>";
}
