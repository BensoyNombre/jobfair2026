<?php

require_once __DIR__ . "/auth.php";
require_admin();
require_once "../database.php";


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;


if ($id <= 0) {
    header("Location: dashboard.php");
    exit();
}


$sql = "DELETE FROM applicants WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}


$stmt->bind_param("i", $id);


if ($stmt->execute()) {

    $stmt->close();
    $conn->close();

    header("Location: dashboard.php?deleted=1");
    exit();

} else {

    echo "Error deleting applicant: " . $stmt->error;
}


$stmt->close();
$conn->close();

?>
