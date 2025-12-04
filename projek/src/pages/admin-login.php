<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../../models/User.php';
require_once '../../config/database.php';

$error = '';
$loading = false;

function getLoginAttemptsPath($username)
{
    return __DIR__ . '/../../tmp/login_attempts/' . md5($username) . '.json';
}

function getLoginAttempts($username)
{
    $file = getLoginAttemptsPath($username);
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        return $data;
    }
    return ['count' => 0, 'blocked_time' => 0];
}

function saveLoginAttempts($username, $data)
{
    $file = getLoginAttemptsPath($username);
    file_put_contents($file, json_encode($data));
}

function isBlocked($username)
{
    $attempts = getLoginAttempts($username);
    return $attempts['count'] >= 3 && (time() - $attempts['blocked_time']) < 7 * 24 * 3600; // 1 minggu dalam detik
}

function getBlockTimeRemaining($username)
{
    $attempts = getLoginAttempts($username);
    if ($attempts['count'] >= 3) {
        return max(0, ($attempts['blocked_time'] + 7 * 24 * 3600) - time());
    }
    return 0;
}

function getRemainingAttempts($username)
{
    $attempts = getLoginAttempts($username);
    return max(0, 3 - $attempts['count']);
}

function resetLoginAttempts($username)
{
    $file = getLoginAttemptsPath($username);
    if (file_exists($file)) {
        unlink($file);
    }
}

if ($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    $user->username = $_POST['username'];
    $user->password = $_POST['password'];

    // Cek jika user diblokir
    if (isBlocked($user->username)) {
        $blockTime = getBlockTimeRemaining($user->username);
        $hours = floor($blockTime / 3600);
        $minutes = floor(($blockTime % 3600) / 60);
        $error = "Akun anda telah diblokir karena terlalu banyak percobaan gagal. Silakan coba lagi dalam {$hours} jam {$minutes} menit.";
    } elseif ($user->login()) {
        // Reset login attempts jika berhasil
        resetLoginAttempts($user->username);

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user->id;
        $_SESSION['admin_username'] = $user->username;
        header("Location: admin-packages.php");
        exit();
    } else {
        // Tambah hitungan percobaan gagal
        $attempts = getLoginAttempts($user->username);
        $attempts['count']++;
        if ($attempts['count'] >= 3) {
            $attempts['blocked_time'] = time();
        }
        saveLoginAttempts($user->username, $attempts);

        $remainingAttempts = getRemainingAttempts($user->username);
        if ($remainingAttempts > 0) {
            $error = "Username atau password salah! Sisa percobaan: {$remainingAttempts} kali";
        } else {
            $error = "Username atau password salah! Akun anda telah diblokir selama 1 minggu karena terlalu banyak percobaan gagal.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Samira Travel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        skyblue: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        @keyframes slide-down {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-in {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate-slide-down {
            animation: slide-down 0.6s ease-out;
        }

        .animate-slide-up {
            animation: slide-up 0.6s ease-out;
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-out;
        }
    </style>
</head>

<body>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-primary-900 to-skyblue-900 p-6 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-primary-500/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-skyblue-500/20 rounded-full blur-3xl animate-bounce"></div>
            <div class="absolute top-3/4 left-1/3 w-48 h-48 bg-primary-400/10 rounded-full blur-2xl animate-pulse"></div>
        </div>

        <!-- Login Container -->
        <div class="relative z-10 w-full max-w-md">
            <!-- Back to Home Button -->
            <div class="mb-6 animate-slide-down">
                <a href="../../index.php" class="flex items-center space-x-2 text-white/80 hover:text-white transition-colors duration-200 group">
                    <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="text-sm font-medium">Kembali ke Halaman Utama</span>
                </a>
            </div>

            <!-- Logo/Brand Section -->
            <div class="text-center mb-8 animate-slide-down">
                <div class="inline-block p-4 rounded-full bg-gradient-to-r from-primary-500 to-skyblue-500 shadow-2xl mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Samira Travel</h1>
                <p class="text-slate-300">Admin Dashboard</p>
            </div>

            <!-- Login Card -->
            <div class="glass rounded-2xl shadow-2xl p-8 animate-slide-up">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-slate-800 mb-2">Selamat Datang Kembali</h2>
                    <p class="text-slate-600">Masuk ke panel admin untuk mengelola travel</p>
                </div>

                <?php if ($error): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl animate-slide-down">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                        <input type="text" id="username" name="username" required
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-primary-500 to-skyblue-500 hover:from-primary-600 hover:to-skyblue-600 text-white font-semibold px-6 py-4 rounded-xl shadow-xl transition-all duration-300 transform hover:scale-105">
                        Masuk ke Dashboard
                    </button>
                </form>

                <!-- Additional Info -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-slate-500">
                        Hanya admin yang diotorisasi yang dapat mengakses dashboard ini
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 animate-fade-in">
                <p class="text-slate-400 text-sm">© 2024 Samira Travel. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>

</html>