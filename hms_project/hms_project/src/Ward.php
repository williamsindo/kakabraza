<?php
class Ward {
    private $conn;
    private $table_bed = "beds";
    private $table_admission = "admissions";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getBeds() {
        $query = "SELECT * FROM " . $this->table_bed . " ORDER BY ward_number, bed_number";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function admitPatient($patient_id, $bed_id) {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO " . $this->table_admission . " SET patient_id=:patient_id, bed_id=:bed_id, admission_date=NOW()";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":patient_id", $patient_id);
            $stmt->bindParam(":bed_id", $bed_id);
            $stmt->execute();

            $updateBed = "UPDATE " . $this->table_bed . " SET is_occupied = 1 WHERE id = :bed_id";
            $stmtBed = $this->conn->prepare($updateBed);
            $stmtBed->bindParam(":bed_id", $bed_id);
            $stmtBed->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function dischargePatient($admission_id, $bed_id) {
        try {
            $this->conn->beginTransaction();

            $query = "UPDATE " . $this->table_admission . " SET discharge_date=NOW() WHERE id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $admission_id);
            $stmt->execute();

            $updateBed = "UPDATE " . $this->table_bed . " SET is_occupied = 0 WHERE id = :bed_id";
            $stmtBed = $this->conn->prepare($updateBed);
            $stmtBed->bindParam(":bed_id", $bed_id);
            $stmtBed->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    public function getActiveAdmissions() {
        $query = "SELECT a.id as admission_id, a.admission_date, p.name as patient_name, b.ward_number, b.bed_number, b.id as bed_id
                  FROM " . $this->table_admission . " a
                  JOIN patients p ON a.patient_id = p.id
                  JOIN beds b ON a.bed_id = b.id
                  WHERE a.discharge_date IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
