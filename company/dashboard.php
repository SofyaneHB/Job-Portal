<?php
session_start();

require_once "../config/db.php";
require_once "../includes/functions.php";

require_login();

/* ROLE CHECK */
if ($_SESSION['user_role'] !== 'company') {
    set_flash("error", "Access denied");
    redirect("../Public/login.php");
    exit;
}

/* IDs */
$company_id = $_SESSION['company_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$company_id || !$user_id) {
    set_flash("error", "Company session missing");
    redirect("../Public/login.php");
    exit;
}

/* FETCH COMPANY DATA (Name, Logo, Description) */
$stmt = $pdo->prepare("SELECT company_name, logo, description FROM companies WHERE id = ? LIMIT 1");
$stmt->execute([$company_id]);
$company_data = $stmt->fetch(PDO::FETCH_ASSOC);

$display_name = $company_data['company_name'] ?? 'Company';
$display_logo = $company_data['logo'] ?? '';
$display_desc = $company_data['description'] ?? '';

/* FETCH USER EMAIL */
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$user_id]);
$user_email = $stmt->fetchColumn();

/* STATS */
$stmt = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE company_id = ?");
$stmt->execute([$company_id]);
$active_jobs = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.company_id = ?");
$stmt->execute([$company_id]);
$total_applicants = $stmt->fetchColumn();

/* RECENT JOBS */
$stmt = $pdo->prepare("SELECT j.*, COUNT(a.id) AS applicants FROM jobs j LEFT JOIN applications a ON j.id = a.job_id WHERE j.company_id = ? GROUP BY j.id ORDER BY j.created_at DESC LIMIT 5");
$stmt->execute([$company_id]);
$recent_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* RECENT APPLICANTS */
$stmt = $pdo->prepare("SELECT u.full_name, j.title, a.status FROM applications a JOIN jobs j ON a.job_id = j.id JOIN users u ON a.candidate_id = u.id WHERE j.company_id = ? ORDER BY a.applied_at DESC LIMIT 5");
$stmt->execute([$company_id]);
$recent_applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Company Dashboard</title>
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
        <a href="dashboard.php" class="block p-2 bg-indigo-600 text-white rounded-lg">Dashboard</a>
        <a href="profile.php" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Profile</a>
        <a href="add_job.php" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Add Jobs</a>
        <a href="my_jobs.php" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">My Jobs</a>
        <a href="applicants.php" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Applicants</a>
    </div>
    <div class="absolute bottom-0 w-full p-4 border-t bg-gray-50">
        <div class="text-sm font-bold"><?= htmlspecialchars($display_name) ?></div>
        <div class="text-xs text-gray-500"><?= htmlspecialchars($user_email) ?></div>
    </div>
</aside>

<main class="ml-64 flex-1 p-8">
    <!-- Welcome Header with Info -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Welcome <?= htmlspecialchars($display_name) ?></h1>
        
        <?php if (!empty($display_logo)): ?>
            <a href="<?= htmlspecialchars($display_logo) ?>" target="_blank" class="text-sm text-indigo-600 hover:underline truncate block">
                <?= htmlspecialchars($display_logo) ?>
            </a>
        <?php endif; ?>
        
        <?php if (!empty($display_desc)): ?>
            <p class="text-gray-600 mt-2 text-sm italic"><?= htmlspecialchars($display_desc) ?></p>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl border">
            <p class="text-gray-500">Active Jobs</p>
            <p class="text-2xl font-bold"><?= $active_jobs ?></p>
        </div>
        <div class="bg-white p-5 rounded-xl border">
            <p class="text-gray-500">Applicants</p>
            <p class="text-2xl font-bold"><?= $total_applicants ?></p>
        </div>
    </div>

    <!-- Recent Jobs -->
    <div class="bg-white p-5 rounded-xl border mb-6">
        <h2 class="font-bold mb-3">Recent Jobs</h2>
        <?php if (!$recent_jobs): ?>
            <p class="text-gray-400">No jobs yet</p>
        <?php else: ?>
            <?php foreach ($recent_jobs as $job): ?>
                <div class="flex justify-between border-b py-2">
                    <div>
                        <p class="font-semibold"><?= htmlspecialchars($job['title']) ?></p>
                        <p class="text-xs text-gray-400"><?= htmlspecialchars($job['type'] ?? 'N/A') ?></p>
                    </div>
                    <span class="text-sm text-gray-600"><?= (int)$job['applicants'] ?> applicants</span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Recent Applicants -->
    <div class="bg-white p-5 rounded-xl border">
        <h2 class="font-bold mb-3">Recent Applicants</h2>
        <?php if (!$recent_applicants): ?>
            <p class="text-gray-400">No applicants yet</p>
        <?php else: ?>
            <?php foreach ($recent_applicants as $a): ?>
                <div class="flex justify-between border-b py-2">
                    <div>
                        <p class="font-semibold"><?= htmlspecialchars($a['full_name']) ?></p>
                        <p class="text-xs text-gray-400"><?= htmlspecialchars($a['title']) ?></p>
                    </div>
                    <span class="text-xs font-medium text-indigo-600 px-2 py-1 bg-indigo-50 rounded-md"><?= htmlspecialchars(ucfirst($a['status'])) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

</body>
</html>