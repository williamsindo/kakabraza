<?php
require_once '../config/db.php';
require_once '../src/Auth.php';
require_once '../src/Patient.php';
require_once '../src/functions.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$patientObj = new Patient($db);

$auth->requireRole(['admin', 'doctor', 'receptionist', 'nurse']);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_patient'])) {
    $name = clean_input($_POST['name']);
    $dob = clean_input($_POST['dob']);
    $gender = clean_input($_POST['gender']);
    $contact = clean_input($_POST['contact']);
    $address = clean_input($_POST['address']);
    $medical_history = clean_input($_POST['medical_history']);

    if ($patientObj->create($name, $dob, $gender, $contact, $address, $medical_history)) {
        $message = '<div class="alert alert-success">Patient registered successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Failed to register patient.</div>';
    }
}

$patients = $patientObj->readAll();

include '../templates/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Patient Management</h2>
        <?php echo $message; ?>

        <div class="card mb-4">
            <div class="card-header">Register New Patient</div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Contact Number</label>
                            <input type="text" name="contact" class="form-control" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="1"></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Medical History (Initial)</label>
                            <textarea name="medical_history" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <button type="submit" name="add_patient" class="btn btn-primary">Register Patient</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Patient Records</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Contact</th>
                                <th>Medical History</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $patients->fetch(PDO::FETCH_ASSOC)): 
                                $dob = new DateTime($row['dob']);
                                $now = new DateTime();
                                $age = $now->diff($dob)->y;
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo $age; ?></td>
                                <td><?php echo $row['gender']; ?></td>
                                <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                <td><?php echo htmlspecialchars(substr($row['medical_history'], 0, 50)) . '...'; ?></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info text-white">View</a>
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

<?php include '../templates/footer.php'; ?>
