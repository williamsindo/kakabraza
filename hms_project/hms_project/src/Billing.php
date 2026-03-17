<?php
class Billing {
    private $conn;
    private $table_name = "bills";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($patient_id, $amount) {
        $query = "INSERT INTO " . $this->table_name . " SET patient_id=:patient_id, amount=:amount";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->bindParam(":amount", $amount);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT b.*, p.name as patient_name 
                  FROM " . $this->table_name . " b
                  JOIN patients p ON b.patient_id = p.id
                  ORDER BY b.generated_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function markPaid($id) {
        $query = "UPDATE " . $this->table_name . " SET status = 'Paid' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
