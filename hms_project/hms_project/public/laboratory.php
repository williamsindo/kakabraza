<?php
require_once '../config/db.php';
require_once '../src/Auth.php';
require_once '../src/Lab.php';
require_once '../src/Patient.php';
require_once '../src/functions.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$labObj = new Lab($db);
$patientObj = new Patient($db);

$auth->requireRole(['admin', 'doctor', 'nurse', 'receptionist']);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['request_test'])) {
        $patient_id = clean_input($_POST['patient_id']);
        $test_type = clean_input($_POST['test_type']);
        if ($labObj->create($patient_id, $test_type)) {
            $message = '<div class="alert alert-success">Test requested successfully!</div>';
        } else {
             $message = '<div class="alert alert-danger">Failed to request test.</div>';
        }
    }
    
    if (isset($_POST['update_result'])) {
        $id = clean_input($_POST['test_id']);
        $result = clean_input($_POST['result']);
        if ($labObj->updateResult($id, $result)) {
            $message = '<div class="alert alert-success">Result updated!</div>';
        }
    }
}

$tests = $labObj->readAll();
$patients = $patientObj->readAll();

include '../templates/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Laboratory Management</h2>
        <?php echo $message; ?>

        <div class="card mb-4">
            <div class="card-header">Request New Test</div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
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
                        <div class="col-md-6 mb-3">
                            <label>Test Type</label>
                            <input type="text" name="test_type" class="form-control" placeholder="e.g. Blood Test, X-Ray" required>
                        </div>
                    </div>
                    <button type="submit" name="request_test" class="btn btn-primary">Request Test</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Lab Requests & Results</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Test Type</th>
                            <th>Result</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $tests->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $row['test_date']; ?></td>
                            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['test_type']); ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="test_id" value="<?php echo $row['id']; ?>">
                                    <div class="input-group">
                                        <input type="text" name="result" class="form-control form-control-sm" value="<?php echo htmlspecialchars($row['result'] ?? ''); ?>" placeholder="Enter Result">
                                        <button class="btn btn-sm btn-outline-secondary" type="submit" name="update_result">Save</button>
                                    </div>
                                </form>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $row['result'] ? 'success' : 'warning'; ?>">
                                    <?php echo $row['result'] ? 'Completed' : 'Pending'; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
