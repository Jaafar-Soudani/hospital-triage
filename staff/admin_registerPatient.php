<!DOCTYPE html>
<html>
<head>
    <title>Register Patient</title>
</head>
<body>
    <h2>Register Patient</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Patient Name: <input type="text" name="name" required><br><br>
        Condition Severity (1-10): <input type="number" name="condition_severity" min="1" max="10" required><br><br>
        <input type="submit" name="submit" value="Register">
    </form>
</body>
</html>

<?php
$host = "localhost";
$dbname = "postgres";
$user = "postgres";
$password = "admin";

try {
    // Create a PostgreSQL database connection
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

    // Display a message if the connection is successful
} catch (PDOException $e) {
    // Display an error message if the connection fails 
    echo "Connection failed: " . $e->getMessage();
}

function generateCode() {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < 3; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $condition_severity = $_POST["condition_severity"];
    $code = generateCode();

    $sql = "INSERT INTO Patients (name, condition_severity, code, arrival_time, is_treated)
            VALUES ('$name', '$condition_severity', '$code', NOW(), FALSE)";

    if ($conn->query($sql) === TRUE) {
        echo "Patient registered successfully with code: " . $code;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();