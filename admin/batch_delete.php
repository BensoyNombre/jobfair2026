<?php

require_once __DIR__ . "/auth.php";
require_admin();
require_once "../database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit();
}

$submittedIds = isset($_POST['ids']) && is_array($_POST['ids'])
    ? $_POST['ids']
    : [];
$ids = array_values(array_unique(array_filter(array_map('intval', $submittedIds))));

if (count($ids) === 0) {
    header("Location: dashboard.php");
    exit();
}

$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $conn->prepare(
    "DELETE FROM applicants WHERE id IN (" . $placeholders . ")"
);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$types = str_repeat('i', count($ids));
$bindValues = [$types];

foreach ($ids as $index => $id) {
    $bindValues[] = &$ids[$index];
}

call_user_func_array([$stmt, 'bind_param'], $bindValues);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: dashboard.php?deleted=" . count($ids));
    exit();
}

echo "Error deleting applicants: " . $stmt->error;
$stmt->close();
$conn->close();

?>