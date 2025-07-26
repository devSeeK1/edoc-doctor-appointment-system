<?php
// Minimal test file for patient directory
session_start();

echo "<h1>Basic PHP Test</h1>";
echo "<p>If you can see this, PHP is working in the patient directory.</p>";

// Check if session is working
if (isset($_SESSION['user'])) {
    echo "<p>Session user: " . htmlspecialchars($_SESSION['user']) . "</p>";
} else {
    echo "<p>No user in session</p>";
}

// Try to include the connection file
if (file_exists('../connection.php')) {
    echo "<p>Connection file exists</p>";
    
    // Try to include it
    include("../connection.php");
    
    if (isset($database)) {
        echo "<p>Database connection successful</p>";
        
        // Try a simple query
        $result = $database->query("SELECT COUNT(*) as count FROM patient");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>Number of patients: " . $row['count'] . "</p>";
        } else {
            echo "<p>Query failed: " . $database->error . "</p>";
        }
    } else {
        echo "<p>Database connection failed</p>";
    }
} else {
    echo "<p>Connection file does not exist</p>";
}
?>