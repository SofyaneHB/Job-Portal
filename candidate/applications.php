<?php

session_start();

require_once "../config/db.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";
require_once "../includes/navbar.php";

require_login();

$user_id = $_SESSION['user_id'];

/* =========================
   GET USER APPLICATIONS
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

?>

<div class="bg-gray-50 min-h-screen py-8 ">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <?php display_flash(); ?>

        <div class="mb-8 p-4 py-4">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">My Applications</h1>
            <p class="text-gray-500 mt-1">Manage your job search progress and stay updated.</p>
        </div>

        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">

            <?php if (count($applications) === 0): ?>

                <div class="p-2 text-center">

                    <h3 class="text-xl font-bold text-gray-900">No applications yet</h3>
                    <p class="text-gray-500 mt-2 max-w-sm mx-auto">Your journey begins here. Explore open roles and submit your first application today!</p>
                </div>

            <?php else: ?>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="p-6">Job Details</th>
                                <th class="p-6">Company</th>
                                <th class="p-6">Location</th>
                                <th class="p-6">Applied Date</th>
                                <th class="p-6">Status</th>
                                <th class="p-6 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($applications as $app): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-6">
                                        <div class="font-bold text-gray-900"><?php echo htmlspecialchars($app['title']); ?></div>
                                        <div class="text-xs text-gray-400 mt-0.5"><?php echo htmlspecialchars($app['type']); ?></div>
                                    </td>
                                    <td class="p-6 font-medium text-gray-700"><?php echo htmlspecialchars($app['company_name']); ?></td>
                                    <td class="p-6 text-gray-500"><?php echo htmlspecialchars($app['location']); ?></td>
                                    <td class="p-6 text-gray-500 text-sm"><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                                    <td class="p-6">
                                        <?php
                                            // Define badge styles based on status
                                            $status = strtolower($app['status']);
                                            $styles = [
                                                'accepted' => 'bg-green-50 text-green-700 border-green-200',
                                                'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                                'pending'  => 'bg-blue-50 text-blue-700 border-blue-200'
                                            ];
                                            $currentStyle = $styles[$status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                        ?>
                                        <span class="px-3 py-1 rounded-full border text-xs font-bold <?php echo $currentStyle; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                    <td class="p-6 text-center">
                                        <a href="../jobs/job_details.php?id=<?php echo $app['job_id']; ?>"
                                           class="text-blue-600 hover:text-blue-800 text-sm font-semibold underline decoration-2 underline-offset-4">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>