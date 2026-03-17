<?php
require_once '../config/db.php';
require_once '../src/Auth.php';
require_once '../src/Ward.php';
require_once '../src/Patient.php';
require_once '../src/functions.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$wardObj = new Ward($db);
$patientObj = new Patient($db);

$auth->requireRole(['admin', 'doctor', 'nurse', 'receptionist']);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['admit_patient'])) {
        $patient_id = clean_input($_POST['patient_id']);
        $bed_id = clean_input($_POST['bed_id']);
        if ($wardObj->admitPatient($patient_id, $bed_id)) {
            $message = '<div class="alert alert-success">Patient admitted successfully!</div>';
        } else {
             $message = '<div class="alert alert-danger">Failed to admit patient.</div>';
        }
    }
    
    if (isset($_POST['discharge_patient'])) {
        $admission_id = clean_input($_POST['admission_id']);
        $bed_id = clean_input($_POST['bed_id']);
        if ($wardObj->dischargePatient($admission_id, $bed_id)) {
            $message = '<div class="alert alert-success">Patient discharged! Bed is now free.</div>';
        }
    }
}

$beds = $wardObj->getBeds();
$admissions = $wardObj->getActiveAdmissions();
$patients = $patientObj->readAll();

include '../templates/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Ward Management</h2>
        <?php echo $message; ?>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Admit Patient</div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label>Patient</label>
                                <select name="patient_id" class="form-select" required>
                                    <option value="">Select Patient</option>
                                    <?php 
                                    while ($p = $patients->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='" . $p['id'] . "'>" . $p['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Available Bed</label>
                                <select name="bed_id" class="form-select" required>
                                    <option value="">Select Bed</option>
                                    <?php 
                                    while ($bed = $beds->fetch(PDO::FETCH_ASSOC)) {
                                        if (!$bed['is_occupied']) {
                                            echo "<option value='" . $bed['id'] . "'>" . $bed['ward_number'] . " - " . $bed['bed_number'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" name="admit_patient" class="btn btn-primary">Admit</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                 <div class="card">
                    <div class="card-header">Current Admissions</div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Bed</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($adm = $admissions->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($adm['patient_name']); ?></td>
                                    <td><?php echo $adm['ward_number'] . ' - ' . $adm['bed_number']; ?></td>
                                    <td><?php echo date('m-d H:i', strtotime($adm['admission_date'])); ?></td>
                                    <td>
                                        <form method="POST" action="" onsubmit="return confirm('Discharge patient?');">
                                            <input type="hidden" name="admission_id" value="<?php echo $adm['admission_id']; ?>">
                                            <input type="hidden" name="bed_id" value="<?php echo $adm['bed_id']; ?>">
                                            <button type="submit" name="discharge_patient" class="btn btn-sm btn-outline-danger">Discharge</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
