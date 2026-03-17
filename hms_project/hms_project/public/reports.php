<?php
require_once '../config/db.php';
require_once '../src/Auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

$auth->requireRole(['admin']);

// Stats Helpers
function getCount($db, $table) {
    $query = "SELECT COUNT(*) as count FROM " . $table;
    $stmt = $db->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['count'];
}

function getRevenue($db) {
    $query = "SELECT SUM(amount) as total FROM bills WHERE status='Paid'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total'] ? $row['total'] : 0;
}

$patientCount = getCount($db, 'patients');
$doctorCount = getCount($db, 'doctors');
$apptCount = getCount($db, 'appointments');
$totalRevenue = getRevenue($db);

include '../templates/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">System Reports</h2>
        
        <div class="row text-center">
            <div class="col-md-3">
                <div class="card bg-primary text-white mb-3">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo $patientCount; ?></h3>
                        <p class="card-text">Total Patients</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                 <div class="card bg-success text-white mb-3">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo $doctorCount; ?></h3>
                        <p class="card-text">Doctors</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                 <div class="card bg-warning text-dark mb-3">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo $apptCount; ?></h3>
                        <p class="card-text">Total Appointments</p>
                    </div>
                </div>
            </div>
             <div class="col-md-3">
                 <div class="card bg-info text-white mb-3">
                    <div class="card-body">
                        <h3 class="card-title">$<?php echo number_format($totalRevenue, 2); ?></h3>
                        <p class="card-text">Total Revenue</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Quick Links</div>
                    <div class="card-body">
                        <a href="patients.php" class="btn btn-outline-primary">Manage Patients</a>
                        <a href="appointments.php" class="btn btn-outline-warning">View Appointments</a>
                        <a href="billing.php" class="btn btn-outline-info">Financial Records</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
