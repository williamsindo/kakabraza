<?php
require_once '../config/db.php';
require_once '../src/Auth.php';
require_once '../src/functions.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if ($auth->isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);

    if ($auth->login($username, $password)) {
        redirect('dashboard.php');
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            background-color: #f5f5f5; 
        }
        .form-signin { 
            width: 100%; 
            max-width: 330px; 
            padding: 15px; 
            margin: auto; 
        }
    </style>
</head>
<body class="text-center">
    <main class="form-signin">
        <form method="POST" action="">
            <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="form-floating mb-3">
                <input type="text" name="username" class="form-control" id="floatingInput" placeholder="Username" required>
                <label for="floatingInput">Username</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                <label for="floatingPassword">Password</label>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
            <p class="mt-5 mb-3 text-muted">&copy; <?php echo date("Y"); ?></p>
        </form>
    </main>
</body>
</html>
