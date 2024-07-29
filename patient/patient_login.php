<?php
// Start the session
session_start();

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
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the request is for patient login
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $code = $_POST['code'];

        // Prepare the SQL statement to check patient credentials
        $stmt = $conn->prepare("SELECT id, name, condition_severity, arrival_time, is_treated FROM hospitaldb.patient WHERE name = :name AND code = :code");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();

        // Check if the patient credentials are valid
        if ($patient = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Patient login successful
            // Store patient data in the session
            $_SESSION['patient_id'] = $patient['id'];
            $_SESSION['patient_name'] = $patient['name'];
            $_SESSION['patient_condition_severity'] = $patient['condition_severity'];
            $_SESSION['patient_arrival_time'] = $patient['arrival_time'];
            $_SESSION['patient_is_treated'] = $patient['is_treated'];

            // Redirect to patient dashboard
            header("Location: patient_index.php");
            exit;
        } else {
            $error = "Invalid patient name or code!";
        }
    }
} catch (PDOException $e) {
    $error = "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Login</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>Patient Login</h1>
        <?php if (isset($error)) : ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="text" name="name" placeholder="Patient Name" required>
            <input type="text" name="code" placeholder="Patient Code" required>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
