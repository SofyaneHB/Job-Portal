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
    $company_name = clean_input($_POST['company_name'] ?? '');
    $description = clean_input($_POST['description'] ?? '');
    $logo = clean_input($_POST['logo'] ?? '');

    if (empty($company_name)) {
        set_flash("error", "Company name is required");
    } else {
        $stmt = $pdo->prepare("
            UPDATE companies 
            SET company_name = ?, description = ?, logo = ? 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$company_name, $description, $logo, $company_id, $user_id]);
        
        set_flash("success", "Profile updated successfully!");
        redirect("profile.php");
        exit;
    }
}

// --- FETCH FRESH DATA ---
$stmt = $pdo->prepare("
    SELECT c.*, u.email 
    FROM companies c
    JOIN users u ON c.user_id = u.id
    WHERE c.id = ?
");
$stmt->execute([$company_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Company Profile</title>
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
        <a href="profile.php" class="block p-2 bg-indigo-600 text-white rounded-lg">Profile</a>
        <a href="add_job.php" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Add Job</a>
        <a href="my_jobs.php" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">My Jobs</a>
        <a href="applicants.php" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Applicants</a>
    </div>
    
    <!-- التعديل هنا: استخدام اسم الشركة عوض full_name كما يظهر في image_bff3de.png -->
    <div class="absolute bottom-0 w-full p-4 border-t bg-gray-50">
        <div class="text-sm font-bold text-gray-800"><?= htmlspecialchars($company['company_name'] ?? 'Company') ?></div>
        <div class="text-xs text-gray-500"><?= htmlspecialchars($company['email'] ?? '') ?></div>
    </div>
</aside>

<main class="ml-64 flex-1 p-8">
    <h1 class="text-2xl font-bold mb-1">Company Profile</h1>
    <p class="text-gray-500 mb-6">Update your company details and branding</p>

    <?php display_flash(); ?>

    <div class="max-w-2xl bg-white p-6 rounded-xl border">
        <form method="POST">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                <input type="text" name="company_name" value="<?= htmlspecialchars($company['company_name'] ?? '') ?>" required class="w-full border-gray-300 border rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-600 outline-none">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1"> URL</label>
                <input type="text" name="logo" value="<?= htmlspecialchars($company['logo'] ?? '') ?>" placeholder="https://..." class="w-full border-gray-300 border rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-600 outline-none">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Company Description</label>
                <textarea name="description" rows="4" class="w-full border-gray-300 border rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-600 outline-none"><?= htmlspecialchars($company['description'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors">
                Save Changes
            </button>
        </form>
    </div>
</main>
</body>
</html>