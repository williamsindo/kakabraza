<?php
$servername = "localhost";
$username = "root";
$password = "roota"; 
$dbname = "new booking site"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT room_number, guest_name, check_in_date, check_out_date FROM bookings WHERE status = 'booked'";
$result = $conn->query($sql);

$bookedRooms = [];

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $bookedRooms[] = $row;
  }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($bookedRooms);
?>
