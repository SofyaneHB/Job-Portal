<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

require_login();

/* التحقق من الصلاحيات */
if ($_SESSION['user_role'] !== 'admin') {
    set_flash("error", "Access denied: Admins only");
    redirect("../Public/login.php");
    exit;
}

/* جلب معلومات الأدمن للـ Sidebar */
$stmt = $pdo->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

/* جلب إحصائيات عامة (مثال للـ Dashboard) */
$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'companies' => $pdo->query("SELECT COUNT(*) FROM companies")->fetchColumn(),
    'jobs' => $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn()
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-50">

<aside class="w-64 bg-slate-900 h-screen fixed text-white">
    <div class="p-6 border-b border-slate-800 text-center font-bold text-xl">Admin Panel</div>
    <nav class="p-4 space-y-2">
        <a href="dashboard.php" class="block p-3 bg-indigo-600 rounded-lg">Dashboard</a>
        <a href="users.php" class="block p-3 hover:bg-slate-800 rounded-lg">Users</a>
        <a href="companies.php" class="block p-3 hover:bg-slate-800 rounded-lg">Companies</a>
        <a href="jobs.php" class="block p-3 hover:bg-slate-800 rounded-lg">Jobs</a>
    </nav>
    <div class="absolute bottom-0 w-full p-4 border-t border-slate-800 bg-slate-950">
        <div class="text-sm font-bold"><?= htmlspecialchars($admin['full_name'] ?? 'Admin') ?></div>
        <div class="text-xs text-gray-400"><?= htmlspecialchars($admin['email'] ?? '') ?></div>
    </div>
</aside>

<main class="ml-64 flex-1 p-8">
    <h1 class="text-2xl font-bold mb-8">Admin Dashboard</h1>
    
    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl border shadow-sm">
            <h3 class="text-gray-500">Total Users</h3>
            <p class="text-3xl font-bold"><?= $stats['users'] ?></p>
        </div>
        <div class="bg-white p-6 rounded-xl border shadow-sm">
            <h3 class="text-gray-500">Total Companies</h3>
            <p class="text-3xl font-bold"><?= $stats['companies'] ?></p>
        </div>
        <div class="bg-white p-6 rounded-xl border shadow-sm">
            <h3 class="text-gray-500">Total Jobs</h3>
            <p class="text-3xl font-bold"><?= $stats['jobs'] ?></p>
        </div>
    </div>
</main>

</body>
</html>