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

$pesan   = '';
$tipe    = '';
$sukses  = false;

if (isset($_POST['register'])) {
    $nama       = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $nim        = mysqli_real_escape_string($conn, trim($_POST['nim']));
    $username   = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password   = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi_password'];

    if ($nama === '' || $nim === '' || $username === '' || $password === '' || $konfirmasi === '') {
        $pesan = "Semua field wajib diisi!";
        $tipe  = "danger";
    } elseif (strlen($password) < 8) {
        $pesan = "Password minimal 8 karakter!";
        $tipe  = "danger";
    } elseif ($password !== $konfirmasi) {
        $pesan = "Password dan konfirmasi password tidak sama!";
        $tipe  = "danger";
    } else {
        $cekNim = mysqli_query($conn, "SELECT id_user FROM user WHERE nim='$nim'");
        if (mysqli_num_rows($cekNim) > 0) {
            $pesan = "NIM sudah terdaftar! Gunakan NIM lain.";
            $tipe  = "danger";
        } else {
            $cekUser = mysqli_query($conn, "SELECT id_user FROM user WHERE username='$username'");
            if (mysqli_num_rows($cekUser) > 0) {
                $pesan = "Username sudah digunakan! Pilih username lain.";
                $tipe  = "danger";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $role = 'user';
                $insert = mysqli_query($conn, "INSERT INTO user (nama, nim, username, password, role, created_at)
                    VALUES ('$nama', '$nim', '$username', '$hash', '$role', NOW())");

                if ($insert) {
                    $pesan  = "Registrasi berhasil! Mengalihkan ke halaman login...";
                    $tipe   = "success";
                    $sukses = true;
                } else {
                    $pesan = "Terjadi kesalahan saat menyimpan data. Silakan coba lagi.";
                    $tipe  = "danger";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Inventaris Laboratorium Komputer & Informatika</title>
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

        .brand-list {
            position: relative; z-index: 1;
            margin-top: 60px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }
        .brand-list .list-item { display: flex; align-items: center; gap: 18px; }
        .brand-list .list-icon {
            width: 50px; height: 50px; border-radius: 13px;
            background: rgba(255,255,255,.09);
            border: 1px solid rgba(255,255,255,.14);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: #7dd3fc;
            flex-shrink: 0;
        }
        .brand-list .list-text { font-size: 17.5px; color: rgba(255,255,255,.78); font-weight: 500; }

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
            max-width: 400px;
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
            margin-bottom: 26px;
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
                <strong>Laboratorium Komputer & Informatika</strong>
                <span>Sistem Inventaris</span>
            </div>
        </div>

        <div class="brand-headline">
            <h1>Daftar untuk mulai meminjam alat laboratorium.</h1>
            <p>Buat akun dengan NIM Anda, lalu ajukan peminjaman alat kapan saja secara online.</p>
        </div>

        <div class="brand-list">
            <div class="list-item">
                <div class="list-icon"><i class="bi bi-1-circle"></i></div>
                <div class="list-text">Daftar menggunakan NIM dan data diri</div>
            </div>
            <div class="list-item">
                <div class="list-icon"><i class="bi bi-2-circle"></i></div>
                <div class="list-text">Pilih alat dan ajukan peminjaman</div>
            </div>
            <div class="list-item">
                <div class="list-icon"><i class="bi bi-3-circle"></i></div>
                <div class="list-text">Tunggu persetujuan admin lab</div>
            </div>
        </div>

        <div class="brand-footer">&copy; <?= date('Y') ?> Laboratorium Komputer & Informatika</div>
    </div>
</div>

<div class="form-panel">
    <div class="login-card">
        <div class="icon-wrap"><i class="bi bi-person-plus-fill"></i></div>
        <h2>Daftar Akun</h2>
        <p class="subtitle">Lengkapi data berikut untuk membuat akun baru</p>

        <?php if ($pesan): ?>
        <div class="alert alert-<?= $tipe ?> d-flex align-items-center gap-2 border-0 rounded-3 mb-4"
             style="background:<?= $tipe=='danger'?'#fef2f2':'#f0fdf4' ?>;color:<?= $tipe=='danger'?'#b91c1c':'#15803d' ?>;">
            <i class="bi bi-<?= $tipe=='danger'?'exclamation-triangle':'check-circle' ?>-fill"></i>
            <span><?= htmlspecialchars($pesan) ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" novalidate id="formRegister" style="<?= $sukses ? 'opacity:0.5;pointer-events:none;' : '' ?>">
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                    <input type="text" name="nama" class="form-control"
                           placeholder="Masukkan nama lengkap" required
                           value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">NIM</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                    <input type="text" name="nim" class="form-control"
                           placeholder="Masukkan NIM" required
                           value="<?= isset($_POST['nim']) ? htmlspecialchars($_POST['nim']) : '' ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control"
                           placeholder="Masukkan username" required autocomplete="username"
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="passInput" class="form-control"
                           placeholder="Minimal 8 karakter" required autocomplete="new-password">
                    <button type="button" class="input-group-text btn-eye" style="cursor:pointer;"
                            onclick="togglePass('passInput','eyeIcon')">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Konfirmasi Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                    <input type="password" name="konfirmasi_password" id="confirmInput" class="form-control"
                           placeholder="Ulangi password" required autocomplete="new-password">
                    <button type="button" class="input-group-text btn-eye" style="cursor:pointer;"
                            onclick="togglePass('confirmInput','eyeIcon2')">
                        <i class="bi bi-eye" id="eyeIcon2"></i>
                    </button>
                </div>
            </div>
            <button type="submit" name="register" class="btn-login">
                <i class="bi bi-person-plus me-2"></i>Daftar
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none" style="color:#2563eb;font-size:13.5px;font-weight:600;">
                Sudah punya akun? Masuk
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass(inputId, iconId) {
    const inp = document.getElementById(inputId);
    const ico = document.getElementById(iconId);
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        ico.className = 'bi bi-eye';
    }
}

<?php if ($sukses): ?>
setTimeout(function () {
    window.location.href = 'login.php';
}, 2000);
<?php endif; ?>
</script>
</body>
</html>