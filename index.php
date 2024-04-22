<?php

//Default time zone set from php manual https://www.php.net/manual/en/timezones.europe.php;
date_default_timezone_set("Europe/Bratislava");

// Time with this format saved in a variable 2023-12-07 23:59:59;
$timeFormat = date("Y/m/d H:i:s");
echo $timeFormat;

$logFilePath = "log.txt";

if (isset($_POST['name'])) {
    $data = $_POST['name'];
    file_put_contents($logFilePath, $data, FILE_APPEND);
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

</body>

