<?php
// Start the session
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

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

    // Prepare the SQL statement to get the next patient to be treated
    $stmt = $conn->prepare("
        SELECT id, name, condition_severity, arrival_time
        FROM hospitaldb.patient
        WHERE is_treated = false
        ORDER BY condition_severity DESC, arrival_time ASC
        LIMIT 1
    ");
    $stmt->execute();

    // Check if there are any patients waiting to be treated
    if ($patient = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Update the patient's status to treated
        if (isset($_POST['treat_patient'])) {
            $updateStmt = $conn->prepare("
                UPDATE hospitaldb.patient
                SET is_treated = true
                WHERE id = :id
            ");
            $updateStmt->bindParam(':id', $patient['id'], PDO::PARAM_INT);
            $updateStmt->execute();

            // Redirect to the same page to refresh the data
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        // Display the patient information
        $patientInfo = "
            <h2>Next Patient to be Treated</h2>
            <p>Patient ID: {$patient['id']}</p>
            <p>Name: {$patient['name']}</p>
            <p>Condition Severity: {$patient['condition_severity']}</p>
            <p>Arrival Time: {$patient['arrival_time']}</p>
            <form method='post'>
                <input type='submit' name='treat_patient' value='Treat Patient'>
            </form>
        ";
    } else {
        $patientInfo = "<p>No patients waiting to be treated.</p>";
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
    <title>Treat Next Patient</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>Treat Next Patient</h1>
        <?php if (isset($error)) : ?>
            <p class="error"><?= $error ?></p>
        <?php else : ?>
            <?= $patientInfo ?>
        <?php endif; ?>
        <form class="form-column">
            <input type="submit" name="action" value="Return to Admin Menu" formaction="admin_index.php">
        </form>
    </div>
</body>
</html>
