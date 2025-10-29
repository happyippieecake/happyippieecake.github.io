<?php
session_start();
$error = '';
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123'); // password bisa diganti sesuai keinginan

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
</head>
<body class="bg-pink-50">
  <div class="flex min-h-screen flex-col items-center justify-center">
    <div class="w-full max-w-sm bg-white rounded-xl shadow p-8">
      <h1 class="text-2xl mb-6 font-bold text-pink-600 text-center">Login Admin</h1>
      <?php if($error): ?>
        <div class="mb-4 bg-red-100 border-l-4 border-red-400 text-red-700 p-3 rounded"><?= $error ?></div>
      <?php endif ?>
      <form method="post" class="space-y-4">
        <div>
          <label class="font-semibold mb-1 text-pink-700">Username</label>
          <input type="text" name="username" class="w-full border border-pink-200 rounded p-2" required>
        </div>
        <div>
          <label class="font-semibold mb-1 text-pink-700">Password</label>
          <input type="password" name="password" class="w-full border border-pink-200 rounded p-2" required>
        </div>
        <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white py-2 rounded font-bold">Login</button>
      </form>
    </div>
  </div>
</body>
</html>
