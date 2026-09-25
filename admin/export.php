<?php

require_once __DIR__ . "/auth.php";
require_admin();
require_once "../database.php";

$filename = "applicants-" . date("Y-m-d") . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
header("Pragma: no-cache");
header("Expires: 0");

$result = $conn->query(
    "SELECT last_name, first_name, middle_name, extension, sex, age,
            barangay, city_municipality, phone_number, email, created_at
     FROM applicants
     ORDER BY last_name ASC, first_name ASC"
);

if (!$result) {
    http_response_code(500);
    exit("Unable to export applicants.");
}

function exportCell($value) {
    $value = (string) $value;

    if ($value !== '' && preg_match('/^[=+\-@]/', $value)) {
        $value = "'" . $value;
    }

    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

?>
<table border="1">
    <tr>
        <th>Last Name</th>
        <th>First Name</th>
        <th>Middle Name</th>
        <th>Extension</th>
        <th>Sex</th>
        <th>Age</th>
        <th>Barangay</th>
        <th>City/Municipality</th>
        <th>Phone Number</th>
        <th>Email</th>
        <th>Registered At</th>
    </tr>
<?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo exportCell($row['last_name']); ?></td>
        <td><?php echo exportCell($row['first_name']); ?></td>
        <td><?php echo exportCell($row['middle_name']); ?></td>
        <td><?php echo exportCell($row['extension']); ?></td>
        <td><?php echo exportCell($row['sex']); ?></td>
        <td><?php echo exportCell($row['age']); ?></td>
        <td><?php echo exportCell($row['barangay']); ?></td>
        <td><?php echo exportCell($row['city_municipality']); ?></td>
        <td><?php echo exportCell($row['phone_number']); ?></td>
        <td><?php echo exportCell($row['email']); ?></td>
        <td><?php echo exportCell($row['created_at']); ?></td>
    </tr>
<?php endwhile; ?>
</table>
<?php

$result->free();
$conn->close();

?>
