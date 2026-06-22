<?php
$username = htmlspecialchars($_SESSION['user']['username']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — Inventaris Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
        <style>
        :root {
            --sidebar-w: 250px;
            --grad-sidebar: linear-gradient(180deg, #0f172a 0%, #14213d 50%, #1e3a5f 100%);
            --brand-primary:   #2563eb;
            --brand-secondary: #0ea5e9;
            --brand-accent:    #4f46e5;
            --grad-brand: linear-gradient(135deg, #0ea5e9 0%, #2563eb 50%, #4f46e5 100%);
        }
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { margin: 0; background: #f0f4ff; min-height: 100vh; }

        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--grad-sidebar);
            display: flex;
            flex-direction: column;
            z-index: 200;
            overflow-y: auto;
            transition: transform .3s;
        }

        .sidebar-header {
            padding: 28px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }

        .sidebar-header .lab-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            background: var(--grad-brand);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            margin-bottom: 12px;
            color: #fff;
        }

        .sidebar-header h2 {
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            margin: 0 0 2px;
            letter-spacing: .01em;
        }

        .sidebar-header span {
            color: rgba(255,255,255,.4);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .07em;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
        }

        .nav-label {
            color: rgba(255,255,255,.25);
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .1em;
            padding: 8px 8px 4px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,.55);
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 10px;
            margin: 2px 0;
            font-size: 14px;
            font-weight: 500;
            transition: background .2s, color .2s;
        }

        .sidebar-nav a:hover {
            background: rgba(255,255,255,.08);
            color: #fff;
        }

        .sidebar-nav a.active {
            background: linear-gradient(135deg, rgba(14,165,233,.55), rgba(37,99,235,.55));
            color: #fff;
            border: 1px solid rgba(255,255,255,.1);
        }

        .sidebar-nav a i { font-size: 17px; }

        .badge-pill {
            background: #ef4444;
            color: #fff;
            border-radius: 50px;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            margin-left: auto;
        }

        .badge-pill-yellow {
            background: #f59e0b;
            color: #fff;
            border-radius: 50px;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            margin-left: auto;
        }
            padding: 2px 7px;
            margin-left: auto;
        }

        .sidebar-footer {
            padding: 16px 12px 20px;
            border-top: 1px solid rgba(255,255,255,.07);
        }

        .sidebar-footer .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .sidebar-footer .avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--grad-brand);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 14px;
        }

        .sidebar-footer .user-name {
            color: #fff;
            font-size: 13px;
            font-weight: 600;
        }

        .sidebar-footer .user-role {
            color: rgba(255,255,255,.4);
            font-size: 11px;
        }

        .sidebar-footer a.logout {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,.4);
            font-size: 13px;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background .2s, color .2s;
        }

        .sidebar-footer a.logout:hover {
            background: rgba(239,68,68,.15);
            color: #fca5a5;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: 64px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            padding: 0 28px;
            z-index: 100;
            gap: 12px;
        }

        .topbar-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            flex: 1;
        }

        .topbar-time {
            font-size: 13px;
            color: #6b7280;
        }


        .main-content {
            margin-left: var(--sidebar-w);
            padding-top: 64px;
            min-height: 100vh;
        }

        .page-body {
            padding: 28px 32px;
        }

        .page-banner {
            background: var(--grad-brand);
            border-radius: 16px;
            padding: 28px 32px;
            margin-bottom: 28px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(37,99,235,.18);
        }

        .page-banner::before {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,.06);
        }

        .page-banner::after {
            content: '';
            position: absolute;
            bottom: -50px; right: 60px;
            width: 150px; height: 150px;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
        }

        .page-banner h1 {
            font-size: 24px;
            font-weight: 800;
            margin: 0 0 4px;
        }

        .page-banner p {
            margin: 0;
            color: rgba(255,255,255,.75);
            font-size: 14px;
        }


        .stat-card {
            border-radius: 16px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            border: none;
            box-shadow: 0 1px 3px rgba(15,23,42,.08), 0 4px 12px rgba(15,23,42,.06);
            transition: transform .2s, box-shadow .2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(15,23,42,.12);
        }

        .stat-card .card-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            background: rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            margin-bottom: 16px;
        }

        .stat-card .card-num {
            font-size: 32px;
            font-weight: 800;
            line-height: 1;
        }

        .stat-card .card-label {
            font-size: 12px;
            font-weight: 500;
            opacity: .75;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-top: 4px;
        }

        .stat-card.indigo  { background: linear-gradient(135deg, #4f46e5, #818cf8); }
        .stat-card.blue    { background: linear-gradient(135deg, #2563eb, #60a5fa); }
        .stat-card.cyan    { background: linear-gradient(135deg, #0ea5e9, #67e8f9); }
        .stat-card.emerald { background: linear-gradient(135deg, #059669, #34d399); }
        .stat-card.amber   { background: linear-gradient(135deg, #d97706, #fbbf24); }
        .stat-card.red     { background: linear-gradient(135deg, #dc2626, #f87171); }

        /* ===== TABLE ===== */
        .table-card {
            background: #fff;
            border-radius: 16px;
            border: none;
            box-shadow: 0 1px 3px rgba(15,23,42,.06), 0 4px 14px rgba(15,23,42,.05);
            overflow: hidden;
        }

        .table-card-header {
            padding: 18px 24px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-card-header h5 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            flex: 1;
        }

        .table > :not(caption) > * > * {
            padding: 13px 18px;
            vertical-align: middle;
        }

        .table thead th {
            background: #f9fafb;
            color: #374151;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            border-bottom: 2px solid #e5e7eb;
            white-space: nowrap;
        }

        .table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: background .15s;
        }

        .table tbody tr:hover { background: #f9fafb; }
        .table tbody tr:last-child { border-bottom: none; }

        .table td { font-size: 13px; color: #374151; }

        .status-badge {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            display: inline-block;
        }

        .badge-menunggu     { background: #fef3c7; color: #92400e; }
        .badge-dipinjam     { background: #dbeafe; color: #1e40af; }
        .badge-dikembalikan { background: #d1fae5; color: #065f46; }
        .badge-terlambat    { background: #fee2e2; color: #991b1b; }
        .badge-ditolak      { background: #f3f4f6; color: #4b5563; }
        .badge-rusak        { background: #fee2e2; color: #991b1b; }

        .form-card {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            border: none;
            box-shadow: 0 1px 3px rgba(15,23,42,.06), 0 4px 14px rgba(15,23,42,.05);
        }

        .form-card label { font-size: 13px; font-weight: 600; color: #374151; }

        .form-card .form-control,
        .form-card .form-select {
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            font-size: 14px;
            padding: 10px 14px;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-card .form-control:focus,
        .form-card .form-select:focus {
            border-color: var(--brand-secondary);
            box-shadow: 0 0 0 3px rgba(14,165,233,.12);
        }

        .filter-card {
            background: #fff;
            border-radius: 16px;
            padding: 22px 24px;
            border: none;
            box-shadow: 0 1px 3px rgba(15,23,42,.06), 0 4px 14px rgba(15,23,42,.05);
        }
        .filter-card label { font-size: 12.5px; font-weight: 600; color: #475569; }
        .filter-card .form-control,
        .filter-card .form-select {
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            font-size: 13.5px;
            padding: 9px 13px;
            transition: border-color .2s, box-shadow .2s;
        }
        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            border-color: var(--brand-secondary);
            box-shadow: 0 0 0 3px rgba(14,165,233,.12);
        }

        .badge-kategori {
            display: inline-block;
            background: #e0f2fe;
            color: #075985;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 11px;
        }

        .btn-primary-grad {
            background: var(--grad-brand);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-weight: 600;
            padding: 10px 20px;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(37,99,235,.22);
            transition: opacity .2s, transform .15s;
        }

        .btn-primary-grad:hover {
            opacity: .9;
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-outline-danger { border-radius: 10px; font-size: 13px; }


        .sidebar-toggle { display: none; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .topbar { left: 0; }
            .sidebar-toggle { display: block; }
            .page-body { padding: 16px; }
        }
    </style>
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="lab-icon"><i class="bi bi-cpu-fill"></i></div>
        <h2>Inventaris Lab</h2>
        <span>Panel Administrator</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Menu Utama</div>
        <a href="dashboard.php" class="<?= ($activePage??'') === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
            <?php
            
            $h1_cnt = mysqli_num_rows(mysqli_query($conn,
                "SELECT id_pinjam FROM peminjaman
                 WHERE status='dipinjam'
                 AND tgl_kembali = CURDATE() + INTERVAL 1 DAY"
            ));
            if ($h1_cnt > 0): ?>
            <span class="badge-pill-yellow"><?= $h1_cnt ?></span>
            <?php endif; ?>
        </a>
        <a href="alat.php" class="<?= ($activePage??'') === 'alat' ? 'active' : '' ?>">
            <i class="bi bi-tools"></i> Data Alat
        </a>
        <a href="peminjaman.php" class="<?= ($activePage??'') === 'peminjaman' ? 'active' : '' ?>">
            <i class="bi bi-journal-check"></i> Peminjaman
            <?php
            $cnt = mysqli_num_rows(mysqli_query($conn,"SELECT id_pinjam FROM peminjaman WHERE status='menunggu'"));
            if ($cnt > 0): ?>
            <span class="badge-pill"><?= $cnt ?></span>
            <?php endif; ?>
        </a>

    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="avatar"><?= strtoupper(substr($username, 0, 1)) ?></div>
            <div>
                <div class="user-name"><?= $username ?></div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
        <a href="../logout.php" class="logout">
            <i class="bi bi-box-arrow-left"></i> Keluar
        </a>
    </div>
</aside>

<header class="topbar">
    <button class="btn sidebar-toggle p-0 me-2" onclick="document.getElementById('sidebar').classList.toggle('open')">
        <i class="bi bi-list fs-4"></i>
    </button>
    <span class="topbar-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></span>
    <span class="topbar-time" id="clock"></span>
</header>

<main class="main-content">
<div class="page-body">
