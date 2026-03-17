<?php
require_once '../config/db.php';
require_once '../src/Auth.php';
require_once '../src/functions.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    redirect('index.php');
}

$user = $auth->getUser();
$role = $user['role'];

include '../templates/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Dashboard</h1>
        <div class="alert alert-info">
            Welcome back, <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>!
        </div>
    </div>
</div>

<div class="row">
    <?php if ($role === 'admin'): ?>
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">User Management</div>
            <div class="card-body">
                <h5 class="card-title">Manage System Users</h5>
                <p class="card-text">Add, remove, or update staff and patient accounts.</p>
                <a href="users.php" class="btn btn-light">Go to Users</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (in_array($role, ['admin', 'doctor', 'receptionist'])): ?>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Patients</div>
            <div class="card-body">
                <h5 class="card-title">Patient Records</h5>
                <p class="card-text">View patient details and medical history.</p>
                <a href="patients.php" class="btn btn-light">Manage Patients</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning mb-3">
            <div class="card-header">Appointments</div>
            <div class="card-body">
                <h5 class="card-title">Schedule</h5>
                <p class="card-text">View and book appointments.</p>
                <a href="appointments.php" class="btn btn-light">Manage Appointments</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($role === 'patient'): ?>
    <div class="col-md-6">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">My Health</div>
            <div class="card-body">
                <h5 class="card-title">My Records</h5>
                <p class="card-text">View your medical history and prescriptions.</p>
                <a href="#" class="btn btn-light">View Records</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-white bg-secondary mb-3">
            <div class="card-header">My Appointments</div>
            <div class="card-body">
                <h5 class="card-title">Appointments</h5>
                <p class="card-text">Check your upcoming visits.</p>
                <a href="#" class="btn btn-light">View Appointments</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include '../templates/footer.php'; ?>
