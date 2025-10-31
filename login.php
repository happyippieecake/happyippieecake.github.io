<?php
session_start();
$error = '';
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
        $_SESSION['admin_login'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Admin | HappyippieCake</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:600,700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat', Arial, sans-serif;}
    .brand-font { font-family: 'Pacifico', cursive;}
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-pink-100 via-white to-pink-200 flex items-center justify-center">
  <div class="w-full max-w-md mx-auto">
    <div class="bg-white/70 backdrop-blur rounded-3xl shadow-2xl border border-pink-300 px-8 py-10 flex flex-col items-center">
      <div class="mb-6 flex flex-col items-center">
        <span class="brand-font text-4xl text-pink-500 drop-shadow-xl tracking-wider mb-1">HappyippieCake</span>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Admin Login</h1>
      </div>
      <?php if($error): ?>
        <div class="mb-5 w-full bg-red-100 border-l-4 border-red-400 text-red-700 p-3 rounded shadow transition-all animate-pulse"><?= $error ?></div>
      <?php endif ?>
      <form method="post" class="space-y-6 w-full">
        <div>
          <label class="font-semibold text-pink-700 mb-2 block">Username</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-pink-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M5.121 17.804A9 9 0 0112 15c2.487 0 4.73.962 6.879 2.804M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>
            </span>
            <input type="text" name="username" class="pl-10 pr-4 py-3 w-full rounded-xl border-2 border-pink-200 focus:border-pink-400 focus:ring-2 focus:ring-pink-200 transition outline-none text-gray-700 font-medium shadow" required autocomplete="username">
          </div>
        </div>
        <div>
          <label class="font-semibold text-pink-700 mb-2 block">Password</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-pink-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M12 15a3 3 0 100-6 3 3 0 000 6zm6 4v-2a4 4 0 10-8 0v2" /></svg>
            </span>
            <input id="password" type="password" name="password" class="pl-10 pr-10 py-3 w-full rounded-xl border-2 border-pink-200 focus:border-pink-400 focus:ring-2 focus:ring-pink-200 transition outline-none text-gray-700 font-medium shadow" required autocomplete="current-password">
            <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-pink-400 focus:outline-none" onclick="togglePassword()">
              <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path id="eyeOpen" stroke-linecap="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-9 0a9 9 0 0118 0c-1.32 3.09-4.06 6-9 6s-7.68-2.91-9-6z"/>
              </svg>
            </button>
          </div>
        </div>
        <button type="submit" class="mt-2 w-full bg-gradient-to-tr from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-400 text-white rounded-xl py-3 font-bold text-lg brand-font shadow-xl hover:-translate-y-1 hover:shadow-2xl transition">
          Login
        </button>
      </form>
      <div class="pt-6 text-xs text-gray-400 text-center">© 2025 HappyippieCake. Admin Only.</div>
    </div>
  </div>
  <!-- Script toggle password -->
  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.94 0-8.268-3.483-9-6 1.32-3.09 4.06-6 9-6 1.161 0 2.283.154 3.355.45M19.071 4.929l-14.142 14.142"/>';
      } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-9 0a9 9 0 0118 0c-1.32 3.09-4.06 6-9 6s-7.68-2.91-9-6z"/>';
      }
    }
  </script>
</body>
</html>
