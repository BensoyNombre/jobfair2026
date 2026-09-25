<?php

require_once __DIR__ . "/auth.php";
require_admin();
require_once "../database.php";


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Invalid applicant ID.");
}


$sql = "SELECT * FROM applicants WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {
    die("Applicant not found.");
}

$applicant = $result->fetch_assoc();

$applicant['barangay'] = !empty($applicant['barangay'])
    ? $applicant['barangay']
    : trim(explode(',', $applicant['address'], 2)[0]);
$addressParts = explode(',', $applicant['address'], 2);
$applicant['city_municipality'] = !empty($applicant['city_municipality'])
    ? $applicant['city_municipality']
    : (isset($addressParts[1]) ? trim($addressParts[1]) : '');

$stmt->close();


if ($_SERVER["REQUEST_METHOD"] === "POST") {

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

        $error = "Please complete all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        $updateSQL = "UPDATE applicants SET
                        last_name = ?,
                        first_name = ?,
                        middle_name = ?,
                        extension = ?,
                        sex = ?,
                        age = ?,
                        address = ?,
                        barangay = ?,
                        city_municipality = ?,
                        phone_number = ?,
                        email = ?
                      WHERE id = ?";


        $updateStmt = $conn->prepare($updateSQL);

        if (!$updateStmt) {
            $error = "Database error: " . $conn->error;
        } else {


            $updateStmt->bind_param(
                "sssssisssssi",
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
                $email,
                $id
            );


            if ($updateStmt->execute()) {

            $updateStmt->close();
            $conn->close();

            header("Location: dashboard.php?updated=1");
            exit();

            } else {

            $error = "Error updating applicant: " . $updateStmt->error;

                $updateStmt->close();
            }
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Applicant</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

</head>


<body>

    <div class="container">

        <div class="form-box">

            <h1>Edit Applicant</h1>

            <p class="subtitle">
                Update the applicant's information
            </p>


            <?php if (isset($error)): ?>

                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>


            <form
                method="POST"
                action="edit.php?id=<?php echo $id; ?>"
            >



                <div class="row">

                    <div class="form-group">

                        <label for="last_name">
                            Last Name
                        </label>

                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="<?php echo htmlspecialchars($applicant['last_name']); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="first_name">
                            First Name
                        </label>

                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="<?php echo htmlspecialchars($applicant['first_name']); ?>"
                            required
                        >

                    </div>

                </div>



                <div class="row">

                    <div class="form-group">

                        <label for="middle_name">
                            Middle Name
                            <span>(If applicable)</span>
                        </label>

                        <input
                            type="text"
                            id="middle_name"
                            name="middle_name"
                            value="<?php echo htmlspecialchars($applicant['middle_name']); ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label for="extension">
                            Extension
                            <span>(If applicable)</span>
                        </label>

                        <input
                            type="text"
                            id="extension"
                            name="extension"
                            value="<?php echo htmlspecialchars($applicant['extension']); ?>"
                            placeholder="Jr., Sr., III"
                        >

                    </div>

                </div>



                <div class="row">

                    <div class="form-group">

                        <label for="sex">
                            Sex
                        </label>

                        <select
                            id="sex"
                            name="sex"
                            required
                        >

                            <option
                                value="Male"
                                <?php echo ($applicant['sex'] === 'Male') ? 'selected' : ''; ?>
                            >
                                Male
                            </option>

                            <option
                                value="Female"
                                <?php echo ($applicant['sex'] === 'Female') ? 'selected' : ''; ?>
                            >
                                Female
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="age">
                            Age
                        </label>

                        <input
                            type="number"
                            id="age"
                            name="age"
                            min="1"
                            max="120"
                            value="<?php echo htmlspecialchars($applicant['age']); ?>"
                            required
                        >

                    </div>

                </div>



                <div class="row">

                    <div class="form-group">

                        <label for="barangay">
                            Barangay
                        </label>

                        <input
                            type="text"
                            id="barangay"
                            name="barangay"
                            value="<?php echo htmlspecialchars($applicant['barangay']); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="city_municipality">
                            City/Municipality
                        </label>

                        <input
                            type="text"
                            id="city_municipality"
                            name="city_municipality"
                            value="<?php echo htmlspecialchars($applicant['city_municipality']); ?>"
                            required
                        >

                    </div>

                </div>



                <div class="row">

                    <div class="form-group">

                        <label for="phone_number">
                            Phone Number
                        </label>

                        <input
                            type="tel"
                            id="phone_number"
                            name="phone_number"
                            value="<?php echo htmlspecialchars($applicant['phone_number']); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="email">
                            Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo htmlspecialchars($applicant['email']); ?>"
                            required
                        >

                    </div>

                </div>



                <div class="edit-buttons">

                    <a
                        href="dashboard.php"
                        class="cancel-edit-button"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="confirm-button"
                    >
                        Save Changes
                    </button>

                </div>


            </form>

        </div>

    </div>

</body>

</html>


<?php

$conn->close();

?>
