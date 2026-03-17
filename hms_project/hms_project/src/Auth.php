<?php

class Auth {
    private $db;
    private $table_name = "users";

    public function __construct($db) {
        $this->db = $db;
    }

    // Login user
	public function login($username, $password) {
    $query = "SELECT id, username, password_hash, role, full_name, email
              FROM " . $this->table_name . "
              WHERE username = :username
              LIMIT 1";
			  
              
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if ($password === $row['password_hash']) {

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['user_id']   = $row['id'];
            $_SESSION['username']  = $row['username'];
            $_SESSION['role']      = $row['role'];
            $_SESSION['full_name'] = $row['full_name'];

            return true;
        }
    }

    return false;
}

    /*public function login($username, $password) {
        $query = "SELECT id, username, password_hash, role, full_name, email FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
             if ($password === $row['password_hash']) {
                // Start Session if not already started
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['full_name'] = $row['full_name'];
                
                return true;
            }
        }
        return false;
    }*/

    // Check if user is logged in
    public function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    // Check for specific role access
    public function requireRole($allowed_roles) {
        if (!$this->isLoggedIn()) {
            header("Location: index.php");
            exit;
        }

        if (!in_array($_SESSION['role'], $allowed_roles)) {
            echo "Access Denied. You do not have permission to view this page.";
            exit;
        }
    }

    // Logout
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header("Location: index.php");
        exit;
    }
    
    // Get current user data
    public function getUser() {
        if ($this->isLoggedIn()) {
             return $_SESSION;
        }
        return null;
    }
}
