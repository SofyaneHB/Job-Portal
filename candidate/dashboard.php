<?php

session_start();

require_once "../config/db.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";
require_once "../includes/navbar.php";

require_login();

if ($_SESSION['user_role'] !== 'candidate') {
    redirect("../public/login.php");
}

$user_id = $_SESSION['user_id'];

/* =========================
   USER INFO (FULL)
========================= */
$stmt = $pdo->prepare("
    SELECT full_name, email, phone, address, country, skills
    FROM users
    WHERE id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$user_name = $user['full_name'] ?? 'User';

$skills = !empty($user['skills']) ? explode(',', $user['skills']) : [];

/* =========================
   APPLICATIONS
========================= */
try {

    $stmt = $pdo->prepare("
        SELECT 
            a.status,
            a.applied_at,
            a.job_id,
            j.title,
            j.location,
            j.type,
            j.salary,
            COALESCE(c.company_name, 'Unknown') AS company_name,
            c.logo
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        LEFT JOIN companies c ON j.company_id = c.id
        WHERE a.candidate_id = ?
        ORDER BY a.applied_at DESC
    ");

    $stmt->execute([$user_id]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $applications = [];
}

$applied_count = count($applications);

?>

<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <?php display_flash(); ?>

        <!-- HEADER -->
        <div class="mb-8 mt-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Welcome BACK, <?php echo htmlspecialchars($user_name); ?>
            </h1>
            <p class="text-gray-500 mt-1">
                Here is your daily activities and job alerts
            </p>
        </div>

        <!-- USER INFO SECTION -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-8">

            <h2 class="text-lg font-bold text-gray-900 mb-4">User Info</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                <div>
                    <p class="text-gray-500">Email</p>
                    <p class="font-semibold text-gray-900"><?php echo $user['email'] ?? 'Not set'; ?></p>
                </div>

                <div>
                    <p class="text-gray-500">Phone</p>
                    <p class="font-semibold text-gray-900"><?php echo $user['phone'] ?? 'Not set'; ?></p>
                </div>

                <div>
                    <p class="text-gray-500">Address</p>
                    <p class="font-semibold text-gray-900"><?php echo $user['address'] ?? 'Not set'; ?></p>
                </div>

                <div>
                    <p class="text-gray-500">Country</p>
                    <p class="font-semibold text-gray-900"><?php echo $user['country'] ?? 'Not set'; ?></p>
                </div>

            </div>

            <!-- Skills -->
            <div class="mt-5">
                <p class="text-gray-500 text-sm mb-2">Skills</p>

                <div class="flex flex-wrap gap-2">
                    <?php if (count($skills) > 0): ?>
                        <?php foreach ($skills as $skill): ?>
                            <span class="px-3 py-1 bg-gray-100 rounded-full text-xs font-bold text-gray-700">
                                <?php echo trim($skill); ?>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-gray-400 text-sm">No skills added</span>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- APPLIED JOBS CARD -->
        <div class="mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4 w-full md:w-64">
                <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-black text-gray-900">
                        <?php echo $applied_count; ?>
                    </p>
                    <p class="text-xs font-bold text-gray-400 uppercase">Applied Jobs</p>
                </div>
            </div>
        </div>

        <!-- BANNER -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
            <div>
                <h4 class="font-bold text-gray-900 text-lg">Your profile editing is not completed.</h4>
                <p class="text-sm text-gray-500 mt-1">
                    Complete your profile editing & build your custom resume.
                </p>
            </div>
            <a href="profile.php"
               class="bg-white border border-gray-200 hover:bg-gray-50 text-blue-600 px-6 py-2.5 font-bold rounded-xl text-sm transition shadow-sm">
                Edit Profile
            </a>
        </div>

        <!-- RECENT JOBS -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900">Recently Applied</h2>
            <a href="applications.php" class="text-sm font-bold text-gray-500 hover:text-blue-600">
                View All →
            </a>
        </div>

        <!-- CONTENT -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            <?php if (count($applications) === 0): ?>

                <div class="text-center text-gray-500 py-10">
                    No Applications Record
                </div>

            <?php else: ?>

                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-500 border-b">
                            <th class="py-2">Job</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr class="border-b">
                                <td class="py-3 font-semibold">
                                    <?php echo htmlspecialchars($app['title']); ?>
                                </td>

                                <td>
                                    <?php echo date('M d, Y', strtotime($app['applied_at'])); ?>
                                </td>

                                <td>
                                    <span class="text-xs font-bold
                                        <?php
                                            echo $app['status'] === 'accepted' ? 'text-green-600' :
                                                 ($app['status'] === 'rejected' ? 'text-red-600' : 'text-blue-600');
                                        ?>">
                                        <?php echo ucfirst($app['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php endif; ?>

        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>