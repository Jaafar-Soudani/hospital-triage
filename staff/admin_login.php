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

    // Check if the request is for admin login
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare the SQL statement to check admin credentials
        $stmt = $conn->prepare("SELECT * FROM hospitaldb.staff WHERE name = :username AND password = :password");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->execute();

        // Check if the admin credentials are valid
        if ($stmt->rowCount() > 0) {
            // Admin login successful
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // Store admin data in the session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

            // Redirect to admin dashboard
            header("Location: admin_index.php");
            exit;
        } else {
            $error = "Invalid admin credentials!";
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
    <title>Admin Login</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h1>Admin Login</h1>
        <?php if (isset($error)) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
