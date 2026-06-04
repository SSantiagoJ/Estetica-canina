<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Intranet - Pet Grooming</title>

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/intranet-login.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="intranet-login-page" data-welcome="Bienvenido a la intranet Pet Grooming">
    <div class="container">
        <div class="auth-wrapper intranet-wrapper">
            <div class="auth-left intranet-left">
                <div class="brand-content">
                    <div class="logo">
                        <i class="fas fa-shield-halved"></i>
                        <h1>Intranet</h1>
                    </div>
                    <h2>Acceso del equipo</h2>
                    <p>Portal exclusivo para trabajadores, supervisores y administradores de Pet Grooming.</p>
                    <div class="intranet-badges" aria-label="Roles permitidos">
                        <span><i class="fas fa-user-nurse"></i> Trabajadores</span>
                        <span><i class="fas fa-user-check"></i> Supervisor</span>
                        <span><i class="fas fa-user-shield"></i> Admin</span>
                    </div>
                </div>
            </div>

            <div class="auth-right">
                <div class="form-container">
                    <div class="form-wrapper active" id="loginForm">
                        <div class="form-header">
                            <h3>Acceso Intranet</h3>
                            <p>Ingresa con tu correo corporativo o usuario autorizado.</p>
                        </div>

                        <form class="auth-form" id="loginFormElement" method="POST" action="{{ route('intranet.login.process') }}">
                            @csrf

                            <div class="input-group">
                                <div class="input-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input
                                        type="email"
                                        id="loginEmail"
                                        name="correo"
                                        placeholder="Correo de intranet"
                                        required
                                        autocomplete="email">
                                </div>
                            </div>

                            <div class="input-group">
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input
                                        type="password"
                                        id="loginPassword"
                                        name="password"
                                        placeholder="Contrasena"
                                        required
                                        autocomplete="current-password">
                                    <button type="button" class="toggle-password" onclick="togglePassword('loginPassword')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-options">
                                <label class="checkbox-wrapper">
                                    <input type="checkbox" name="remember">
                                    <span class="checkmark"></span>
                                    Recordarme
                                </label>
                                <span class="intranet-note">Solo personal autorizado</span>
                            </div>

                            <button type="submit" class="btn-primary">
                                <span>Ingresar</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>

                        <form class="auth-form" id="mfaFormElement" method="POST" action="{{ route('intranet.login.mfa') }}" hidden>
                            @csrf

                            <div class="form-header">
                                <h3>Verificacion MFA</h3>
                                <p id="mfaMessageText">Ingresa el codigo de 6 digitos enviado a tu correo.</p>
                            </div>

                            <div class="input-group">
                                <div class="input-wrapper">
                                    <i class="fas fa-key input-icon"></i>
                                    <input
                                        type="text"
                                        id="mfaCode"
                                        name="code"
                                        placeholder="Codigo MFA"
                                        required
                                        inputmode="numeric"
                                        pattern="[0-9]{6}"
                                        maxlength="6"
                                        autocomplete="one-time-code">
                                </div>
                            </div>

                            <button type="submit" class="btn-primary">
                                <span>Verificar codigo</span>
                                <i class="fas fa-shield-check"></i>
                            </button>
                        </form>

                        <div class="form-footer intranet-footer-link">
                            <p>
                                <a href="{{ route('home') }}" class="switch-form">
                                    <i class="fas fa-arrow-left"></i> Volver al portal de clientes
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-container"></div>

    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
