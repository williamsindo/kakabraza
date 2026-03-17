<?php
require_once '../config/db.php';
require_once '../src/Auth.php';
require_once '../src/Billing.php';
require_once '../src/Patient.php';
require_once '../src/functions.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$billObj = new Billing($db);
$patientObj = new Patient($db);

$auth->requireRole(['admin', 'receptionist']);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_bill'])) {
    $patient_id = clean_input($_POST['patient_id']);
    $amount = clean_input($_POST['amount']);
    
    if ($billObj->create($patient_id, $amount)) {
        $message = '<div class="alert alert-success">Bill generated successfully!</div>';
    } else {
         $message = '<div class="alert alert-danger">Failed to generate bill.</div>';
    }
}

if (isset($_GET['pay_id'])) {
    $id = $_GET['pay_id'];
    if ($billObj->markPaid($id)) {
        redirect('billing.php');
    }
}

$bills = $billObj->readAll();
$patients = $patientObj->readAll();

include '../templates/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Billing & Payments</h2>
        <?php echo $message; ?>

        <div class="card mb-4">
            <div class="card-header">Generate New Bill</div>
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
                            <label>Amount ($)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" name="generate_bill" class="btn btn-primary">Generate Bill</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Invoices</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $bills->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($row['generated_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                            <td>$<?php echo number_format($row['amount'], 2); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $row['status'] == 'Paid' ? 'success' : 'danger'; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if($row['status'] == 'Unpaid'): ?>
                                    <a href="billing.php?pay_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Mark as Paid?');">Mark Paid</a>
                                <?php else: ?>
                                    <span class="text-muted">Paid</span>
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
