<?php
class Medicine {
    private $conn;
    private $table_name = "medicines";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($name, $description, $price, $stock) {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, description=:description, price=:price, stock_quantity=:stock";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":stock", $stock);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function dispense($id, $quantity) {
        // First check stock
        $query = "SELECT stock_quantity FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['stock_quantity'] >= $quantity) {
            $new_stock = $row['stock_quantity'] - $quantity;
            $updateQuery = "UPDATE " . $this->table_name . " SET stock_quantity = :stock WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':stock', $new_stock);
            $updateStmt->bindParam(':id', $id);
            return $updateStmt->execute();
        }
        return false;
    }
}
