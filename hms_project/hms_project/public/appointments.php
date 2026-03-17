<?php
require_once '../config/db.php';
require_once '../src/Auth.php';
require_once '../src/Appointment.php';
require_once '../src/Patient.php';
require_once '../src/Doctor.php';
require_once '../src/functions.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$apptObj = new Appointment($db);
$patientObj = new Patient($db);
$doctorObj = new Doctor($db);

$auth->requireRole(['admin', 'doctor', 'receptionist', 'nurse']);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_appointment'])) {
    $patient_id = clean_input($_POST['patient_id']);
    $doctor_id = clean_input($_POST['doctor_id']);
    $date = clean_input($_POST['date']);
    $time = clean_input($_POST['time']); // Basic implementation, concat date+time
    $reason = clean_input($_POST['reason']);

    $appointment_date = $date . ' ' . $time;

    if ($apptObj->create($patient_id, $doctor_id, $appointment_date, $reason)) {
        $message = '<div class="alert alert-success">Appointment booked successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Failed to book appointment.</div>';
    }
}

// Handle status update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action']; // complete, cancel
    $id = $_GET['id'];
    $status = '';
    if ($action == 'complete') $status = 'Completed';
    if ($action == 'cancel') $status = 'Cancelled';
    
    if ($status && $apptObj->updateStatus($id, $status)) {
        redirect('appointments.php');
    }
}

$appointments = $apptObj->readAll();
$patients = $patientObj->readAll();
$doctors = $doctorObj->readAll();

include '../templates/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Appointment Management</h2>
        <?php echo $message; ?>

        <div class="card mb-4">
            <div class="card-header">Book Appointment</div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Patient</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">Select Patient</option>
                                <?php 
                                while ($p = $patients->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . $p['id'] . "'>" . $p['name'] . " (" . $p['contact'] . ")</option>";
                                }
                                $patients->execute(); // Reset cursor for next usage if needed (though not needed here)
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Doctor</label>
                            <select name="doctor_id" class="form-select" required>
                                <option value="">Select Doctor</option>
                                <?php 
                                while ($d = $doctors->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . $d['id'] . "'>" . $d['full_name'] . " - " . $d['specialization'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Time</label>
                            <input type="time" name="time" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Reason</label>
                            <input type="text" name="reason" class="form-control" placeholder="Reason for visit">
                        </div>
                    </div>
                    <button type="submit" name="book_appointment" class="btn btn-primary">Book Appointment</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Appointments List</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date/Time</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $appointments->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($row['appointment_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['reason']); ?></td>
                            <td>
                                <?php 
                                    $badge = 'dark';
                                    if($row['status'] == 'Booked') $badge = 'primary';
                                    if($row['status'] == 'Completed') $badge = 'success';
                                    if($row['status'] == 'Cancelled') $badge = 'danger';
                                ?>
                                <span class="badge bg-<?php echo $badge; ?>"><?php echo $row['status']; ?></span>
                            </td>
                            <td>
                                <?php if($row['status'] == 'Booked'): ?>
                                <a href="appointments.php?action=complete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">Complete</a>
                                <a href="appointments.php?action=cancel&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger">Cancel</a>
                                <?php endif; ?>
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
