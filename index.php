<?php
date_default_timezone_set("Europe/Bratislava");

echo date("Y-m-d H:i:s");

class Logger {
    private const students = "students.json";
    private const prichody = "prichody.json";

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
            $logData = file_exists(self::students) ? json_decode(file_get_contents(self::students), true) : [];

            $totalArrivals = isset($logData['total_arrivals']) ? $logData['total_arrivals'] + 1 : 1;

            $logData[$totalArrivals] = array(
                'name' => $name,
                'time' => date("Y-m-d H:i:s"),
                'late' => $late ? 'Yes' : 'No'
            );

            $logData['total_arrivals'] = $totalArrivals;
            file_put_contents(self::students, json_encode($logData, JSON_PRETTY_PRINT));
        }
    }

    public static function logCustomMessage($message)
    {
        if (!empty($message)) {
            $logData = file_exists(self::prichody) ? json_decode(file_get_contents(self::prichody), true) : [];

            $logData[] = array(
                'message' => $message,
                'time' => date("Y-m-d H:i:s")
            );

            file_put_contents(self::prichody, json_encode($logData, JSON_PRETTY_PRINT));
        }
    }

    public static function displayStudentData()
    {
        if (file_exists(self::students)) {
            $studentData = json_decode(file_get_contents(self::students), true);
            if (!empty($studentData)) {
                echo "<h3>Total Students: {$studentData['total_arrivals']}</h3>";
                echo "<table border='1'>";
                echo "<tr><th>Name</th><th>Time</th><th>Late</th></tr>";
                foreach ($studentData as $key => $student) {
                    if ($key !== 'total_arrivals') {
                        echo "<tr>";
                        echo "<td>{$student['name']}</td>";
                        echo "<td>{$student['time']}</td>";
                        echo "<td>{$student['late']}</td>";
                        echo "</tr>";
                    }
                }
                echo "</table>";
                return;
            }
        }
        echo "No student data available";
    }

    public static function displayCustomMessages()
    {
        $arrivalsData = file_exists(self::prichody) ? json_decode(file_get_contents(self::prichody), true) : [];
        if (!empty($arrivalsData)) {
            echo "<ul>";
            foreach ($arrivalsData as $message) {
                echo "<li>{$message['message']} | Time: {$message['time']}</li>";
            }
            echo "</ul>";
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

if (isset($_GET['name'])) {
    $nameFromUrl = $_GET['name'];
    $late = Logger::isLateMorning();
    Logger::logStudent($nameFromUrl, $late);
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
<?php Logger::displayStudentData(); ?>

<h3>Custom Messages</h3>
<?php Logger::displayCustomMessages(); ?>

</body>
</html>
