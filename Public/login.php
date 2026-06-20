<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login page</title>
    <link href="../output.css" rel="stylesheet"> 

</head>
<body class="min-h-screen bg-cover flex items-center justify-center p-6 " >

    <fieldset class="flex items-center justify-center w-full max-w-6xl h-[85vh] bg-cover bg-center rounded-[40px] shadow-2xl relative overflow-hidden "
    style="background-image: url(../assets/img/Background_Login.php.png);">

            <div class="absolute top-8 left-8 flex items-center gap-2">
                <div class="w-3 h-3 bg-black rounded-full"></div>
                    <span class="font-bold text-lg tracking-tight">Job Portal</span>
                </div>

        <form action="" method="POST" class="w-full max-w-md p-10 border bg-white/30 backdrop-blur-xl  border-white/40 rounded-[32px] shadow-2xl">
            
        <div class="bg-white/40 p-3 rounded-2xl w-fit mx-auto mb-4 border border-white/50 shadow-inner">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
        </div>

            <h1 class="font-bold text-3xl text-center">Sign in with email</h1>
            <p class="text-gray-500 text-center my-5">Make a new doc to bring ypur words, data, and teams together. For Free</p>

            <input type="email" placeholder="Enter Email" class="bg-white/50 p-4 rounded-xl w-full mb-3 border border-white/30 placeholder-gray-600 outline-none mb-5 focus:bg-white focus:border-black"> 
            <input type="password" placeholder="Enter the password" class="bg-white/50 p-4 rounded-xl w-full mb-3 border border-white/30 placeholder-gray-600 outline-none focus:bg-white focus:border-black">

            <a href="#" class="flex justify-end mt-2 underline-0">Forget password?</a>

            <button class="bg-black block mx-auto hover:bg-black-700 text-white font-semibold px-4 py-3 rounded-full mt-8 cursor-pointer ">  
                <a href="#">
                    Get Started
                </a>
            </button>

        </form>
    </fieldset>
</body>
</html>