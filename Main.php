<?php
$host = "localhost";
$dbname = "postgres";
$user = "postgres";
$password = "admin";

try {
    // Create a PostgreSQL database connection
    $host = "localhost";
    $dbname = "postgres";
    $user = "postgres";
    $password = "admin";

    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

    // Drop the hospitaldb schema if it exists, along with all its objects
    $conn->exec("DROP SCHEMA IF EXISTS hospitaldb CASCADE;");

    // Creating Database if not already created
    $conn->exec("CREATE SCHEMA IF NOT EXISTS hospitaldb AUTHORIZATION postgres;");

    // Set search path
    $conn->exec("SET search_path TO hospitaldb;");

    $tables = "CREATE TABLE IF NOT EXISTS patient (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE,
        condition_severity INT NOT NULL CHECK (condition_severity BETWEEN 1 AND 10),
        code CHAR(3) NOT NULL UNIQUE,
        arrival_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        is_treated BOOLEAN NOT NULL DEFAULT FALSE
    );

    CREATE TABLE IF NOT EXISTS staff (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    );";

    // Creating tables
    $conn->exec($tables);

    // Populating Database
    $patients = "INSERT INTO patient (name, condition_severity, code, arrival_time, is_treated)
    VALUES
        ('John Doe', 7, 'ABC', NOW(), FALSE),
        ('Ava Mac', 6, 'BCA', NOW(), FALSE),
        ('Ryan Yi', 8, 'DAC', NOW(), FALSE)
    ON CONFLICT (name) DO NOTHING;";

    $conn->exec($patients);

    // Populating staff table
    $staff = "INSERT INTO staff (name, password)
    VALUES
        ('John Smith', 'password123'),
        ('Jane Doe', 'securepassword'),
        ('Michael Johnson', 'admin123')
    ON CONFLICT (name) DO NOTHING;";

    $conn->exec($staff);


    echo "Database operations completed successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
