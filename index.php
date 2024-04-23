<?php

//Default time zone set from PHP manual https://www.php.net/manual/en/timezones.europe.php;
date_default_timezone_set("Europe/Bratislava");

// Time with this format saved in a variable 2023-12-07 23:59:59;
$timeFormat = date("Y-m-d H:i:s");
echo $timeFormat;

$logFilePath = "students.json";

if (date('H') >= 20 && date('H') <= 0) {
    die("You cannot enter school between 20 and 24");
}

function isLateMorning()
{
    $hour = date("H");
    return ($hour >= 8);
}

function logStudent($name, $late)
{
    global $logFilePath, $timeFormat;

    if (!empty($name)) {
        $logData = file_exists($logFilePath) ? json_decode(file_get_contents($logFilePath), true) : [];

        $logData[] = array(
            'name' => $name,
            'time' => $timeFormat,
            'late' => $late ? 'Yes' : 'no'
        );

        file_put_contents($logFilePath, json_encode($logData, JSON_PRETTY_PRINT));
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $late = isLateMorning();

    logStudent($name, $late);
}

function displayStudentData()
{
    global $logFilePath;
    $studentData = file_exists($logFilePath) ? json_decode(file_get_contents($logFilePath), true) : [];
    if (!empty($studentData)) {
        foreach ($studentData as $student) {
            echo "Name: " . $student['name'] . "\n";
            echo "Time: " . $student['time'] . "\n";
            echo "Late: " . ($student['late']) . "\n\n";
        }
    } else {
        echo "No student data available";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP learning</title>
</head>
<body>

<form method="post">
    Name: <input type="text" name="name">
    <input type="submit">
</form>
<h3>Student Data</h3>
<form method="post">
    <input type="hidden" name="action" value="display_data">
    <input type="submit" value="Display Student Data">
</form>
<pre>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'display_data') {
            displayStudentData();
        }
        ?>
</pre>
</body>
</html>
