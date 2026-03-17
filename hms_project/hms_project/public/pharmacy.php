<?php
require_once '../config/db.php';
require_once '../src/Auth.php';
require_once '../src/Medicine.php';
require_once '../src/functions.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$medObj = new Medicine($db);

$auth->requireRole(['admin', 'doctor', 'nurse', 'receptionist']); // Expanded roles for demo

$message = '';

// Add Medicine
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_medicine'])) {
    $name = clean_input($_POST['name']);
    $description = clean_input($_POST['description']);
    $price = clean_input($_POST['price']);
    $stock = clean_input($_POST['stock']);

    if ($medObj->create($name, $description, $price, $stock)) {
        $message = '<div class="alert alert-success">Medicine added successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Failed to add medicine.</div>';
    }
}

// Dispense Medicine
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['dispense'])) {
    $id = clean_input($_POST['med_id']);
    $qty = clean_input($_POST['quantity']);
    
    if ($medObj->dispense($id, $qty)) {
         $message = '<div class="alert alert-success">Medicine dispensed successfully! Stock updated.</div>';
    } else {
         $message = '<div class="alert alert-danger">Failed to dispense (Insufficient stock or error).</div>';
    }
}

$medicines = $medObj->readAll();

include '../templates/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Pharmacy Management</h2>
        <?php echo $message; ?>

        <div class="card mb-4">
            <div class="card-header">Inventory</div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price ($)</th>
                            <th>Stock</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $medicines->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo $row['price']; ?></td>
                            <td>
                                <?php if($row['stock_quantity'] < 10): ?>
                                    <span class="text-danger fw-bold"><?php echo $row['stock_quantity']; ?> (Low)</span>
                                <?php else: ?>
                                    <?php echo $row['stock_quantity']; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" action="" class="d-flex align-items-center">
                                    <input type="hidden" name="med_id" value="<?php echo $row['id']; ?>">
                                    <input type="number" name="quantity" class="form-control form-control-sm me-2" style="width: 70px;" value="1" min="1">
                                    <button type="submit" name="dispense" class="btn btn-sm btn-primary">Dispense</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Add New Medicine</div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Initial Stock</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Description</label>
                            <input type="text" name="description" class="form-control">
                        </div>
                    </div>
                    <button type="submit" name="add_medicine" class="btn btn-success">Add to Inventory</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
