<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen flex items-center justify-center bg-cover bg-center" style="background-image: url('<?= base_url('assets/img/background.jpeg') ?>');">
    <div class="absolute inset-0 bg-black opacity-50"></div>

    <div class="relative z-10 bg-white p-8 rounded-xl shadow-2xl max-w-sm w-full mx-4">
        <div class="text-center mb-6">
            <img src="<?= base_url('assets/img/logo.png') ?>" class="h-16 mx-auto mb-2">
            <h2 class="text-2xl font-bold text-gray-800">Login Peserta & Admin</h2>
            <p class="text-gray-500 text-sm">Masuk untuk mengakses sistem</p>
        </div>
        
        <form action="<?= base_url('auth/process_login') ?>" method="POST">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                <input type="text" name="username" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button type="submit" class="w-full bg-blue-900 text-white font-bold py-2 rounded-lg hover:bg-blue-800 transition">MASUK</button>
        </form>
    </div>
</body>
</html>
