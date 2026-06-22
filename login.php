<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/home.php");
    }
    exit;
}

$pesan = '';
$tipe  = '';

if (isset($_POST['login'])) {
    $u = mysqli_real_escape_string($conn, trim($_POST['username']));
    $p = $_POST['password'];

    if ($u === '' || $p === '') {
        $pesan = "Username dan password wajib diisi!";
        $tipe  = "danger";
    } else {
        $query = mysqli_query($conn, "SELECT * FROM user WHERE username='$u'");
        $data  = mysqli_fetch_assoc($query);

        if ($data) {
            if (password_verify($p, $data['password'])) {
                $_SESSION['user'] = $data;
                if ($data['role'] == 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: user/home.php");
                }
                exit;
            } else {
                $pesan = "Password salah! Silakan coba lagi.";
                $tipe  = "danger";
            }
        } else {
            $pesan = "Username tidak ditemukan!";
            $tipe  = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Inventaris Lab Komputer & Elektronika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
        :root {
            --navy-deep: #0f172a;
            --brand-primary:   #2563eb;
            --brand-secondary: #0ea5e9;
            --brand-accent:    #4f46e5;
            --grad-brand: linear-gradient(135deg, #0ea5e9 0%, #2563eb 50%, #4f46e5 100%);
        }

        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--navy-deep);
            display: flex;
        }

        .brand-panel {
            position: relative;
            flex: 1.55;
            background: linear-gradient(150deg, #0f172a 0%, #14305c 45%, #0ea5e9 145%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 64px 72px;
            overflow: hidden;
            color: #fff;
        }

        .brand-panel .grid-overlay {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.045) 1px, transparent 1px);
            background-size: 46px 46px;
            mask-image: radial-gradient(ellipse 85% 85% at 30% 30%, #000 30%, transparent 80%);
        }

        .brand-panel .glow {
            position: absolute;
            width: 620px; height: 620px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37,99,235,.4) 0%, transparent 70%);
            top: -160px; right: -160px;
            pointer-events: none;
        }

        .brand-panel .glow2 {
            position: absolute;
            width: 480px; height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79,70,229,.3) 0%, transparent 70%);
            bottom: -180px; left: -120px;
            pointer-events: none;
        }

        .brand-top {
            position: relative; z-index: 1;
            max-width: 660px;
        }

        .brand-mark {
            display: flex; align-items: center; gap: 18px;
            margin-bottom: 56px;
        }
        .brand-mark .mark-icon {
            width: 64px; height: 64px; border-radius: 16px;
            background: var(--grad-brand);
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; color: #fff;
            box-shadow: 0 10px 26px rgba(37,99,235,.4);
            flex-shrink: 0;
        }
        .brand-mark .mark-text { line-height: 1.25; }
        .brand-mark .mark-text strong { display: block; font-size: 22px; font-weight: 800; letter-spacing: -.01em; }
        .brand-mark .mark-text span { font-size: 13px; color: rgba(255,255,255,.55); text-transform: uppercase; letter-spacing: .1em; }

        .brand-headline h1 {
            font-size: clamp(44px, 5.2vw, 76px);
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: -.02em;
            margin: 0 0 26px;
        }
        .brand-headline p {
            font-size: clamp(18px, 1.5vw, 23px);
            color: rgba(255,255,255,.68);
            max-width: 560px;
            line-height: 1.6;
            margin: 0;
        }

        .brand-features {
            position: relative; z-index: 1;
            display: flex; flex-direction: column;
            gap: 22px;
            margin-top: 60px;
        }
        .brand-features .feature-item { display: flex; align-items: center; gap: 18px; }
        .brand-features .feature-icon {
            width: 58px; height: 58px; border-radius: 14px;
            background: rgba(255,255,255,.09);
            border: 1px solid rgba(255,255,255,.14);
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; color: #7dd3fc;
            flex-shrink: 0;
        }
        .brand-features .feature-text strong { display: block; font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 2px; }
        .brand-features .feature-text span { font-size: 14.5px; color: rgba(255,255,255,.5); }

        .brand-footer {
            position: relative; z-index: 1;
            font-size: 13px; color: rgba(255,255,255,.35);
            margin-top: 56px;
        }

        .form-panel {
            flex: 1;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .login-card {
            width: 100%;
            max-width: 380px;
        }

        .login-card .icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: var(--grad-brand);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 22px;
            box-shadow: 0 8px 18px rgba(37,99,235,.25);
        }
        .login-card .icon-wrap i { color: #fff; }

        .login-card h2 {
            color: #0f172a;
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .login-card .subtitle {
            color: #64748b;
            font-size: 13.5px;
            margin-bottom: 30px;
        }

        .form-label {
            color: #334155;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            color: #0f172a;
            padding: 11px 14px;
            font-size: 14px;
            transition: border-color .15s, box-shadow .15s;
        }

        .form-control::placeholder { color: #94a3b8; }

        .form-control:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }

        .input-group-text {
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-right: none;
            color: #94a3b8;
            border-radius: 10px 0 0 10px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .input-group:focus-within .input-group-text { border-color: var(--brand-primary); }

        .input-group .btn-eye {
            border: 1.5px solid #e2e8f0;
            border-left: none;
            background: #fff;
            color: #94a3b8;
            border-radius: 0 10px 10px 0 !important;
        }

        .btn-login {
            background: var(--grad-brand);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-weight: 700;
            font-size: 14.5px;
            padding: 12px;
            width: 100%;
            transition: opacity .2s, transform .15s;
            box-shadow: 0 6px 16px rgba(37,99,235,.3);
        }

        .btn-login:hover { opacity: .92; transform: translateY(-1px); color: #fff; }
        .btn-login:active { transform: translateY(0); }

        .cred-info {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 13px 15px;
            margin-top: 22px;
        }
        .cred-info p { color: #64748b; font-size: 12px; margin: 0; line-height: 1.8; }
        .cred-info span { color: var(--brand-primary); font-weight: 600; }

        @media (max-width: 1100px) {
            .brand-headline h1 { font-size: clamp(36px, 5vw, 56px); }
        }
        @media (max-width: 900px) {
            .brand-panel { display: none; }
        }
    </style>
</head>
<body>

<div class="brand-panel">
    <div class="grid-overlay"></div>
    <div class="glow"></div>
    <div class="glow2"></div>

    <div class="brand-top">
        <div class="brand-mark">
            <div class="mark-icon"><i class="bi bi-cpu-fill"></i></div>
            <div class="mark-text">
                <strong>Laboratorium Komputer dan Informatika</strong>
                <span>Sistem Inventaris</span>
            </div>
        </div>

        <div class="brand-headline">
            <h1>Sistem Peminjaman Alat Laboratorium.</h1>
            <p>Menyediakan layanan peminjaman alat, pengelolaan inventaris, serta monitoring status peminjaman dan pengembalian secara terpusat.</p>
        </div>

        <div class="brand-features">
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-box-seam"></i></div>
                <div class="feature-text"><strong>Data Alat</strong><span>Informasi alat yang tersedia</span></div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                <div class="feature-text"><strong>Persetujuan</strong><span>Ajukan peminjaman secara online</span></div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-clock-history"></i></div>
                <div class="feature-text"><strong>Riwayat</strong><span>Cek status dan pengembalian alat</span></div>
            </div>
        </div>

        <div class="brand-footer">&copy; <?= date('Y') ?> Laboratorium Komputer & Informatika</div>
    </div>
</div>

<!-- RIGHT: Login form -->
<div class="form-panel">
    <div class="login-card">
        <div class="icon-wrap"><i class="bi bi-box-arrow-in-right"></i></div>
        <h2>Masuk ke Sistem</h2>
        <p class="subtitle">Masuk untuk mengakses layanan peminjaman alat laboratorium</p>

        <?php if ($pesan): ?>
        <div class="alert alert-<?= $tipe ?> d-flex align-items-center gap-2 border-0 rounded-3 mb-4"
             style="background:<?= $tipe=='danger'?'#fef2f2':'#f0fdf4' ?>;color:<?= $tipe=='danger'?'#b91c1c':'#15803d' ?>;">
            <i class="bi bi-<?= $tipe=='danger'?'exclamation-triangle':'check-circle' ?>-fill"></i>
            <span><?= htmlspecialchars($pesan) ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control"
                           placeholder="Masukkan username" required autocomplete="username"
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="passInput" class="form-control"
                           placeholder="Masukkan password" required autocomplete="current-password">
                    <button type="button" class="input-group-text btn-eye" style="cursor:pointer;" onclick="togglePass()">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" name="login" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="register.php" class="text-decoration-none" style="color:#2563eb;font-size:13.5px;font-weight:600;">
                Belum punya akun? Daftar di sini
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass() {
    const inp = document.getElementById('passInput');
    const ico = document.getElementById('eyeIcon');
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        ico.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>