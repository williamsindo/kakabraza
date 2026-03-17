<?php
class Doctor {
    private $conn;
    private $table_name = "doctors";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all doctors with user info
    public function readAll() {
        $query = "SELECT d.id, d.specialization, d.availability, u.full_name 
                  FROM " . $this->table_name . " d
                  JOIN users u ON d.user_id = u.id
                  ORDER BY u.full_name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get doctor by ID
    public function readOne($id) {
        $query = "SELECT d.id, d.specialization, d.availability, u.full_name 
                  FROM " . $this->table_name . " d
                  JOIN users u ON d.user_id = u.id
                  WHERE d.id = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
