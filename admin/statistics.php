<?php

require_once __DIR__ . "/auth.php";
require_admin();
require_once "../database.php";

$totalResult = $conn->query("SELECT COUNT(*) AS total FROM applicants");
$totalApplicants = $totalResult->fetch_assoc()['total'];

$registrationResult = $conn->query(
    "SELECT DATE(created_at) AS registration_date, COUNT(*) AS total
     FROM applicants
     GROUP BY DATE(created_at)
     ORDER BY registration_date ASC"
);

$registrationStats = [];

while ($stat = $registrationResult->fetch_assoc()) {
    $registrationStats[] = [
        'label' => date('M j', strtotime($stat['registration_date'])),
        'total' => $stat['total']
    ];
}


$ageResult = $conn->query(
    "SELECT CAST(age AS CHAR) AS label, COUNT(*) AS total
     FROM applicants
     GROUP BY age
     ORDER BY age ASC"
);
$ageStats = [];
while ($stat = $ageResult->fetch_assoc()) {
    $ageStats[] = $stat;
}

$locationResult = $conn->query(
    "SELECT COALESCE(NULLIF(city_municipality, ''), 'Not specified') AS label, COUNT(*) AS total
     FROM applicants
     GROUP BY city_municipality
     ORDER BY label ASC"
);
$locationStats = [];
while ($stat = $locationResult->fetch_assoc()) {
    $locationStats[] = $stat;
}

function renderLineChart($stats, $ariaLabel, $emptyMessage) {
    $chartWidth = 800;
    $chartHeight = 320;
    $chartLeft = 60;
    $chartRight = 20;
    $chartTop = 20;
    $chartBottom = 55;
    $plotWidth = $chartWidth - $chartLeft - $chartRight;
    $plotHeight = $chartHeight - $chartTop - $chartBottom;
    $maxCount = 0;
    $linePoints = [];

    foreach ($stats as $stat) {
        $maxCount = max($maxCount, (int) $stat['total']);
    }

    foreach ($stats as $index => $stat) {
        $x = count($stats) > 1
            ? $chartLeft + ($index * $plotWidth / (count($stats) - 1))
            : $chartLeft + ($plotWidth / 2);
        $y = $chartTop + $plotHeight - (
            (int) $stat['total'] / $maxCount * $plotHeight
        );
        $linePoints[] = [
            'x' => round($x, 2),
            'y' => round($y, 2),
            'label' => $stat['label'],
            'total' => $stat['total']
        ];
    }

    if (count($linePoints) === 0) {
        echo '<p class="statistics-empty">' . htmlspecialchars($emptyMessage) . '</p>';
        return;
    }

    $pointString = implode(' ', array_map(function ($point) {
        return $point['x'] . ',' . $point['y'];
    }, $linePoints));
    ?>
    <svg
        class="line-chart"
        viewBox="0 0 <?php echo $chartWidth; ?> <?php echo $chartHeight; ?>"
        role="img"
        aria-label="<?php echo htmlspecialchars($ariaLabel); ?>"
    >
        <line class="line-chart-axis" x1="<?php echo $chartLeft; ?>" y1="<?php echo $chartTop; ?>" x2="<?php echo $chartLeft; ?>" y2="<?php echo $chartHeight - $chartBottom; ?>"></line>
        <line class="line-chart-axis" x1="<?php echo $chartLeft; ?>" y1="<?php echo $chartHeight - $chartBottom; ?>" x2="<?php echo $chartWidth - $chartRight; ?>" y2="<?php echo $chartHeight - $chartBottom; ?>"></line>
        <polyline class="line-chart-line" points="<?php echo htmlspecialchars($pointString); ?>"></polyline>

        <?php foreach ($linePoints as $point): ?>
            <circle
                class="line-chart-point"
                cx="<?php echo $point['x']; ?>"
                cy="<?php echo $point['y']; ?>"
                r="5"
            >
                <title><?php echo htmlspecialchars($point['label']); ?>: <?php echo $point['total']; ?> applicants</title>
            </circle>
            <text class="line-chart-value" x="<?php echo $point['x']; ?>" y="<?php echo $point['y'] - 12; ?>"><?php echo $point['total']; ?></text>
            <text class="line-chart-label" x="<?php echo $point['x']; ?>" y="<?php echo $chartHeight - 25; ?>"><?php echo htmlspecialchars($point['label']); ?></text>
        <?php endforeach; ?>
    </svg>
    <?php
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Statistics</title>
    <link rel="stylesheet" href="../css/style.css?v=2">
</head>
<body class="sidebar-hidden">
    <aside class="sidebar" id="applicantSidebar">
        <h2>Applicant Services</h2>

        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="statistics.php" class="active">Statistics</a>
            <a href="export.php" class="sidebar-export-button">Export Excel</a>
        </nav>

        <a href="logout.php" class="sidebar-logout-button">Logout</a>
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
                    <p class="dashboard-brand-name">Central Philippines State University</p>
                    <h1>JOB FAIR 2026</h1>
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
            <p class="card-title">Total Applicants</p>
            <h2><?php echo $totalApplicants; ?></h2>
        </section>

        <section class="statistics-grid" aria-label="Applicant statistics">
            <div class="statistics-panel line-chart-panel">
                <h2>Applicants Registered Over Time</h2>
                <?php renderLineChart($registrationStats, 'Line graph showing applicants registered by date', 'No registration data yet'); ?>
            </div>

            <div class="statistics-panel line-chart-panel">
                <h2>Applicants by Age</h2>
                <?php renderLineChart($ageStats, 'Line graph showing applicants by age', 'No age data yet'); ?>
            </div>

            <div class="statistics-panel line-chart-panel">
                <h2>Applicants by City/Municipality</h2>
                <?php renderLineChart($locationStats, 'Line graph showing applicants by city or municipality', 'No location data yet'); ?>
            </div>
        </section>
    </main>

    <script src="../js/script.js?v=3"></script>
</body>
</html>

<?php
$conn->close();
?>
