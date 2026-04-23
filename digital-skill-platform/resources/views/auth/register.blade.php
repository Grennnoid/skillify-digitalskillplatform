<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Skillify</title>
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
            width: min(500px, 100%);
            border: 1px solid var(--line);
            background: var(--panel);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 26px 56px rgba(3, 8, 21, 0.45);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 28px;
            letter-spacing: -0.2px;
        }

        p {
            margin: 0 0 22px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .field { margin-bottom: 16px; }

        label {
            display: block;
            margin-bottom: 7px;
            color: #c7d8f6;
            font-size: 13px;
        }

        input, select {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
            background: rgba(7, 14, 28, 0.84);
            color: var(--text);
            outline: none;
        }

        input:focus, select:focus {
            border-color: rgba(69, 208, 255, 0.72);
            box-shadow: 0 0 0 3px rgba(69, 208, 255, 0.15);
        }

        .error {
            margin-bottom: 14px;
            border: 1px solid rgba(251, 113, 133, 0.4);
            border-radius: 12px;
            background: rgba(80, 21, 33, 0.5);
            color: #ffd5db;
            padding: 11px 12px;
            font-size: 13px;
        }

        .btn {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #041220;
            background: linear-gradient(120deg, var(--primary), var(--primary-2));
            cursor: pointer;
        }

        .btn:hover { filter: brightness(1.05); }

        .foot {
            margin-top: 15px;
            font-size: 13px;
            color: var(--muted);
            text-align: center;
        }

        a {
            color: #9deeff;
            text-decoration: none;
        }

        a:hover { text-decoration: underline; }

        .site-footer {
            margin-top: 18px;
            color: var(--muted);
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div>
    <main class="card">
        <h1>Create Account</h1>
        <p>Start your journey with a clean and future-ready learning platform.</p>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="field">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="field">
                <label for="role">Register As</label>
                <select id="role" name="role" required>
                    <option value="student" @selected(old('role', $preferredRole ?? 'student') === 'student')>Student</option>
                    <option value="dosen" @selected(old('role', $preferredRole ?? 'student') === 'dosen')>Dosen / Mentor (Need Admin Approval)</option>
                </select>
            </div>

    

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn">Sign Up</button>
        </form>

        <div class="foot">
            Already have an account? <a href="{{ route('login') }}">Login</a>
            <br>
            <a href="{{ route('landing') }}">Back to landing page</a>
        </div>
    </main>
    <footer class="site-footer">
        <p>&copy; {{ date('Y') }} Skillify</p>
    </footer>
    </div>
</body>
</html>
