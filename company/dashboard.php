<?php
session_start();

require_once "../config/db.php";
require_once "../includes/functions.php";

require_login();

$company_id = $_SESSION['company_id'] ?? null;

if (!$company_id) {
    redirect("../auth/login.php");
}

/* =========================
   COMPANY INFO
========================= */
$stmt = $pdo->prepare("
    SELECT name, logo, plan
    FROM companies
    WHERE id = ?
");
$stmt->execute([$company_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    $company = [
        'name' => 'My Company',
        'logo' => 'https://via.placeholder.com/100',
        'plan' => 'Free'
    ];
}

/* =========================
   STATS (REAL DB)
========================= */

/* Active jobs */
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM jobs 
    WHERE company_id = ? AND status = 'active'
");
$stmt->execute([$company_id]);
$active_jobs = $stmt->fetchColumn();

/* Total applicants */
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE j.company_id = ?
");
$stmt->execute([$company_id]);
$total_applicants = $stmt->fetchColumn();

/* New today */
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE j.company_id = ?
    AND DATE(a.applied_at) = CURDATE()
");
$stmt->execute([$company_id]);
$new_today = $stmt->fetchColumn();

/* Shortlisted */
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE j.company_id = ?
    AND a.status = 'accepted'
");
$stmt->execute([$company_id]);
$shortlisted = $stmt->fetchColumn();


/* =========================
   RECENT JOBS
========================= */
$stmt = $pdo->prepare("
    SELECT 
        j.id,
        j.title,
        j.type,
        j.status,
        j.created_at,
        COUNT(a.id) AS applicants
    FROM jobs j
    LEFT JOIN applications a ON j.id = a.job_id
    WHERE j.company_id = ?
    GROUP BY j.id
    ORDER BY j.created_at DESC
    LIMIT 5
");
$stmt->execute([$company_id]);
$recent_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   RECENT APPLICANTS
========================= */
$stmt = $pdo->prepare("
    SELECT 
        u.full_name,
        j.title AS job_title,
        a.status,
        a.applied_at
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN users u ON a.candidate_id = u.id
    WHERE j.company_id = ?
    ORDER BY a.applied_at DESC
    LIMIT 5
");
$stmt->execute([$company_id]);
$recent_applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Entreprise — JobPortal</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: { brand: { 600: '#4f46e5', 700: '#4338ca' } }
                }
            }
        }
    </script>
</head>

<body class="h-full font-sans flex">

<!-- SIDEBAR (UNCHANGED UI) -->
<aside class="w-64 bg-white border-r fixed inset-y-0">
    <div class="h-16 flex items-center px-6 border-b gap-3">
        <div class="w-9 h-9 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold">J</div>
        <span class="font-bold">Job<span class="text-brand-600">Portal</span></span>
    </div>

    <nav class="p-4 space-y-1 text-sm">
        <a href="dashboard.php" class="block p-2 bg-brand-600 text-white rounded-xl">Dashboard</a>
        <a href="my_jobs.php" class="block p-2 text-gray-600">My Jobs</a>
        <a href="applicants.php" class="block p-2 text-gray-600">Applicants</a>
        <a href="add_job.php" class="block p-2 text-gray-600">Post Job</a>
    </nav>

    <div class="absolute bottom-0 p-4 border-t w-full flex items-center gap-2">
        <img src="<?= $company['logo'] ?>" class="w-10 h-10 rounded-lg">
        <div>
            <p class="text-sm font-bold"><?= $company['name'] ?></p>
            <p class="text-xs text-gray-400"><?= $company['plan'] ?></p>
        </div>
    </div>
</aside>

<!-- MAIN -->
<div class="ml-64 flex-1 p-8">

    <!-- HEADER -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold">Bonjour, <?= $company['name'] ?> 👋</h2>
        <p class="text-gray-500">Overview of your recruitment activity</p>
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-4 gap-5 mb-8">

        <div class="bg-white p-5 rounded-2xl border">
            <p class="text-gray-400 text-sm">Active Jobs</p>
            <p class="text-2xl font-bold"><?= $active_jobs ?></p>
        </div>

        <div class="bg-white p-5 rounded-2xl border">
            <p class="text-gray-400 text-sm">Applicants</p>
            <p class="text-2xl font-bold"><?= $total_applicants ?></p>
        </div>

        <div class="bg-white p-5 rounded-2xl border">
            <p class="text-gray-400 text-sm">New Today</p>
            <p class="text-2xl font-bold"><?= $new_today ?></p>
        </div>

        <div class="bg-white p-5 rounded-2xl border">
            <p class="text-gray-400 text-sm">Shortlisted</p>
            <p class="text-2xl font-bold"><?= $shortlisted ?></p>
        </div>

    </div>

    <!-- RECENT JOBS -->
    <div class="bg-white rounded-2xl border p-5 mb-8">
        <h3 class="font-bold mb-4">Recent Jobs</h3>

        <?php if (empty($recent_jobs)): ?>
            <p class="text-gray-400">No jobs yet</p>
        <?php else: ?>
            <table class="w-full text-sm">
                <thead class="text-gray-400">
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Applicants</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($recent_jobs as $job): ?>
                        <tr class="border-t">
                            <td><?= htmlspecialchars($job['title']) ?></td>
                            <td><?= $job['type'] ?></td>
                            <td><?= $job['status'] ?></td>
                            <td><?= $job['applicants'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- RECENT APPLICANTS -->
    <div class="bg-white rounded-2xl border p-5">
        <h3 class="font-bold mb-4">Recent Applicants</h3>

        <?php if (empty($recent_applicants)): ?>
            <p class="text-gray-400">No applicants yet</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($recent_applicants as $a): ?>
                    <div class="flex justify-between border-b py-2">
                        <div>
                            <p class="font-semibold"><?= htmlspecialchars($a['full_name']) ?></p>
                            <p class="text-xs text-gray-400"><?= $a['job_title'] ?></p>
                        </div>
                        <span class="text-xs"><?= ucfirst($a['status']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>