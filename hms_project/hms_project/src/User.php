<?php
class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new user
    public function create($username, $password, $role, $full_name, $email) {
        $query = "INSERT INTO " . $this->table_name . " SET username=:username, password_hash=:password, role=:role, full_name=:full_name, email=:email";
        
        $stmt = $this->conn->prepare($query);
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":full_name", $full_name);
        $stmt->bindParam(":email", $email);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read all users
    public function readAll() {
        $query = "SELECT id, username, role, full_name, email, created_at FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Delete user
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
