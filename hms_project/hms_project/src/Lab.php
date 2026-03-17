<?php
class Lab {
    private $conn;
    private $table_name = "lab_tests";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($patient_id, $test_type) {
        $query = "INSERT INTO " . $this->table_name . " SET patient_id=:patient_id, test_type=:test_type";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->bindParam(":test_type", $test_type);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT l.*, p.name as patient_name 
                  FROM " . $this->table_name . " l
                  JOIN patients p ON l.patient_id = p.id
                  ORDER BY l.test_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function updateResult($id, $result) {
        $query = "UPDATE " . $this->table_name . " SET result = :result WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":result", $result);
        $stmt->bindParam(":id", $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
