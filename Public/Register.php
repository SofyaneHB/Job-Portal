<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link rel="stylesheet" href="http://localhost:5174/src/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-gray-100 ">

    <fieldset class="w-full max-w-5xl h-[92vh] bg-white/40 backdrop-blur-xl rounded-[40px] shadow-2xl border border-white/50 flex flex-col items-center justify-center relative overflow-hidden"
              style="background-image: url(./img/Background_Login.php.png);" >
        
        <div class="absolute top-8 left-8 flex items-center gap-2">
            <div class="w-3 h-3 bg-black rounded-full"></div>
            <span class="font-bold text-lg tracking-tight">Job Portal</span>
        </div>

        <div class="w-full max-w-sm px-6 py-3 border bg-white/30 backdrop-blur-xl  border-white/40 rounded-[32px] shadow-2xl">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mt-3">Create an account</h2>
                <p class="text-gray-500 text-sm mt-2">Make a new doc to bring your words, data, and teams together. For free.</p>
            </div>

            <form class="space-y-4 ">
                <input type="text" name="fullname" placeholder="Full Name" class="w-full px-4 py-3 bg-white/60 border border-gray-200 rounded-lg outline-none focus:bg-white focus:border-black">
                <input type="email" name = "email" placeholder="Email Address" class="w-full px-4 py-3 bg-white/60 border border-gray-200 rounded-lg outline-none focus:bg-white focus:border-black">
                <input type="password" name ="password" placeholder="Password" class="w-full px-4 py-3 bg-white/60 border border-gray-200 rounded-lg outline-none focus:bg-white focus:border-black">
                <input type="password" name="confirm_password" placeholder="Confirm Password" class="w-full px-4 py-3 bg-white/60 border border-gray-200 rounded-lg outline-none focus:bg-white focus:border-black">
                
                <button class="w-full cursor-pointer bg-[#182134] text-white py-3 rounded-lg font-medium hover:bg-black transition mt-2">Sign Up</button>
            </form>

            <p class="text-center text-sm text-gray-600 mt-4">Already have an account? <a href="#" class="font-bold text-gray-900">Log in</a></p>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-300"></div></div>
                <div class="relative flex justify-center text-xs">
                    <span class="bg-transparent px-2 text-gray-400">Or sign up with...</span>
                </div>
            </div>

            <button class=" cursor-pointer  mb-3 w-full h-12  border border-gray-300 rounded-lg hover:bg-white transition">
                <i class="fab fa-google text-lg"></i>
            </button>
        </div>
    </fieldset>

</body>
</html>