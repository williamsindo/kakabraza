<?php
class Appointment {
    private $conn;
    private $table_name = "appointments";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($patient_id, $doctor_id, $appointment_date, $reason) {
        $query = "INSERT INTO " . $this->table_name . " SET patient_id=:patient_id, doctor_id=:doctor_id, appointment_date=:appointment_date, reason=:reason, status='Booked'";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->bindParam(":appointment_date", $appointment_date);
        $stmt->bindParam(":reason", $reason);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT a.id, a.appointment_date, a.reason, a.status, p.name as patient_name, u.full_name as doctor_name 
                  FROM " . $this->table_name . " a
                  JOIN patients p ON a.patient_id = p.id
                  JOIN doctors d ON a.doctor_id = d.id
                  JOIN users u ON d.user_id = u.id
                  ORDER BY a.appointment_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
