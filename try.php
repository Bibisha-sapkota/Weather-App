<?php


$servername = "localhost";
$username = "root";
$password = "";
$conn = mysqli_connect($servername, $username, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$createDatabase = "CREATE DATABASE IF NOT EXISTS prototype2";
if (mysqli_query($conn, $createDatabase)) {
} else {
    die("Failed to create database: " . mysqli_error($conn));
}

mysqli_close($conn);

// Reconnect to the database
$conn = mysqli_connect($servername, $username, $password, "prototype2");

if (!$conn) {
    die("Connection to database failed: " . mysqli_connect_error());
}

$createTable = "CREATE TABLE IF NOT EXISTS weather1 (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    City VARCHAR(255),
    Temperature FLOAT NOT NULL,
    Wind FLOAT NOT NULL,
    Humidity FLOAT NOT NULL,
    Pressure FLOAT NOT NULL,
    Description VARCHAR(255),
    Icon VARCHAR(255),
    AccessedTime datetime
)";

if (mysqli_query($conn, $createTable)) {
} else {
    die("Failed to create table: " . mysqli_error($conn));
}

if (isset($_GET['q'])) {
    $city = $_GET['q'];
} else {
    $city = "dharan";
}

$apiKey = "28d49291f45ce958e2298871a1ed7af5";

$selectAllData = "SELECT * FROM weather1 WHERE City = '$city' ORDER BY AccessedTime DESC";
$result = mysqli_query($conn, $selectAllData);
$row = mysqli_fetch_assoc($result);
$lastUpdate = strtotime($row['AccessedTime']);
$currentTime = time();

if (mysqli_num_rows($result) == 0 || $currentTime - $lastUpdate >= 7200) {
    $url = "https://api.openweathermap.org/data/2.5/weather?q=" . $city . "&units=metric&appid=" . $apiKey;
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    $city = $data['name'];
    $temperature = $data['main']['temp'];
    $humidity = $data['main']['humidity'];
    $wind = $data['wind']['speed'];
    $pressure = $data['main']['pressure'];
    $description = $data['weather'][0]['description'];
    $icon = $data['weather'][0]['icon'];
    $AccessedTime = date('Y-m-d H:i:s', $data['dt']);

    $insertData = "INSERT INTO weather1 (City, Temperature, Wind, Humidity, Pressure, Description, Icon, AccessedTime) 
                   VALUES ('$city', '$temperature', '$wind', '$humidity', '$pressure', '$description', '$icon', '$AccessedTime')";
    if (!mysqli_query($conn, $insertData)) {
        die("Error inserting weather data: " . mysqli_error($conn));
    }
}

$result = mysqli_query($conn, $selectAllData);
$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

header('Content-Type: application/json');
echo json_encode($rows);

mysqli_close($conn);

?>