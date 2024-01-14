<?php
function clean($data)
{
    $data = trim($data);
    $data = stripslashes($data);

    return $data;
}

function showPrompt()
{
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
    echo "
    <div class='alert alert-danger alert-dismissible' role='alert'>
        <button type='button' class='close' data-dismiss='alert'>&times;</button>
        <div class='alert-icon'>
            <i class='fa fa-warning'></i>
        </div>
        <div class='alert-message'>
            <span>" . $_SESSION['errprompt'] . "</span>
        </div>
    </div>";
}

function insertAssessmentData($con, $symptoms, $contact, $travel, $exposure, $hygiene, $symptomDuration, $assessmentResult, $patientID)
{
    $sql = "INSERT INTO assessment_data (symptoms, contact, travel, exposure, hygiene, symptom_duration, assessmentResult, patientID) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssss", $symptoms, $contact, $travel, $exposure, $hygiene, $symptomDuration, $assessmentResult, $patientID);

    if (mysqli_stmt_execute($stmt)) {
        return true; // Insertion successful
    } else {
        return false; // Insertion failed
    }

    mysqli_stmt_close($stmt);
}

function processString($inputString)
{
    $processedArray = preg_split('/[,.]+|[ ,.]+/', $inputString, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    $processedArray = array_map('strtolower', $processedArray);

    return $processedArray;
}
