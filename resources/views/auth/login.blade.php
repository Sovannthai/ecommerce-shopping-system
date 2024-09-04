<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Form</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@200;300;400;500;600;700&display=swap">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Open Sans", sans-serif;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' version='1.1' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns:svgjs='http://svgjs.dev/svgjs' width='1440' height='560' preserveAspectRatio='none' viewBox='0 0 1440 560'%3e%3cg mask='url(%26quot%3b%23SvgjsMask1041%26quot%3b)' fill='none'%3e%3crect width='1440' height='560' x='0' y='0' fill='%230e2a47'%3e%3c/rect%3e%3cpath d='M562.52 452.58L609.28 452.58L609.28 499.34L562.52 499.34z' stroke='%23e73635'%3e%3c/path%3e%3cpath d='M316.23 129.22L334.55 129.22L334.55 147.54L316.23 147.54z' stroke='%23037b0b'%3e%3c/path%3e%3cpath d='M1341.97 199.42L1391.22 199.42L1391.22 214.11L1341.97 214.11z' fill='%23037b0b'%3e%3c/path%3e%3cpath d='M732.62 354.59 a9.22 9.22 0 1 0 18.44 0 a9.22 9.22 0 1 0 -18.44 0z' fill='%23d3b714'%3e%3c/path%3e%3cpath d='M570.3 20.91L582.29 20.91L582.29 32.9L570.3 32.9z' fill='%23037b0b'%3e%3c/path%3e%3cpath d='M195.36 21.39a10.29 10.29 0 1 0-16.84-11.83z' stroke='%23e73635'%3e%3c/path%3e%3cpath d='M439.68 349.07 a41.44 41.44 0 1 0 82.88 0 a41.44 41.44 0 1 0 -82.88 0z' stroke='%23e73635'%3e%3c/path%3e%3cpath d='M1373.96 512.74a48.28 48.28 0 1 0 61.37 74.55z' stroke='%23d3b714'%3e%3c/path%3e%3cpath d='M325.17 259.78a39.48 39.48 0 1 0-63.89-46.4z' fill='%23037b0b'%3e%3c/path%3e%3cpath d='M34.8 345.03a52.45 52.45 0 1 0 36.79 98.24z' fill='%23d3b714'%3e%3c/path%3e%3cpath d='M156.98 197.02L187.16 197.02L187.16 230.61L156.98 230.61z' stroke='%23d3b714'%3e%3c/path%3e%3cpath d='M686.16 465.3L686.42 465.3L686.42 465.56L686.16 465.56z' stroke='%23d3b714'%3e%3c/path%3e%3cpath d='M1252.87 395.6L1262.32 395.6L1262.32 443.26L1252.87 443.26z' fill='%23e73635'%3e%3c/path%3e%3cpath d='M-8.28 396.16a15.47 15.47 0 1 0 30.88 1.94z' fill='%23d3b714'%3e%3c/path%3e%3cpath d='M194.34 432.23 a44.85 44.85 0 1 0 89.7 0 a44.85 44.85 0 1 0 -89.7 0z' fill='%23d3b714'%3e%3c/path%3e%3cpath d='M437.67 248.1a25.07 25.07 0 1 0-9.46 49.24z' stroke='%23037b0b'%3e%3c/path%3e%3cpath d='M797.81 174.93 a8.13 8.13 0 1 0 16.26 0 a8.13 8.13 0 1 0 -16.26 0z' stroke='%23d3b714'%3e%3c/path%3e%3cpath d='M376.9 241.27L394.53 241.27L394.53 257.11L376.9 257.11z' stroke='%23037b0b'%3e%3c/path%3e%3cpath d='M605.88 208.98a11.02 11.02 0 1 0-21.29 5.73z' stroke='%23037b0b'%3e%3c/path%3e%3cpath d='M982.45 286.74L1006.24 286.74L1006.24 310.53L982.45 310.53z' fill='%23e73635'%3e%3c/path%3e%3cpath d='M1208.37 421.55 a6.47 6.47 0 1 0 12.94 0 a6.47 6.47 0 1 0 -12.94 0z' fill='%23037b0b'%3e%3c/path%3e%3cpath d='M1087.83 260.65a24.95 24.95 0 1 0 7.94-49.27z' stroke='%23d3b714'%3e%3c/path%3e%3cpath d='M977.57 538.11L982.95 538.11L982.95 576.81L977.57 576.81z' fill='%23e73635'%3e%3c/path%3e%3cpath d='M941.22 155.88 a29.94 29.94 0 1 0 59.88 0 a29.94 29.94 0 1 0 -59.88 0z' stroke='%23d3b714'%3e%3c/path%3e%3cpath d='M960.41 71.26a53.27 53.27 0 1 0 87.08 61.38z' stroke='%23d3b714'%3e%3c/path%3e%3cpath d='M571.64 454.3L594.51 454.3L594.51 477.17L571.64 477.17z' fill='%23037b0b'%3e%3c/path%3e%3cpath d='M1099.75 446.68L1152.76 446.68L1152.76 499.69L1099.75 499.69z' fill='%23037b0b'%3e%3c/path%3e%3cpath d='M666.34 297.85L666.86 297.85L666.86 298.37L666.34 298.37z' stroke='%23d3b714'%3e%3c/path%3e%3cpath d='M661.89 72.81a2.71 2.71 0 1 0-4.98-2.13z' stroke='%23d3b714'%3e%3c/path%3e%3cpath d='M1136.04 155.62a44.91 44.91 0 1 0-87.23 21.41z' fill='%23037b0b'%3e%3c/path%3e%3cpath d='M682.92 379.02 a13.02 13.02 0 1 0 26.04 0 a13.02 13.02 0 1 0 -26.04 0z' fill='%23e73635'%3e%3c/path%3e%3cpath d='M852.1 420.15L885.86 420.15L885.86 453.91L852.1 453.91z' fill='%23d3b714'%3e%3c/path%3e%3cpath d='M1322.84 339.67L1377.96 339.67L1377.96 394.79L1322.84 394.79z' fill='%23e73635'%3e%3c/path%3e%3c/g%3e%3cdefs%3e%3cmask id='SvgjsMask1041'%3e%3crect width='1440' height='560' fill='white'%3e%3c/rect%3e%3c/mask%3e%3c/defs%3e%3c/svg%3e");
            background-position: center;
            background-size: cover;
            padding: 0 10px;
        }

        .wrapper {
            width: 90%;
            max-width: 400px;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            border: 2px solid rgba(255, 255, 255, 0.763);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            background: rgba(57, 54, 54, 0.15);
            margin: auto;
            transition: 0.5s;
        }

        .wrapper:hover {
            transform: 5s;
            transform: translateY(-15px);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #fff;
        }

        .input-field {
            position: relative;
            margin: 15px 0;
        }

        .input-field input {
            width: 100%;
            height: 40px;
            background: transparent;
            border: none;
            outline: none;
            font-size: 16px;
            color: #fff;
            border-bottom: 2px solid #ccc;
            transition: border-color 0.3s ease;
        }

        .input-field input:focus {
            border-bottom-color: #fff;
        }

        .input-field label {
            position: absolute;
            top: 10px;
            left: 0;
            color: #fff;
            font-size: 16px;
            pointer-events: none;
            transition: 0.15s ease;
        }

        .input-field input:focus~label,
        .input-field input:valid~label {
            top: -10px;
            font-size: 12px;
        }

        .forget {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 25px 0 35px 0;
            color: #fff;
        }

        .forget a {
            color: #fff;
            text-decoration: none;
        }

        .forget a:hover {
            text-decoration: underline;
        }

        button {
            background: #fff;
            color: #000;
            font-weight: 600;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 3px;
            font-size: 16px;
            border: 2px solid transparent;
            transition: 0.3s ease;
            margin-top: 15px;
        }

        button:hover {
            color: #fff;
            border-color: #fff;
            background: rgba(255, 255, 255, 0.15);
        }

        .register {
            text-align: center;
            margin-top: 30px;
            color: #fff;
        }

        .register a {
            color: #fff;
            text-decoration: none;
        }

        .register a:hover {
            text-decoration: underline;
        }

        .input-field {
            position: relative;
            margin-bottom: 1rem;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .btn-telegram {
            background-color: #0088cc;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            text-align: center;
            margin-top: 10px;
            cursor: pointer;
        }

        .btn-telegram:hover {
            background-color: #006699;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="row">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <h2>Login</h2>
                <!-- Email Input Field -->
                <div class="input-field">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="off" autofocus>
                    <label for="email">Enter your email</label>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong style="color: rgb(255, 98, 98);">{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Password Input Field with Toggle -->
                <div class="input-field">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="off">
                    <label for="password">Enter your password</label>
                    <i class="toggle-password fa fa-eye" onclick="togglePasswordVisibility()"></i>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong style="color: rgb(255, 98, 98);">{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Remember Me and Forgot Password -->
                <div class="forget">
                    <label for="remember">
                        <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                    <a href="#">Forgot password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit">Log In</button>

                <!-- Register Prompt -->
                <div class="register">
                    <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
                </div>
            </form>
            <!-- Telegram Login Widget -->
            <script async src="https://telegram.org/js/telegram-widget.js?7" data-telegram-login="NotificatonServiceLogin887_bot"
                data-size="large" data-auth-url="{{ route('telegram_callback') }}" data-request-access="write"></script>
            <script type="text/javascript">
                function onTelegramAuth(user) {
                    fetch('{{ route('telegram_callback') }}', {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(user)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = '/home'; // Redirect to the home route on successful login
                            } else {
                                alert('Login failed.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }
            </script>

        </div>
    </div>

</body>

</html>
