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

    // Prepare the SQL statement with specific columns
    $stmt = $conn->prepare("SELECT * FROM hospitaldb.patient");

    // Execute the prepared statement
    $stmt->execute();

    // Get the result set
    $rowCount = $stmt->rowCount();
    if ($rowCount > 0) {
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $patients = array(); // Set an empty array if no records found
    }

    // Close the database connection
    $conn = null;
} catch (PDOException $e) {
    // Display an error message if the connection fails
    echo "Connection failed: " . $e->getMessage();
    exit(); // Exit the script if the connection fails
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient List</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
    <h1>Patient List</h1>
    <table>
        <thead>
            <tr>
                <th>Patient ID</th>
                <th>Name</th>
                <th>Condition Severity</th>
                <th>Arrival Time</th>
                <th>Code</th>
                <th>Has Been Treated</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($patients as $patient): ?>
            <tr>
                <td><?php echo $patient['id']; ?></td>
                <td><?php echo $patient['name']; ?></td>
                <td><?php echo $patient['condition_severity']; ?></td>
                <td><?php echo $patient['arrival_time']; ?></td>
                <td><?php echo $patient['code']; ?></td>
                <td><?php echo ($patient['is_treated']) ? 'Yes' : 'No'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form class="form-column">
        <input type="submit" name="action" value="Return to Admin Menu" formaction="admin_index.php">
    </form>
    </div>
</body>
</html>

