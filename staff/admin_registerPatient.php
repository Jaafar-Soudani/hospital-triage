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
    $condition_severity = intval($_POST["condition_severity"]);
    $code = generateCode();


    $stmt = $conn->prepare("INSERT INTO hospitaldb.patient (name, condition_severity, code, arrival_time, is_treated) VALUES (:name, :condition_severity, :code, NOW(), FALSE)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':condition_severity', $condition_severity, PDO::PARAM_INT);
    $stmt->bindParam(':code', $code);

    if ($stmt->execute()) {
        echo "Patient registered successfully with code: " . strval($code);
    } else {
        echo "Could not register patient. Error: " . $stmt->errorInfo()[2];
    }

}

$conn->close();