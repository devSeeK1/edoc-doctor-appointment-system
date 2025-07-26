<?php
// Check table structure

include("connection.php");

$tables = ['patient', 'webuser'];

foreach($tables as $table) {
    echo "Structure of $table table:\n";
    $result = $database->query("DESCRIBE $table");
    if($result) {
        while($row = $result->fetch_assoc()) {
            echo "  " . $row['Field'] . " " . $row['Type'] . " " . 
                 ($row['Null'] == 'NO' ? 'NOT NULL' : 'NULL') . " " . 
                 ($row['Key'] ? $row['Key'] : '') . " " . 
                 ($row['Default'] !== null ? "DEFAULT " . $row['Default'] : '') . "\n";
        }
    } else {
        echo "Error describing table: " . $database->error . "\n";
    }
    echo "\n";
}
?>