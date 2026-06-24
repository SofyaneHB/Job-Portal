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

// --- Handle Form Submission ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = clean_input($_POST['title'] ?? '');
    $location = clean_input($_POST['location'] ?? '');
    $type = clean_input($_POST['type'] ?? 'full-time');
    $salary = clean_input($_POST['salary'] ?? '');
    $description = clean_input($_POST['description'] ?? '');

    if (empty($title) || empty($description) || empty($location)) {
        set_flash("error", "Please fill in all required fields.");
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO jobs (company_id, title, description, location, type, salary, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'active')
        ");
        $stmt->execute([$company_id, $title, $description, $location, $type, $salary]);
        
        set_flash("success", "Job posted successfully!");
        redirect("my_jobs.php");
        exit;
    }
}

// --- Fetch Company Info for Sidebar ---
$stmt = $pdo->prepare("SELECT c.company_name, u.email FROM companies c JOIN users u ON c.user_id = u.id WHERE c.id = ? LIMIT 1");
$stmt->execute([$company_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Post a Job</title>
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
        <a href="add_job.php" class="block p-2 bg-indigo-600 text-white rounded-lg">Add Jobs</a>
        <a href="my_jobs.php" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">My Jobs</a>
        <a href="applicants.php" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Applicants</a>
    </div>
    
    <!-- عرض اسم الشركة والبريد الإلكتروني -->
    <div class="absolute bottom-0 w-full p-4 border-t bg-gray-50">
        <div class="text-sm font-bold"><?= htmlspecialchars($company['company_name'] ?? 'Company') ?></div>
        <div class="text-xs text-gray-500"><?= htmlspecialchars($company['email'] ?? '') ?></div>
    </div>
</aside>

<main class="ml-64 flex-1 p-8">
    <h1 class="text-2xl font-bold mb-1">Post a New Job</h1>
    <p class="text-gray-500 mb-6">Fill out the details below to attract top talent.</p>

    <?php display_flash(); ?>

    <div class="max-w-3xl bg-white p-6 rounded-xl border">
        <form method="POST" action="">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Job Title *</label>
                    <input type="text" name="title" required class="w-full border-gray-300 border rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-600 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
                    <input type="text" name="location" placeholder="e.g. Remote, Taroudant..." required class="w-full border-gray-300 border rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-600 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employment Type</label>
                    <select name="type" class="w-full border-gray-300 border rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-600 outline-none bg-white">
                        <option value="full-time">Full-Time</option>
                        <option value="part-time">Part-Time</option>
                        <option value="remote">Remote</option>
                        <option value="internship">Internship</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salary Range</label>
                    <input type="text" name="salary" placeholder="e.g. 5000 - 8000 MAD" class="w-full border-gray-300 border rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-600 outline-none">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Job Description *</label>
                <textarea name="description" rows="5" required class="w-full border-gray-300 border rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-600 outline-none"></textarea>
            </div>

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors">
                Publish Job
            </button>
        </form>
    </div>
</main>
</body>
</html>