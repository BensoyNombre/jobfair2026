<?php

require_once __DIR__ . "/auth.php";
require_admin();
require_once "../database.php";


$search = trim(isset($_GET['search']) ? $_GET['search'] : '');


if ($search !== '') {

    $sql = "SELECT * FROM applicants
            WHERE last_name LIKE ?
               OR first_name LIKE ?
               OR middle_name LIKE ?
               OR phone_number LIKE ?
               OR email LIKE ?
            ORDER BY created_at DESC, id DESC";

    $stmt = $conn->prepare($sql);

    $searchTerm = "%" . $search . "%";

    $stmt->bind_param(
        "sssss",
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm
    );

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $sql = "SELECT * FROM applicants
            ORDER BY created_at DESC, id DESC";

    $result = $conn->query($sql);

}


$countQuery = "SELECT COUNT(*) AS total FROM applicants";

$countResult = $conn->query($countQuery);

$totalApplicants = $countResult->fetch_assoc()['total'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

</head>


<body class="sidebar-hidden batch-selection-hidden">


    <aside class="sidebar" id="applicantSidebar">

        <h2>
            Applicant Services
        </h2>

        <nav>

            <a
                href="dashboard.php"
                class="active"
            >
                Dashboard
            </a>

            <a href="statistics.php">
                Statistics
            </a>

            <a
                href="export.php"
                class="sidebar-export-button"
            >
                Export Excel
            </a>

            <button
                type="button"
                class="sidebar-batch-delete-button"
                id="batchDeleteButton"
                aria-pressed="false"
            >
                Batch Delete
            </button>

        </nav>

        <a
            href="logout.php"
            class="sidebar-logout-button"
        >
            Logout
        </a>

    </aside>



    <main class="main-content">


        <header class="topbar">

            <div class="dashboard-brand">

                <img
                    class="cpsu-mark"
                    src="../img/Central_Philippines_State_University_Official_Logo.png"
                    alt="Central Philippines State University logo"
                >

                <div>

                    <p class="dashboard-brand-name">
                        Central Philippines State University
                    </p>

                    <h1>
                        JOB FAIR 2026
                    </h1>

                </div>

            </div>

            <button
                type="button"
                class="sidebar-toggle"
                id="sidebarToggle"
                aria-controls="applicantSidebar"
                aria-expanded="false"
                aria-label="Show sidebar"
                title="Show sidebar"
            >
                &#9776;
            </button>

        </header>

        <div class="partner-ribbon dashboard-ribbon" aria-label="Partner organizations">
            <span class="partner-mark"><img src="../img/safe_center.png" alt="SAFE Center of CPSU logo"></span>
            <span class="partner-mark"><img src="../img/peso_ph.png" alt="Public Employment Service Office logo"></span>
            <span class="partner-mark"><img src="../img/kabankalan.png" alt="Kabankalan City logo"></span>
            <span class="partner-mark"><img src="../img/city_mall.png" alt="CityMall logo"></span>
        </div>



        <section class="dashboard-card">

            <div>

                <p class="card-title">
                    Total Applicants
                </p>

                <h2>
                    <?php echo $totalApplicants; ?>
                </h2>

            </div>

        </section>



        <section class="applicants-section">

            <div class="section-header">

                <h2>
                    Applicants
                </h2>

                <button
                    type="button"
                    class="confirm-batch-delete-button"
                    id="confirmBatchDeleteButton"
                    hidden
                >
                    Confirm Delete (<span id="selectedApplicantCount">0</span>)
                </button>


                <form
                    method="GET"
                    class="search-form"
                >

                    <input
                        type="text"
                        name="search"
                        placeholder="Search applicant..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >

                    <button type="submit">
                        Search
                    </button>


                    <?php if ($search !== ''): ?>

                        <a
                            href="dashboard.php"
                            class="clear-button"
                        >
                            Clear
                        </a>

                    <?php endif; ?>

                </form>

            </div>



            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th class="selection-column">
                                <input
                                    type="checkbox"
                                    id="selectAllApplicants"
                                    aria-label="Select all applicants"
                                >
                            </th>

                            <th>#</th>

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

                            <th>Actions</th>

                        </tr>

                    </thead>


                    <tbody data-search-active="<?php echo $search !== '' ? 'true' : 'false'; ?>">

                        <?php if ($result->num_rows > 0): ?>

                            <?php
                            $applicantsByDate = [];

                            while ($row = $result->fetch_assoc()) {
                                $registrationDate = date(
                                    'Y-m-d',
                                    strtotime($row['created_at'])
                                );
                                $applicantsByDate[$registrationDate][] = $row;
                            }

                            $number = 1;
                            ?>

                            <?php foreach ($applicantsByDate as $registrationDate => $applicants): ?>

                                <?php $groupId = 'date-group-' . str_replace('-', '', $registrationDate); ?>

                                <tr class="date-divider-row">

                                    <td colspan="13">
                                        <button
                                            type="button"
                                            class="date-divider-button"
                                            data-date-toggle="<?php echo $groupId; ?>"
                                            aria-expanded="false"
                                        >
                                            <span>
                                                Registered <?php echo date('F j, Y', strtotime($registrationDate)); ?>
                                            </span>
                                            <span class="date-divider-icon" aria-hidden="true">+</span>
                                        </button>
                                    </td>

                                </tr>

                                <?php foreach ($applicants as $row): ?>

                                    <tr
                                        class="date-group-row"
                                        data-date-group="<?php echo $groupId; ?>"
                                        hidden
                                    >

                                    <td class="selection-column">
                                        <input
                                            type="checkbox"
                                            name="ids[]"
                                            value="<?php echo $row['id']; ?>"
                                            form="batchDeleteForm"
                                            class="applicant-checkbox"
                                            aria-label="Select <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"
                                        >
                                    </td>

                                    <td>
                                        <?php echo $number++; ?>
                                    </td>


                                    <td>
                                        <strong>
                                            <?php
                                            echo htmlspecialchars(
                                                $row['last_name']
                                            );
                                            ?>
                                        </strong>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['first_name']
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo !empty($row['middle_name'])
                                            ? htmlspecialchars($row['middle_name'])
                                            : "—";
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo !empty($row['extension'])
                                            ? htmlspecialchars($row['extension'])
                                            : "—";
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['sex']
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['age']
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php echo htmlspecialchars($row['barangay']); ?>
                                    </td>


                                    <td>
                                        <?php echo htmlspecialchars($row['city_municipality']); ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['phone_number']
                                        );
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $row['email']
                                        );
                                        ?>
                                    </td>


                                    <td class="actions">

                                        <a
                                            href="edit.php?id=<?php echo $row['id']; ?>"
                                            class="edit-button"
                                        >
                                            Edit
                                        </a>


                                        <a
                                            href="delete.php?id=<?php echo $row['id']; ?>"
                                            class="delete-button delete-trigger"
                                            data-delete-url="delete.php?id=<?php echo $row['id']; ?>"
                                        >
                                            Delete
                                        </a>

                                    </td>

                                    </tr>

                                <?php endforeach; ?>

                                <?php if (count($applicants) > 20): ?>

                                    <tr
                                        class="date-pagination-row"
                                        data-pagination-for="<?php echo $groupId; ?>"
                                        hidden
                                    >

                                        <td colspan="13">
                                            <nav
                                                class="date-pagination"
                                                aria-label="Pages for applicants registered <?php echo date('F j, Y', strtotime($registrationDate)); ?>"
                                            ></nav>
                                        </td>

                                    </tr>

                                <?php endif; ?>

                            <?php endforeach; ?>


                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="13"
                                    class="no-results"
                                >
                                    <?php if ($search !== ''): ?>

                                        No applicants found for
                                        "<?php echo htmlspecialchars($search); ?>".

                                    <?php else: ?>

                                        No applicants registered yet.

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

    <form
        id="batchDeleteForm"
        method="POST"
        action="batch_delete.php"
    ></form>

    <div
        id="deleteConfirmationModal"
        class="modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="deleteModalTitle"
    >

        <div class="modal-content delete-modal-content">

            <span class="modal-alert" aria-hidden="true">!</span>

            <h2 id="deleteModalTitle">Delete applicant?</h2>

            <p id="deleteModalMessage">This action permanently removes this applicant from the records.</p>

            <div class="modal-buttons">

                <button
                    type="button"
                    class="cancel-button"
                    id="cancelDeleteButton"
                >
                    Cancel
                </button>

                <a
                    href="#"
                    class="final-confirm-button delete-confirm-link"
                    id="confirmDeleteButton"
                >
                    Delete applicant
                </a>

            </div>

        </div>

    </div>


    <script src="../js/script.js?v=4"></script>

</body>

</html>


<?php

$conn->close();

?>
