<?php
require 'db.php';

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$user_type = $_POST['user_type'] ?? 'guest';

if (!$name || !$email || !$password) {
    exit("Missing fields.");
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
try {
    $stmt->execute([$name, $email, $hash, $user_type]);
    echo "User registered!";
    if ($stmt->execute()) {
    // Registration successful, redirect to home
    echo "<script>
    window.open('home.php', '_blank'); // Open in new tab
    window.location.href = 'login.php'; // Keep current tab on login or redirect
</script>";

    exit();
} else {
    echo "Error: " . $stmt->error;
}

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
