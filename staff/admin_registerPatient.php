<!DOCTYPE html>
<html>
<head>
    <title>Register Patient</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h2>Register Patient</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="form-column">
            <div class="form-group">
                <label for="name">Patient Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="condition_severity">Condition Severity (1-10):</label>
                <input type="number" id="condition_severity" name="condition_severity" min="1" max="10" required>
            </div>
            <input type="submit" name="submit" value="Register">
        </form>
        <form class="form-column">
            <input type="submit" name="action" value="Return to Admin Menu" formaction="admin_index.php">
        </form>
    </div>
</body>
</html>


<?php
// connect to the database
$filename = "../dbCreds.json";
$file = fopen( $filename, "r" );

if( $file == false ) {
echo ( "Error in opening file" );
exit();
}

$filesize = filesize( $filename );
$filetext = fread( $file, $filesize );
fclose( $file );

$login_arr = json_decode($filetext, true);

$host = $login_arr["host"];
$port = $login_arr["port"];
$dbname = $login_arr["db_name"];
$user = $login_arr["db_username"];
$password = $login_arr["db_password"];
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