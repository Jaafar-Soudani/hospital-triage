<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <nav>
        <h2>Admin Actions</h2>
        <form>
            <input type="submit" name="action" value="Register Patient" formaction="admin_registerPatient.php">
        </form>
        <form>
            <input type="submit" name="action" value="Treat Next Patient" formaction="admin_treatNextPatient.php">
        </form>
        <form>
            <input type="submit" name="action" value="View Patient List" formaction="admin_viewListPatient.php">
        </form>
    </nav>
</body>
</html>
