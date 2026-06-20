<nav class="bg-white shadow-md">

    <div class="container mx-auto px-4 py-3 flex justify-between items-center">

        <!-- Logo -->
        <a href="/job-portal/public/index.php" class="text-blue-600 font-bold text-xl">
            JobPortal
        </a>

        <!-- Links -->
        <div class="flex gap-6">

            <a href="/job-portal/public/index.php" class="hover:text-blue-600">
                Home
            </a>

            <a href="/job-portal/public/jobs.php" class="hover:text-blue-600">
                Jobs
            </a>

            <?php if (is_logged_in()) { ?>

                <!-- user logged in -->
                <a href="/job-portal/public/dashboard.php" class="hover:text-blue-600">
                    Dashboard
                </a>

                <a href="/job-portal/auth/logout.php" class="text-red-500">
                    Logout
                </a>

            <?php } else { ?>

                <!-- guest -->
                <a href="/job-portal/public/login.php" class="hover:text-blue-600">
                    Login
                </a>

                <a href="/job-portal/public/register.php"
                   class="bg-blue-600 text-white px-3 py-1 rounded">
                    Register
                </a>

            <?php } ?>

        </div>

    </div>

</nav>