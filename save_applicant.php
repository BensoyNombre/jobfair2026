<?php

require_once "database.php";

$last_name = trim(isset($_POST['last_name']) ? $_POST['last_name'] : '');
$first_name = trim(isset($_POST['first_name']) ? $_POST['first_name'] : '');
$middle_name = trim(isset($_POST['middle_name']) ? $_POST['middle_name'] : '');
$extension = trim(isset($_POST['extension']) ? $_POST['extension'] : '');
$sex = trim(isset($_POST['sex']) ? $_POST['sex'] : '');
$age = intval(isset($_POST['age']) ? $_POST['age'] : 0);
$barangay = trim(isset($_POST['barangay']) ? $_POST['barangay'] : '');
$city_municipality = trim(isset($_POST['city_municipality']) ? $_POST['city_municipality'] : '');
$address = $barangay . ', ' . $city_municipality;
$phone_number = trim(isset($_POST['phone_number']) ? $_POST['phone_number'] : '');
$email = trim(isset($_POST['email']) ? $_POST['email'] : '');


if (
    empty($last_name) ||
    empty($first_name) ||
    empty($sex) ||
    $age <= 0 ||
    empty($barangay) ||
    empty($city_municipality) ||
    empty($phone_number) ||
    empty($email)
) {
    die("Please complete all required fields.");
}


if (!preg_match('/^09[0-9]{9}$/', $phone_number)) {
    die("Please enter a valid 11-digit mobile number starting with 09.");
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Please enter a valid email address.");
}


$sql = "INSERT INTO applicants
        (
            last_name,
            first_name,
            middle_name,
            extension,
            sex,
            age,
            address,
            barangay,
            city_municipality,
            phone_number,
            email
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}


$stmt->bind_param(
    "sssssisssss",
    $last_name,
    $first_name,
    $middle_name,
    $extension,
    $sex,
    $age,
    $address,
    $barangay,
    $city_municipality,
    $phone_number,
    $email
);


if ($stmt->execute()) {

    $stmt->close();
    $conn->close();

    header("Location: index.php?success=1");
    exit();

} else {

    echo "Error saving applicant: " . $stmt->error;
}


$stmt->close();
$conn->close();

?>
