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
            ORDER BY last_name ASC, first_name ASC";

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
            ORDER BY last_name ASC, first_name ASC";

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


<body class="sidebar-hidden">


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

            <a
                href="export.php"
                class="sidebar-export-button"
            >
                Export Excel
            </a>

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
            <span class="partner-mark"><img src="../img/safe_center.jpg" alt="SAFE Center of CPSU logo"></span>
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


                    <tbody>

                        <?php if ($result->num_rows > 0): ?>

                            <?php $number = 1; ?>


                            <?php while ($row = $result->fetch_assoc()): ?>

                                <tr>

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

                            <?php endwhile; ?>


                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="11"
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

            <p>This action permanently removes this applicant from the records.</p>

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


    <script src="../js/script.js"></script>

</body>

</html>


<?php

$conn->close();

?>
