<?php
// Start the session
session_start();

// Check if the patient is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
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

    // Prepare the SQL statement to get the patient's position in the queue
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS position
        FROM hospitaldb.patient
        WHERE is_treated = false
          AND (condition_severity > (SELECT condition_severity FROM hospitaldb.patient WHERE id = :patient_id)
            OR (condition_severity = (SELECT condition_severity FROM hospitaldb.patient WHERE id = :patient_id)
              AND arrival_time < (SELECT arrival_time FROM hospitaldb.patient WHERE id = :patient_id)))
    ");
    $stmt->bindParam(':patient_id', $_SESSION['patient_id'], PDO::PARAM_INT);
    $stmt->execute();
    $position = $stmt->fetchColumn();

    // Prepare the SQL statement to get the patients in front and their condition severity
    $stmt = $conn->prepare("
        SELECT condition_severity
        FROM hospitaldb.patient
        WHERE is_treated = false
          AND (condition_severity > (SELECT condition_severity FROM hospitaldb.patient WHERE id = :patient_id)
            OR (condition_severity = (SELECT condition_severity FROM hospitaldb.patient WHERE id = :patient_id)
              AND arrival_time < (SELECT arrival_time FROM hospitaldb.patient WHERE id = :patient_id)))
        ORDER BY condition_severity DESC, arrival_time ASC
        LIMIT :position
    ");
    $stmt->bindParam(':patient_id', $_SESSION['patient_id'], PDO::PARAM_INT);
    $stmt->bindParam(':position', $position, PDO::PARAM_INT);
    $stmt->execute();
    $patientsInFront = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Calculate the approximate wait time based on patients in front and their condition severity
    $avgWaitTime = 0;
    foreach ($patientsInFront as $severity) {
        $avgWaitTime += $severity * 3; // Assuming each severity level adds 3 minutes of wait time
    }

    // Format the wait time for display
    $waitTimeFormatted = sprintf("%02d:%02d", floor($avgWaitTime / 60), $avgWaitTime % 60);

    // Get the number of patients in front
    $patientsInFrontCount = $position;
} catch (PDOException $e) {
    $error = "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>Patient Dashboard</h1>
        <?php if (isset($error)) : ?>
            <p class="error"><?= $error ?></p>
        <?php else : ?>
            <div class="patient-info">
                <p>Patient Name: <?= $_SESSION['patient_name'] ?></p>
                <p>Condition Severity: <?= $_SESSION['patient_condition_severity'] ?></p>
                <p>Arrival Time: <?= $_SESSION['patient_arrival_time'] ?></p>
                <p>Approximate Wait Time: <?= $waitTimeFormatted ?></p>
                <p>Patients in Front: <?= $patientsInFrontCount ?></p>
            </div>
        <?php endif; ?>
        <a href="../index.php">Logout</a>
    </div>
</body>
</html>
