<?php
class MedicalRecord {
    private $conn;
    private $table_name = "medical_records";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($patient_id, $doctor_id, $diagnosis, $prescription, $lab_results_file) {
        $query = "INSERT INTO " . $this->table_name . " SET patient_id=:patient_id, doctor_id=:doctor_id, diagnosis=:diagnosis, prescription=:prescription, lab_results_path=:lab_results_path";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->bindParam(":diagnosis", $diagnosis);
        $stmt->bindParam(":prescription", $prescription);
        $stmt->bindParam(":lab_results_path", $lab_results_file);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readByPatient($patient_id) {
        $query = "SELECT m.*, u.full_name as doctor_name 
                  FROM " . $this->table_name . " m
                  JOIN doctors d ON m.doctor_id = d.id
                  JOIN users u ON d.user_id = u.id
                  WHERE m.patient_id = ?
                  ORDER BY m.visit_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $patient_id);
        $stmt->execute();
        return $stmt;
    }
}
