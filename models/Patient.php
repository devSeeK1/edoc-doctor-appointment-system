<?php
/**
 * Patient model
 */

class Patient {
    private $database;
    
    public function __construct($database) {
        $this->database = $database;
    }
    
    /**
     * Get patient by email
     */
    public function getByEmail($email) {
        $email = Utils::sanitizeEmail($email);
        $stmt = $this->database->prepare("SELECT * FROM patient WHERE pemail=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Get patient by ID
     */
    public function getById($id) {
        $stmt = $this->database->prepare("SELECT * FROM patient WHERE pid=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Update patient information
     */
    public function update($id, $email, $name, $password, $nic, $tele, $address) {
        $email = Utils::sanitizeEmail($email);
        $tele = Utils::sanitizePhone($tele);
        
        // Validate email
        if (!Utils::validateEmail($email)) {
            return false;
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->database->prepare("UPDATE patient SET pemail=?, pname=?, ppassword=?, pnic=?, ptel=?, paddress=? WHERE pid=?");
        $stmt->bind_param("ssssssi", $email, $name, $hashedPassword, $nic, $tele, $address, $id);
        return $stmt->execute();
    }
    
    /**
     * Delete patient account
     */
    public function delete($email) {
        $email = Utils::sanitizeEmail($email);
        
        // Validate email
        if (!Utils::validateEmail($email)) {
            return false;
        }
        
        $stmt = $this->database->prepare("DELETE FROM webuser WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $stmt = $this->database->prepare("DELETE FROM patient WHERE pemail=?");
        $stmt->bind_param("s", $email);
        return $stmt->execute();
    }
    
    /**
     * Get statistics for dashboard
     */
    public function getDashboardStats($today) {
        // Patient count
        $stmt = $this->database->prepare("SELECT COUNT(*) as count FROM patient");
        $stmt->execute();
        $patientrow = $stmt->get_result();
        
        // Doctor count
        $stmt = $this->database->prepare("SELECT COUNT(*) as count FROM doctor");
        $stmt->execute();
        $doctorrow = $stmt->get_result();
        
        // Appointment count
        $stmt = $this->database->prepare("SELECT COUNT(*) as count FROM appointment WHERE appodate >= ?");
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $appointmentrow = $stmt->get_result();
        
        // Schedule count
        $stmt = $this->database->prepare("SELECT COUNT(*) as count FROM schedule WHERE scheduledate = ?");
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $schedulerow = $stmt->get_result();
        
        return [
            'patients' => $patientrow,
            'doctors' => $doctorrow,
            'appointments' => $appointmentrow,
            'schedules' => $schedulerow
        ];
    }
}