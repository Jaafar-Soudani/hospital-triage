import java.sql.*;

public class Main {
    public static void main(String[] args) throws SQLException {

        // Make sure you have the correct url, user and password
        Connection db = DriverManager.getConnection("jdbc:postgresql://localhost:5432/postgres",
                "postgres", "admin");


        // Creating Database if not already created
        db.createStatement().execute("CREATE SCHEMA IF NOT EXISTS hospitaldb AUTHORIZATION postgres;");


        // Set search path
        db.createStatement().execute("SET search_path TO hospitaldb");

        String tables =
                "CREATE TABLE IF NOT EXISTS patient (\n" +
                        "\tid SERIAL PRIMARY KEY,\n" +
                        "\tname VARCHAR(255) NOT NULL,\n" +
                        "\tcondition_severity INT NOT NULL CHECK (condition_severity BETWEEN 1 AND 10),\n" +
                        "\tcode CHAR(3) NOT NULL UNIQUE,\n" +
                        "\tarrival_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" +
                        "\tis_treated BOOLEAN NOT NULL DEFAULT FALSE\n" +
                        ");" +
                        "CREATE TABLE IF NOT EXISTS staff (\n" +
                        "\tid SERIAL PRIMARY KEY,\n" +
                        "\tname VARCHAR(255) NOT NULL,\n" +
                        "\tpassword VARCHAR(255) NOT NULL\n" +
                        ");";

        // Creating tables
        db.createStatement().execute(tables);

        // Populating Database

        String patients = "INSERT INTO patient (name, condition_severity, code, arrival_time, is_treated) VALUES\n" +
                        "('John Doe', 7, 'ABC', NOW(), FALSE),\n" +
                        "('Ava Mac', 6, 'BCA', NOW(), FALSE),\n" +
                        "('Ryan Yi', 8, 'DAC', NOW(), FALSE) ON CONFLICT (name) DO NOTHING";

        db.createStatement().execute(patients);
    }
}