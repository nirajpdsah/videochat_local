<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get Railway environment variables
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Update - Add call-request Signal Type</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            max-width: 900px; 
            margin: 40px auto; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            background: white; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2); 
        }
        h1 { 
            color: #333; 
            margin-bottom: 10px;
            font-size: 28px;
        }
        h2 {
            color: #555;
            font-size: 20px;
            margin-top: 30px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        .success { 
            color: #0f9d58; 
            padding: 15px; 
            background: #e6f4ea; 
            border-left: 5px solid #0f9d58; 
            margin: 15px 0; 
            border-radius: 4px;
            font-weight: 500;
        }
        .error { 
            color: #d93025; 
            padding: 15px; 
            background: #fce8e6; 
            border-left: 5px solid #d93025; 
            margin: 15px 0; 
            border-radius: 4px;
            font-weight: 500;
        }
        .info { 
            color: #1967d2; 
            padding: 15px; 
            background: #e8f0fe; 
            border-left: 5px solid #1967d2; 
            margin: 15px 0; 
            border-radius: 4px;
        }
        .warning { 
            color: #f9ab00; 
            padding: 15px; 
            background: #fef7e0; 
            border-left: 5px solid #f9ab00; 
            margin: 15px 0; 
            border-radius: 4px;
            font-weight: 500;
        }
        .btn { 
            display: inline-block; 
            padding: 12px 30px; 
            background: #667eea; 
            color: white; 
            text-decoration: none; 
            border-radius: 6px; 
            margin: 10px 10px 10px 0;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn:hover { 
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .code { 
            background: #1e1e1e; 
            color: #d4d4d4; 
            padding: 20px; 
            border-radius: 6px; 
            font-family: 'Courier New', monospace; 
            margin: 15px 0;
            overflow-x: auto;
            font-size: 14px;
            line-height: 1.6;
        }
        .code .keyword { color: #569cd6; }
        .code .string { color: #ce9178; }
        pre { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 6px; 
            overflow-x: auto;
            border: 1px solid #e0e0e0;
            margin: 15px 0;
        }
        .step {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .checkmark {
            color: #0f9d58;
            font-size: 24px;
            margin-right: 10px;
        }
        .crossmark {
            color: #d93025;
            font-size: 24px;
            margin-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        table tr:hover {
            background: #f8f9fa;
        }
        .highlight {
            background: #fff3cd;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Database Update</h1>
        <p class="subtitle">Adding 'call-request' signal type to the signals table</p>

<?php

echo "<div class='step'>";
echo "<h2>Step 1: Connection</h2>";
echo "<div class='info'><strong>🔌 Connecting to database...</strong><br>";
echo "Host: " . $host . "<br>";
echo "Database: " . $db . "<br>";
echo "Port: " . $port . "</div>";

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    echo "<div class='error'><span class='crossmark'>✗</span><strong>Connection Failed!</strong><br>";
    echo "Error: " . $conn->connect_error . "</div>";
    echo "</div></div></body></html>";
    exit;
}

echo "<div class='success'><span class='checkmark'>✓</span><strong>Connected Successfully!</strong></div>";
echo "</div>";

// Show current structure
echo "<div class='step'>";
echo "<h2>Step 2: Current Structure</h2>";
echo "<p>Checking current <span class='highlight'>signal_type</span> column configuration...</p>";

$result = $conn->query("SHOW COLUMNS FROM signals LIKE 'signal_type'");
if ($result && $row = $result->fetch_assoc()) {
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
    echo "<td><code>" . htmlspecialchars($row['Type']) . "</code></td>";
    echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
    echo "</tr>";
    echo "</table>";
    
    if (strpos($row['Type'], 'call-request') !== false) {
        echo "<div class='warning'><span class='checkmark'>✓</span><strong>Note:</strong> The 'call-request' signal type already exists in the database. No update needed.</div>";
        $needs_update = false;
    } else {
        echo "<div class='info'><strong>Current ENUM values:</strong> offer, answer, ice-candidate<br>";
        echo "<strong>Missing:</strong> call-request</div>";
        $needs_update = true;
    }
} else {
    echo "<div class='error'><span class='crossmark'>✗</span>Could not retrieve column information.</div>";
    $needs_update = false;
}
echo "</div>";

// Update the signals table
if ($needs_update) {
    echo "<div class='step'>";
    echo "<h2>Step 3: Updating Database</h2>";
    echo "<p>Running ALTER TABLE command...</p>";

    echo "<div class='code'>";
    echo "<span class='keyword'>ALTER TABLE</span> signals<br>";
    echo "<span class='keyword'>MODIFY</span> signal_type <span class='keyword'>ENUM</span>(<span class='string'>'offer'</span>, <span class='string'>'answer'</span>, <span class='string'>'ice-candidate'</span>, <span class='string'>'call-request'</span>) <span class='keyword'>NOT NULL</span>;";
    echo "</div>";

    $sql = "ALTER TABLE signals 
            MODIFY signal_type ENUM('offer', 'answer', 'ice-candidate', 'call-request') NOT NULL";

    if ($conn->query($sql) === TRUE) {
        echo "<div class='success'><span class='checkmark'>✓</span><strong>Update Successful!</strong><br>";
        echo "The signals table has been updated with the new 'call-request' signal type.</div>";
    } else {
        echo "<div class='error'><span class='crossmark'>✗</span><strong>Update Failed!</strong><br>";
        echo "Error: " . $conn->error . "</div>";
    }
    echo "</div>";

    // Verify the update
    echo "<div class='step'>";
    echo "<h2>Step 4: Verification</h2>";
    echo "<p>Confirming the update was applied correctly...</p>";

    $result = $conn->query("SHOW COLUMNS FROM signals LIKE 'signal_type'");
    if ($result && $row = $result->fetch_assoc()) {
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td><code>" . htmlspecialchars($row['Type']) . "</code></td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
        echo "</table>";
        
        if (strpos($row['Type'], 'call-request') !== false) {
            echo "<div class='success'><span class='checkmark'>✓</span><strong>Verification Successful!</strong><br>";
            echo "The 'call-request' signal type is now available in the database.<br>";
            echo "<strong>Updated ENUM values:</strong> offer, answer, ice-candidate, <span class='highlight'>call-request</span></div>";
        } else {
            echo "<div class='error'><span class='crossmark'>✗</span><strong>Verification Failed!</strong><br>";
            echo "The 'call-request' value was not found in the ENUM. Please check manually.</div>";
        }
    }
    echo "</div>";
}

$conn->close();

echo "<div class='success' style='text-align: center; padding: 30px; margin-top: 30px; font-size: 18px;'>";
echo "<h2 style='color: #0f9d58; margin-top: 0;'>🎉 Database Update Complete!</h2>";
echo "<p style='margin: 10px 0;'>Your signals table now supports the 'call-request' signal type.</p>";
echo "<p style='font-size: 14px; color: #666;'>The video chat application can now handle incoming call requests properly.</p>";
echo "</div>";

echo "<div class='warning'>";
echo "<h3 style='margin-top: 0;'>⚠️ CRITICAL: Delete This File</h3>";
echo "<p><strong>This file must be removed from your server immediately!</strong></p>";
echo "<p>Run these commands to delete it:</p>";
echo "<div class='code'>";
echo "git rm update-db.php<br>";
echo "git commit -m <span class='string'>\"Remove database update script\"</span><br>";
echo "git push";
echo "</div>";
echo "</div>";

echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='index.php' class='btn'>🏠 Go to Home Page</a>";
echo "<a href='dashboard.php' class='btn'>📱 Open Dashboard</a>";
echo "</div>";

?>

    </div>
</body>
</html>