<?php
require_once '../config/db.php';
require_once '../src/Auth.php';
require_once '../src/MedicalRecord.php';
require_once '../src/Patient.php';
require_once '../src/Doctor.php';
require_once '../src/functions.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$recordObj = new MedicalRecord($db);
$patientObj = new Patient($db);
$doctorObj = new Doctor($db);

$auth->requireRole(['admin', 'doctor', 'receptionist', 'nurse']);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_record'])) {
    $patient_id = clean_input($_POST['patient_id']);
    
    // We need to find the doctor_id associated with the logged in user (if doctor)
    // For simplicity, we'll let the user select the doctor or default to current user if they are a doctor
    $doctor_id = clean_input($_POST['doctor_id']); 
    
    $diagnosis = clean_input($_POST['diagnosis']);
    $prescription = clean_input($_POST['prescription']);
    
    $lab_results_path = null;
    if (isset($_FILES['lab_file']) && $_FILES['lab_file']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["lab_file"]["name"]);
        // Ideally we should rename file to avoid conflicts and check type
        $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $newFileName = $target_dir . uniqid() . '.' . $fileType;
        
        if (move_uploaded_file($_FILES["lab_file"]["tmp_name"], $newFileName)) {
            $lab_results_path = $newFileName;
        } else {
             $message = '<div class="alert alert-warning">File upload failed. Record saved without file.</div>';
        }
    }

    if ($recordObj->create($patient_id, $doctor_id, $diagnosis, $prescription, $lab_results_path)) {
        $message = '<div class="alert alert-success">Medical Record added successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Failed to add record.</div>';
    }
}

$patients = $patientObj->readAll();
$doctors = $doctorObj->readAll();

// Handle viewing records for a specific patient
$selected_patient_records = null;
if (isset($_GET['view_patient_id'])) {
    $view_id = $_GET['view_patient_id'];
    $selected_patient_records = $recordObj->readByPatient($view_id);
    $selected_patient = $patientObj->readOne($view_id);
}

include '../templates/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Medical Records</h2>
        <?php echo $message; ?>

        <div class="card mb-4">
            <div class="card-header">Add New Record</div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">Select Patient</option>
                                <?php 
                                // Reset patient pointer
                                $patients->execute();
                                while ($p = $patients->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . $p['id'] . "'>" . $p['name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Doctor</label>
                            <select name="doctor_id" class="form-select" required>
                                <option value="">Select Doctor</option>
                                <?php 
                                $doctors->execute();
                                while ($d = $doctors->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . $d['id'] . "'>" . $d['full_name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Diagnosis</label>
                            <textarea name="diagnosis" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Prescription</label>
                            <textarea name="prescription" class="form-control" rows="2"></textarea>
                        </div>
                         <div class="col-md-12 mb-3">
                            <label>Lab Results (PDF/Image)</label>
                            <input type="file" name="lab_file" class="form-control">
                        </div>
                    </div>
                    <button type="submit" name="add_record" class="btn btn-primary">Save Record</button>
                </form>
            </div>
        </div>

        <hr>
        
        <h4>View Patient History</h4>
        <form method="GET" action="" class="row g-3 mb-4">
            <div class="col-auto">
                 <select name="view_patient_id" class="form-select">
                    <option value="">Select Patient to View</option>
                    <?php 
                    $patients->execute();
                    while ($p = $patients->fetch(PDO::FETCH_ASSOC)) {
                        $selected = (isset($_GET['view_patient_id']) && $_GET['view_patient_id'] == $p['id']) ? 'selected' : '';
                        echo "<option value='" . $p['id'] . "' $selected>" . $p['name'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">View History</button>
            </div>
        </form>

        <?php if($selected_patient_records): ?>
        <div class="card">
            <div class="card-header">Records for <?php echo htmlspecialchars($selected_patient['name']); ?></div>
            <div class="card-body">
                <?php if($selected_patient_records->rowCount() > 0): ?>
                    <ul class="list-group list-group-flush">
                    <?php while($rec = $selected_patient_records->fetch(PDO::FETCH_ASSOC)): ?>
                        <li class="list-group-item">
                            <h5><?php echo date('Y-m-d H:i', strtotime($rec['visit_date'])); ?> <small class="text-muted">by Dr. <?php echo htmlspecialchars($rec['doctor_name']); ?></small></h5>
                            <p><strong>Diagnosis:</strong> <?php echo nl2br(htmlspecialchars($rec['diagnosis'])); ?></p>
                            <p><strong>Prescription:</strong> <?php echo nl2br(htmlspecialchars($rec['prescription'])); ?></p>
                            <?php if($rec['lab_results_path']): ?>
                                <a href="<?php echo $rec['lab_results_path']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">View Lab Report</a>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No records found for this patient.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php include '../templates/footer.php'; ?>
