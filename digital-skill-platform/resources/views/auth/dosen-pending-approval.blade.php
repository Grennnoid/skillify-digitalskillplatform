<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Dosen Pending | Skillify</title>
    <style>
        :root {
            --bg: #070b14;
            --panel: rgba(12, 20, 37, 0.88);
            --line: rgba(151, 177, 226, 0.26);
            --text: #e7f0ff;
            --muted: #93a8ce;
            --primary: #45d0ff;
            --primary-2: #7cf6d6;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: "Segoe UI", "Inter", Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(900px 460px at 18% -12%, rgba(69, 208, 255, 0.2), transparent 58%),
                radial-gradient(900px 460px at 82% -18%, rgba(124, 246, 214, 0.18), transparent 58%),
                linear-gradient(180deg, #050911 0%, #060a12 100%);
            padding: 20px;
        }

        .card {
            width: min(700px, 100%);
            border: 1px solid var(--line);
            background: var(--panel);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 26px 56px rgba(3, 8, 21, 0.45);
        }

        h1 {
            margin: 0 0 10px;
            font-size: 30px;
            letter-spacing: -0.2px;
        }

        p {
            margin: 0;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        .note {
            margin-top: 16px;
            border: 1px solid rgba(69, 208, 255, 0.35);
            border-radius: 12px;
            background: rgba(12, 30, 52, 0.5);
            color: #c9e7ff;
            padding: 12px 14px;
            font-size: 14px;
        }

        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            padding: 10px 14px;
        }

        .btn-outline {
            color: #d9e8ff;
            border: 1px solid var(--line);
            background: rgba(17, 27, 48, 0.6);
        }

        .btn-primary {
            color: #041220;
            background: linear-gradient(120deg, var(--primary), var(--primary-2));
        }
    </style>
</head>
<body>
<main class="card">
    @if(session('success'))
        <div class="note" style="margin-top: 0; margin-bottom: 14px; border-color: rgba(124, 246, 214, 0.4); color: #c8ffe8;">
            {{ session('success') }}
        </div>
    @endif
    <h1>Pengajuan Dosen Sedang Diproses</h1>
    <p>
        Akun kamu sudah terdaftar, tapi akses dosen belum aktif.
        Pengajuan kamu sebagai dosen/mentor harus disetujui admin terlebih dulu.
    </p>
    <div class="note">
        Status saat ini: <strong>Pending Approval</strong>. Setelah disetujui, kamu bisa langsung masuk ke Dosen Dashboard.
    </div>
    <div class="actions">
        <a class="btn btn-outline" href="{{ route('landing') }}">Back to Landing</a>
        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
            @csrf
            <button class="btn btn-primary" type="submit" style="border:0;cursor:pointer;">Logout</button>
        </form>
    </div>
</main>
</body>
</html>
