<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRAIL Register</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            min-width: 100vw;
            background: #faf7f7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .register-container {
            background: #fff;
            border: 1px solid #f3caca;
            border-radius: 1.5vw;
            box-shadow: 0 2px 12px rgba(179,7,7,0.06);
            width: 90vw;
            max-width: 400px;
            min-width: 280px;
            padding: 4vw 2vw 4vw 2vw;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .register-title {
            color: #b70707;
            font-size: 2.2vw;
            min-font-size: 1.2rem;
            margin-bottom: 0.5vw;
            text-align: center;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .register-subtitle {
            color: #991b1b;
            font-size: 1.2vw;
            min-font-size: 1rem;
            text-align: center;
            margin-bottom: 2vw;
        }
        .form-label {
            display: block;
            font-size: 1vw;
            min-font-size: 0.97rem;
            color: #991b1b;
            margin-bottom: 0.5vw;
            font-weight: 500;
        }
        form {
            width: 100%;
        }
        .form-input {
            width: 90%;
            display: block;
            margin-left: auto;
            margin-right: auto;
            padding: 0.7vw 1vw;
            border: 1px solid #f3caca;
            border-radius: 0.7vw;
            background: #fff;
            font-size: 1vw;
            min-font-size: 1rem;
            margin-bottom: 1.2vw;
            transition: border 0.2s;
            box-sizing: border-box;
        }
        .form-input:focus {
            border: 1.5px solid #d30707;
            outline: none;
        }
        .form-select {
            width: 90%;
            display: block;
            margin-left: auto;
            margin-right: auto;
            padding: 0.7vw 1vw;
            border: 1px solid #f3caca;
            border-radius: 0.7vw;
            background: #fff;
            font-size: 1vw;
            min-font-size: 1rem;
            margin-bottom: 1.2vw;
            transition: border 0.2s;
            box-sizing: border-box;
            cursor: pointer;
        }
        .form-select:focus {
            border: 1.5px solid #d30707;
            outline: none;
        }
        .form-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #ffcccc;
            border-radius: 0.7vw;
            padding: 0.7vw 1vw;
            font-size: 1vw;
            min-font-size: 0.95rem;
            margin-bottom: 1.2vw;
            width: 100%;
            box-sizing: border-box;
        }
        .register-btn {
            width: 100%;
            background: #d30707;
            color: #fff;
            border: none;
            border-radius: 0.7vw;
            padding: 0.9vw 0;
            font-size: 1.1vw;
            min-font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.18s;
            margin-top: 0.2vw;
        }
        .register-btn:hover, .register-btn:focus {
            background: #991b1b;
        }
        .register-link {
            display: block;
            text-align: center;
            margin-top: 1.5vw;
            font-size: 1vw;
            min-font-size: 0.97rem;
            color: #991b1b;
        }
        .register-link a {
            color: #d30707;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.18s;
        }
        .register-link a:hover {
            color: #991b1b;
        }
        @media (max-width: 500px) {
            .register-container {
                max-width: 98vw;
                padding: 6vw 3vw;
            }
            .register-title, .register-subtitle, .form-label, .form-input, .form-select, .form-error, .register-btn, .register-link {
                font-size: 1rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-title">GRAIL</div>
        <div class="register-subtitle">Create your account</div>
        @if(session('error'))
            <div class="form-error">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('register') }}" style="width:100%">
            @csrf
            <label for="name" class="form-label">Full Name</label>
            <input id="name" type="text" name="name" required autofocus class="form-input" placeholder="Enter your full name">

            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" required class="form-input" placeholder="Enter your email">

            <label for="user_type" class="form-label">Role</label>
            <select id="user_type" name="user_type" required class="form-select">
                <option value="">Select your role</option>
                <option value="teacher">Teacher</option>
                <option value="department_head">Department Head</option>
            </select>

            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" required class="form-input" placeholder="Enter your password">

            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="form-input" placeholder="Confirm your password">

            <button type="submit" class="register-btn">Create Account</button>
        </form>
        <div class="register-link">
            Already have an account?
            <a href="{{ route('login') }}">Sign in</a>
        </div>
    </div>
</body>
</html> 