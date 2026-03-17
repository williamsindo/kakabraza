<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:90"); // Allow requests from your frontend
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow specific HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers

require_once __DIR__ . "/../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);
$user = $data['username'];
$pass = $data['password'];

$sql = "SELECT password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL prepare error: " . $conn->error]);
    exit();
}

$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

	//$hashedPassword = password_hash($pass, PASSWORD_DEFAULT);		

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if (password_verify($pass, $row['password'])) {
        echo json_encode(["success" => true, "message" => "Login successful"]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid username or password"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid username or password"]);
}


$stmt->close();
$conn->close();
?>
