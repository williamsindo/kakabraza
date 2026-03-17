<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php if(isset($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">HMS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">Dashboard</a>
        </li>
        <?php if($_SESSION['role'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
        <?php endif; ?>
        <?php if(in_array($_SESSION['role'], ['admin', 'receptionist', 'doctor'])): ?>
            <li class="nav-item"><a class="nav-link" href="patients.php">Patients</a></li>
            <li class="nav-item"><a class="nav-link" href="appointments.php">Appointments</a></li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <span class="nav-link text-white">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?> (<?php echo ucfirst($_SESSION['role']); ?>)</span>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">
<?php endif; ?>
