<?php
class Patient {
    private $conn;
    private $table_name = "patients";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($name, $dob, $gender, $contact, $address, $medical_history) {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, dob=:dob, gender=:gender, contact=:contact, address=:address, medical_history=:medical_history";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":dob", $dob);
        $stmt->bindParam(":gender", $gender);
        $stmt->bindParam(":contact", $contact);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":medical_history", $medical_history);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
