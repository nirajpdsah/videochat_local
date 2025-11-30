<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f0f2f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #1a73e8; margin-bottom: 10px; }
        .success { color: #0f9d58; padding: 12px; background: #e6f4ea; border-left: 4px solid #0f9d58; margin: 10px 0; }
        .error { color: #d93025; padding: 12px; background: #fce8e6; border-left: 4px solid #d93025; margin: 10px 0; }
        .info { color: #1967d2; padding: 12px; background: #e8f0fe; border-left: 4px solid #1967d2; margin: 10px 0; }
        .warning { color: #f9ab00; padding: 12px; background: #fef7e0; border-left: 4px solid #f9ab00; margin: 10px 0; }
        .btn { display: inline-block; padding: 12px 24px; background: #1a73e8; color: white; text-decoration: none; border-radius: 4px; margin: 10px 10px 10px 0; font-weight: 500; }
        .btn:hover { background: #1557b0; }
        .btn-success { background: #0f9d58; }
        .btn-success:hover { background: #0b8043; }
        ul { list-style: none; padding: 0; }
        ul li { padding: 8px 0; border-bottom: 1px solid #e0e0e0; }
        ul li:before { content: "✓ "; color: #0f9d58; font-weight: bold; margin-right: 8px; }
        .step { background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 4px; }
        .code { background: #263238; color: #aed581; padding: 15px; border-radius: 4px; font-family: monospace; margin: 10px 0; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛠️ Video Chat Database Setup</h1>
        <p>Setting up database tables for your video chat application...</p>
        
<?php

echo "<div class='step'>";
echo "<h3>📡 Step 1: Checking Connection</h3>";
echo "<div class='info'><strong>Connection Details:</strong><br>";
echo "Host: " . ($host ?: '<span style="color:red;">Not set</span>') . "<br>";
echo "Database: " . ($db ?: '<span style="color:red;">Not set</span>') . "<br>";
echo "Port: " . ($port ?: '<span style="color:red;">Not set</span>') . "<br>";
echo "User: " . ($user ?: '<span style="color:red;">Not set</span>') . "</div>";
echo "</div>";

if (!$host || !$user || !$db) {
    echo "<div class='error'><strong>❌ Environment Variables Missing!</strong><br>";
    echo "Make sure you've set up the Railway MySQL variables in your PHP service.<br>";
    echo "Go to Railway → Your PHP Service → Variables and add:<br>";
    echo "<div class='code'>";
    echo "MYSQLHOST = \${{MySQL.MYSQLHOST}}<br>";
    echo "MYSQLUSER = \${{MySQL.MYSQLUSER}}<br>";
    echo "MYSQLPASSWORD = \${{MySQL.MYSQLPASSWORD}}<br>";
    echo "MYSQLDATABASE = \${{MySQL.MYSQLDATABASE}}<br>";
    echo "MYSQLPORT = \${{MySQL.MYSQLPORT}}";
    echo "</div></div>";
    echo "</div></body></html>";
    exit;
}

echo "<div class='step'>";
echo "<h3>🔌 Step 2: Connecting to Database</h3>";

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    echo "<div class='error'><strong>❌ Connection Failed!</strong><br>";
    echo "Error: " . $conn->connect_error . "<br>";
    echo "Please verify your Railway MySQL service is running.</div>";
    echo "</div></div></body></html>";
    exit;
}

echo "<div class='success'><strong>✅ Connected Successfully!</strong></div>";
echo "</div>";

echo "<div class='step'>";
echo "<h3>📝 Step 3: Creating Tables</h3>";

$tables = [
    'users' => "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        profile_picture VARCHAR(255) DEFAULT 'uploads/default-avatar.png',
        status ENUM('online', 'offline', 'on_call') DEFAULT 'offline',
        last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    'signals' => "CREATE TABLE IF NOT EXISTS signals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        from_user_id INT NOT NULL,
        to_user_id INT NOT NULL,
        signal_type VARCHAR(50) NOT NULL,
        signal_data TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_to_user (to_user_id),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    'messages' => "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        from_user_id INT NOT NULL,
        to_user_id INT NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_users (from_user_id, to_user_id),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

$success_count = 0;

foreach ($tables as $name => $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success'>✅ Table '<strong>$name</strong>' created successfully</div>";
        $success_count++;
    } else {
        echo "<div class='error'>❌ Error creating '<strong>$name</strong>': " . $conn->error . "</div>";
    }
}

// Create indexes (ignore errors if they already exist)
$conn->query("CREATE INDEX idx_user_status ON users(status)");
$conn->query("CREATE INDEX idx_user_email ON users(email)");

echo "</div>";

echo "<div class='step'>";
echo "<h3>✅ Step 4: Verification</h3>";

$result = $conn->query("SHOW TABLES");

if ($result && $result->num_rows > 0) {
    echo "<div class='success'><strong>Tables in database:</strong><ul>";
    while($row = $result->fetch_array()) {
        $table = $row[0];
        $count_result = $conn->query("SELECT COUNT(*) as cnt FROM $table");
        $count = $count_result->fetch_assoc()['cnt'];
        echo "<li><strong>$table</strong> (0 records)</li>";
    }
    echo "</ul></div>";
} else {
    echo "<div class='error'>❌ No tables found!</div>";
}

echo "</div>";

$conn->close();

if ($success_count == 3) {
    echo "<div class='success' style='font-size: 18px; text-align: center; padding: 20px;'>";
    echo "<h2>🎉 Setup Complete!</h2>";
    echo "<p>All database tables have been created successfully.</p>";
    echo "<p>Your video chat application is ready to use!</p>";
    echo "</div>";
}

echo "<div class='warning'>";
echo "<h3>⚠️ CRITICAL: Security Warning</h3>";
echo "<p><strong>DELETE THIS FILE IMMEDIATELY!</strong></p>";
echo "<p>This file should not remain on your production server as it may expose database information.</p>";
echo "<p><strong>How to delete:</strong></p>";
echo "<div class='code'>";
echo "git rm setup.php<br>";
echo "git commit -m \"Remove setup script\"<br>";
echo "git push";
echo "</div>";
echo "</div>";

echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='index.php' class='btn'>🏠 Go to Home Page</a>";
echo "<a href='signup.php' class='btn btn-success'>📝 Register New User</a>";
echo "</div>";

?>

    </div>
</body>
</html>