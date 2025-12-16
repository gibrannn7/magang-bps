<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Magang BPS Banten</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
    
    <script>
        const CI_BASE_URL = '<?= base_url() ?>';
        const CSRF_NAME = '<?= $this->security->get_csrf_token_name() ?>';
        const CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';
    </script>
</head>
<body class="bg-gray-50 text-gray-800">
    <div id="loading-screen" class="fixed inset-0 bg-white/80 z-[9999] hidden flex justify-center items-center">
        <lottie-player src="<?= base_url('assets/img/loading.json') ?>" background="transparent" speed="1" style="width: 200px; height: 200px;" loop autoplay></lottie-player>
    </div>
