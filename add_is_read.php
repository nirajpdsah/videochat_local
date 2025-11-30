<?php
/**
 * Railway MySQL Migration Script
 * Adds is_read column to signals table
 */

// Get credentials from Railway environment variables
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

// Create connection
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

echo "✅ Connected successfully<br><br>";

// Add is_read column
$sql = "ALTER TABLE signals 
        ADD COLUMN `is_read` TINYINT(1) DEFAULT 0 AFTER `call_type`";

if ($conn->query($sql) === TRUE) {
    echo "✅ Column 'is_read' added successfully!<br>";
} else {
    if (strpos($conn->error, 'Duplicate column') !== false) {
        echo "✅ Column 'is_read' already exists!<br>";
    } else {
        echo "❌ Error adding column: " . $conn->error . "<br>";
    }
}

// Update existing records
$updateSql = "UPDATE signals SET is_read = 0";
if ($conn->query($updateSql) === TRUE) {
    echo "✅ Updated existing records (affected rows: " . $conn->affected_rows . ")<br>";
} else {
    echo "❌ Error updating records: " . $conn->error . "<br>";
}

// Verify the column was added
echo "<br><strong>Verification:</strong><br>";
$result = $conn->query("SHOW COLUMNS FROM signals LIKE 'is_read'");

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Field: " . $row['Field'] . "<br>";
    echo "Type: " . $row['Type'] . "<br>";
    echo "Null: " . $row['Null'] . "<br>";
    echo "Default: " . ($row['Default'] ?? 'NULL') . "<br>";
} else {
    echo "⚠️ Column 'is_read' not found in table<br>";
}

// Show full table structure
echo "<br><strong>Current signals table structure:</strong><br>";
echo "<pre>";
$result = $conn->query("SHOW COLUMNS FROM signals");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " | " . $row['Type'] . " | " . 
             $row['Null'] . " | " . $row['Key'] . " | " . 
             ($row['Default'] ?? 'NULL') . "\n";
    }
}
echo "</pre>";

$conn->close();
echo "<br><strong>✅ Migration completed! Delete this file now for security.</strong>";
?>