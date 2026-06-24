<?php
session_start();

require_once "../config/db.php";
require_once "../includes/functions.php";

require_login();

/* =========================
   ROLE & SESSION CHECK
========================= */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'company') {
    set_flash("error", "Access denied");
    redirect("../Public/login.php");
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$company_id = $_SESSION['company_id'] ?? null;

if (!$company_id || !$user_id) {
    set_flash("error", "Session missing");
    redirect("../Public/login.php");
    exit;
}

/* =========================
   FETCH COMPANY INFO FOR SIDEBAR
========================= */
$stmt = $pdo->prepare("SELECT c.company_name, u.email FROM companies c JOIN users u ON c.user_id = u.id WHERE c.id = ? LIMIT 1");
$stmt->execute([$company_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

/* =========================
   HANDLE STATUS UPDATE
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['app_id'], $_POST['status'])) {
    $app_id = (int) $_POST['app_id'];
    $status = clean_input($_POST['status']);
    
    $allowed_status = ['pending', 'accepted', 'rejected'];
    if (in_array($status, $allowed_status)) {
        $stmt = $pdo->prepare("
            UPDATE applications a
            INNER JOIN jobs j ON a.job_id = j.id
            SET a.status = ?
            WHERE a.id = ? AND j.company_id = ?
        ");
        $stmt->execute([$status, $app_id, $company_id]);
    }
    redirect("applicants.php");
    exit;
}

/* =========================
   FETCH DATA
========================= */
$stmt = $pdo->prepare("
    SELECT 
        a.id AS app_id, a.status, a.cv_path,
        u.full_name, u.email
    FROM applications a
    INNER JOIN jobs j ON a.job_id = j.id
    INNER JOIN users u ON a.candidate_id = u.id
    WHERE j.company_id = ?
    ORDER BY a.applied_at DESC
");
$stmt->execute([$company_id]);
$applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Applicants</title>
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
        <a href="my_jobs.php" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">My Jobs</a>
        <a href="applicants.php" class="block p-2 bg-indigo-600 text-white rounded-lg">Applicants</a>
    </div>
    <div class="absolute bottom-0 w-full p-4 border-t bg-gray-50">
        <div class="text-sm font-bold"><?= htmlspecialchars($company['company_name'] ?? 'Company') ?></div>
        <div class="text-xs text-gray-500"><?= htmlspecialchars($company['email'] ?? '') ?></div>
    </div>
</aside>

<main class="ml-64 flex-1 p-8">
    <h1 class="text-2xl font-bold mb-1">Manage Applicants</h1>
    <p class="text-gray-500 mb-6">Review candidates, download CVs, and update status.</p>

    <div class="bg-white rounded-xl border overflow-hidden shadow-sm">
        <table class="w-full text-sm text-left border-collapse">
            <thead class="bg-gray-50 text-gray-600 border-b">
                <tr>
                    <th class="px-6 py-4 font-medium border-r border-gray-200 text-center">Candidate</th>
                    <th class="px-6 py-4 font-medium border-r border-gray-200 text-center">CV Preview</th>
                    <th class="px-6 py-4 font-medium text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if(empty($applicants)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-gray-400">No applicants found for your jobs yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($applicants as $app): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 border-r border-gray-100">
                            <div class="font-bold text-gray-800"><?= htmlspecialchars($app['full_name']) ?></div>
                            <div class="text-xs text-gray-400"><?= htmlspecialchars($app['email']) ?></div>
                        </td>
                        
                        <td class="px-6 py-4 border-r border-gray-100">
                            <?php if (!empty($app['cv_path'])): ?>
                                <a href="../uploads/<?= htmlspecialchars($app['cv_path']) ?>" target="_blank" 
                                   class="text-indigo-600 font-bold hover:underline">Download CV</a>
                            <?php else: ?>
                                <span class="text-gray-300 text-xs italic">No file</span>
                            <?php endif; ?>
                        </td>

                        <td class="px-6 py-4">
                            <form method="POST">
                                <input type="hidden" name="app_id" value="<?= $app['app_id'] ?>">
                                <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-lg p-2 text-xs font-bold cursor-pointer outline-none w-full">
                                    <option value="pending" <?= $app['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="accepted" <?= $app['status'] === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                                    <option value="rejected" <?= $app['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>