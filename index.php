<?php

date_default_timezone_set("Europe/Bratislava");

echo date("Y-m-d H:i:s");

class Logger {
    private static $studentsFile = "students.json";
    private static $arrivalsFile = "prichody.json";

    public static function isLateMorning()
    {
        $hour = date("H");
        return ($hour >= 8);
    }

    public static function logStudent($name, $late)
    {
        if (date('H') >= 20 || date('H') <= 0) {
            die("You cannot enter school between 20 and 24");
        }

        if (!empty($name)) {
            $logData = file_exists(self::$studentsFile) ? json_decode(file_get_contents(self::$studentsFile), true) : [];

            $totalArrivals = isset($logData['total_arrivals']) ? $logData['total_arrivals'] + 1 : 1;

            $logData[] = array(
                'name' => $name,
                'time' => date("Y-m-d H:i:s"),
                'late' => $late ? 'Yes' : 'No'
            );

            $logData['total_arrivals'] = $totalArrivals;
            file_put_contents(self::$studentsFile, json_encode($logData, JSON_PRETTY_PRINT));
        }
    }

    public static function logCustomMessage($message)
    {
        if (!empty($message)) {
            $logData = file_exists(self::$arrivalsFile) ? json_decode(file_get_contents(self::$arrivalsFile), true) : [];

            $logData[] = array(
                'message' => $message,
                'time' => date("Y-m-d H:i:s")
            );

            file_put_contents(self::$arrivalsFile, json_encode($logData, JSON_PRETTY_PRINT));
        }
    }

    public static function displayStudentData()
    {
        if (file_exists(self::$studentsFile)) {
            $studentData = json_decode(file_get_contents(self::$studentsFile), true);
            if (!empty($studentData)) {
                print_r($studentData);
                return;
            }
        }
        echo "No student data available";
    }



    public static function displayCustomMessages()
    {
        $arrivalsData = file_exists(self::$arrivalsFile) ? json_decode(file_get_contents(self::$arrivalsFile), true) : [];
        if (!empty($arrivalsData)) {
            print_r($arrivalsData);
        } else {
            echo "No messages available";
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['name'])) {
        $name = $_POST['name'];
        $late = Logger::isLateMorning();

        Logger::logStudent($name, $late);
    } else if (isset($_POST['custom_message'])) {
        $customMessage = $_POST['custom_message'];

        Logger::logCustomMessage($customMessage);
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
    <input type="submit" value="Log Student">
</form>

<form method="post">
    Custom Message: <input type="text" name="custom_message">
    <input type="submit" value="Log Custom Message">
</form>

<h3>Student Data</h3>
<pre>
        <?php
        Logger::displayStudentData();
        ?>
</pre>

<h3>Custom Messages</h3>
<pre>
        <?php
        Logger::displayCustomMessages();
        ?>
</pre>

</body>
</html>
