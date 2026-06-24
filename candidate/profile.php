<?php
session_start();

require_once "../includes/functions.php";
require_once "../includes/header.php";
require_once "../includes/navbar.php";

require_once "../config/db.php";

$user_id = $_SESSION['user_id'];


// جلب بيانات المستخدم الحالية من قاعدة البيانات لتعبئة الحقول
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    // إضافة مفتاح 'full_name' هنا لتفادي أي خطأ في حال لم يجد ملف المستخدم
    $profile = ['full_name' => '', 'phone' => '', 'address' => '', 'skills' => '', 'country' => ''];
}

$countries = ["Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia", "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"];

?>


<div class="flex flex-col bg-gray-50">
    
    <div class=" flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl w-full bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            
            <div class="p-6 md:p-8 border-b border-gray-100 bg-white">
                <!-- تعديل العنوان ليصبح ديناميكياً -->
                <h1 class="text-3xl font-bold text-gray-950 tracking-tight">
                    <?php echo htmlspecialchars($profile['full_name'] ?? 'User Profile'); ?>
                </h1>
                <p class="text-xs text-gray-500 mt-1">Manage your account identity, contact details and professional skill sets</p>
            </div>

            <form action="update_profile.php" method="POST" class="p-6 md:p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Full Name</label>
                        <!-- تعديل الـ value ليجلب الاسم الحقيقي من قاعدة البيانات -->
                        <input type="text" name="full_name" 
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-150" 
                               value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" placeholder="Enter full name">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Country</label>
                        <select name="country" 
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-150 cursor-pointer">
                            <?php foreach ($countries as $country): ?>
                                <option value="<?php echo $country; ?>" <?php echo ($profile['country'] == $country || ($country == 'Morocco' && empty($profile['country']))) ? 'selected' : ''; ?>>
                                    <?php echo $country; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="md:col-span-2 space-y-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Core Professional Skills</label>
                        
                        <div id="skills-container" class="space-y-3">
                            <?php 
                            // تحليل المهارات المخزنة مسبقاً إذا كانت مفصولة بفاصلة، أو عرض حقل فارغ كبداية
                            $current_skills = !empty($profile['skills']) ? explode(',', $profile['skills']) : [''];
                            foreach ($current_skills as $index => $skill): 
                                $skill = trim($skill);
                            ?>
                            <div class="flex items-center gap-2">
                                <input type="text" name="skills[]" 
                                       class="w-full md:w-1/2 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-150" 
                                       value="<?php echo htmlspecialchars($skill); ?>" placeholder="e.g. PHP, Tailwind CSS, MySQL">
                                <?php if ($index > 0): ?>
                                    <button type="button" class="remove-skill-btn text-gray-400 hover:text-red-500 p-2 transition duration-150">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Phone Number</label>
                        <input type="text" name="phone" 
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-150" 
                               value="<?php echo htmlspecialchars($profile['phone']); ?>" placeholder="e.g. +212 600-000000">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Contact Address</label>
                        <input type="text" name="address" 
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-150" 
                               value="<?php echo htmlspecialchars($profile['address']); ?>" placeholder="e.g. Street, City, Postcode">
                    </div>

                </div> 
                
                <div class="pt-5 flex justify-end">
                    <button type="submit" 
                            class="w-fit sm:w-auto px-6 py-3 bg-black text-white rounded-full cursor-pointer hover:bg-gray-800">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addSkillBtn = document.getElementById('add-skill-btn');
    const container = document.getElementById('skills-container');
    
    if (addSkillBtn && container) {
        addSkillBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // ربط حدث الحذف المباشر للحقل الجديد عند الضغط على الأيقونة الحمراء
            div.querySelector('.remove-skill-btn').addEventListener('click', function() {
                div.remove();
            });
        });
    }

    // ربط الحقول القديمة المسترجعة من قاعدة البيانات بحدث الحذف أيضاً
    document.querySelectorAll('.remove-skill-btn').forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.remove();
        });
    });
});
</script>