<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "applicant_system";

if (!preg_match('/^[A-Za-z0-9_]+$/', $database)) {
    die("Invalid database configuration.");
}

$conn = new mysqli($host, $username, $password);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$createDatabase = "CREATE DATABASE IF NOT EXISTS `" . $database . "`
                  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";

if (!$conn->query($createDatabase)) {
    die("Database setup failed: " . $conn->error);
}

if (!$conn->select_db($database)) {
    die("Database selection failed: " . $conn->error);
}

$createApplicantsTable = "CREATE TABLE IF NOT EXISTS applicants (
                            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            last_name VARCHAR(100) NOT NULL,
                            first_name VARCHAR(100) NOT NULL,
                            middle_name VARCHAR(100) NOT NULL DEFAULT '',
                            extension VARCHAR(20) NOT NULL DEFAULT '',
                            sex VARCHAR(20) NOT NULL,
                            age TINYINT UNSIGNED NOT NULL,
                            address VARCHAR(255) NOT NULL,
                            barangay VARCHAR(100) NOT NULL DEFAULT '',
                            city_municipality VARCHAR(100) NOT NULL DEFAULT '',
                            phone_number VARCHAR(30) NOT NULL,
                            email VARCHAR(255) NOT NULL,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!$conn->query($createApplicantsTable)) {
    die("Table setup failed: " . $conn->error);
}

$columnResult = $conn->query("SHOW COLUMNS FROM applicants LIKE 'barangay'");
if ($columnResult && $columnResult->num_rows === 0) {
    $conn->query("ALTER TABLE applicants ADD barangay VARCHAR(100) NOT NULL DEFAULT '' AFTER address");
}

$columnResult = $conn->query("SHOW COLUMNS FROM applicants LIKE 'city_municipality'");
if ($columnResult && $columnResult->num_rows === 0) {
    $conn->query("ALTER TABLE applicants ADD city_municipality VARCHAR(100) NOT NULL DEFAULT '' AFTER barangay");
}

$conn->query(
    "UPDATE applicants
     SET barangay = TRIM(SUBSTRING_INDEX(address, ',', 1)),
         city_municipality = CASE
             WHEN INSTR(address, ',') > 0
             THEN TRIM(SUBSTRING_INDEX(address, ',', -1))
             ELSE ''
         END
     WHERE barangay = '' AND city_municipality = '' AND address <> ''"
);

$conn->set_charset("utf8mb4");

?>
