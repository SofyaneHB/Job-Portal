<?php
session_start();

require_once "../config/db.php";
require_once "../includes/functions.php";

require_login();

if ($_SESSION['user_role'] !== 'company') {
    set_flash("error", "Access denied");
    redirect("../Public/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'];

// --- Fetch Company Info for Sidebar ---
$stmt = $pdo->prepare("SELECT c.company_name, u.email FROM companies c JOIN users u ON c.user_id = u.id WHERE c.id = ? LIMIT 1");
$stmt->execute([$company_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

// --- Fetch All Jobs for this Company ---
$stmt = $pdo->prepare("
    SELECT j.*, COUNT(a.id) as applicant_count 
    FROM jobs j
    LEFT JOIN applications a ON j.id = a.job_id
    WHERE j.company_id = ?
    GROUP BY j.id
    ORDER BY j.created_at DESC
");
$stmt->execute([$company_id]);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Jobs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-50">

<aside class="w-64 bg-white border-r h-screen fixed">
    <div class="p-4 flex items-center gap-3 border-b">
        <div class="w-10 h-10 bg-indigo-600 text-white flex items-center justify-center rounded-lg">J</div>
        <div>
            <div class="font-bold">JobPortal</div>
            <div class="text-xs text-gray-400">Company</div>
        </div>
    </div>
    <div class="p-4 space-y-2 text-sm">
        <a href="dashboard.php" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Dashboard</a>
        <a href="profile.php" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Profile</a>
        <a href="add_job.php" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Add Job</a>
        <a href="my_jobs.php" class="block p-2 bg-indigo-600 text-white rounded-lg">My Jobs</a>
        <a href="applicants.php" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Applicants</a>
    </div>
    
    <div class="absolute bottom-0 w-full p-4 border-t bg-gray-50">
        <div class="text-sm font-bold"><?= htmlspecialchars($company['company_name'] ?? 'Company') ?></div>
        <div class="text-xs text-gray-500"><?= htmlspecialchars($company['email'] ?? '') ?></div>
    </div>
</aside>

<main class="ml-64 flex-1 p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold mb-1">My Jobs</h1>
            <p class="text-gray-500">Manage your job postings</p>
        </div>
        <a href="add_job.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
            + Post New Job
        </a>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <?php if(!$jobs): ?>
            <div class="p-8 text-center text-gray-500">You haven't posted any jobs yet.</div>
        <?php else: ?>
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 border-b">
                    <tr>
                        <th class="px-6 py-4 font-medium">Job Title</th>
                        <th class="px-6 py-4 font-medium">Type</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Applicants</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach($jobs as $job): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800"><?= htmlspecialchars($job['title']) ?></div>
                            <div class="text-xs text-gray-400"><?= htmlspecialchars($job['location']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-gray-600"><?= ucfirst($job['type']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-md text-xs font-medium 
                                <?= $job['status'] === 'active' ? 'bg-green-100 text-green-700' : ($job['status'] === 'paused' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') ?>">
                                <?= ucfirst($job['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600"><?= $job['applicant_count'] ?></td>
                        <td class="px-6 py-4 text-right">
                            <a href="edit_job.php?id=<?= $job['id'] ?>" class="text-indigo-600 hover:text-indigo-900 font-medium mr-3">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>
</body>
</html>