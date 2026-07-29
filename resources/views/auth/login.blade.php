@extends('layouts.master-auth')
@section('title') Iniciar Sesión - Capochi @endsection

@section('content')
    <style>
        :root {
            --color-primary: #f95a02;
            --color-secondary: #75a373;
            --color-tertiary: #0f6a0a;
            --color-background: #faebd7;
        }
        body{
            background-color: #faebd7 !important;
        }
        .text-secondary{
            color: #e14e01 !important;
        }

        .backdrop-blur {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1) !important;
            transition: all 0.3s ease;
        }

        .backdrop-blur:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15) !important;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* Badges de sucursales */
        .badge.bg-white\/20 {
            background: rgba(255, 255, 255, 0.2) !important;
            font-weight: normal;
            transition: all 0.3s ease;
        }

        .badge.bg-white\/20:hover {
            background: rgba(255, 255, 255, 0.3) !important;
            transform: scale(1.05);
        }
    </style>

    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <!-- Columna izquierda - Formulario -->
            <div class="col-lg-4 col-md-6 d-flex align-items-center justify-content-center"   >
                <div class="w-100" style="max-width: 380px; padding: 2rem;">
                    <!-- Logo -->
                    <div class="text-center mb-5">
                        <img src="{{ URL::asset('build/images/logo-dark.png') }}" style=" filter: brightness(0) saturate(100%) invert(35%) sepia(15%) saturate(500%) hue-rotate(50deg);" alt="Capochi" height="60" class="mb-3">
                        <h2 class="fw-bold mb-1" style="color: #567954;">¡Bienvenido!</h2>
                        <p class="text-muted" style="color: #567954">Inicia sesión para continuar</p>
                    </div>

                    <!-- Mensajes de error -->
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="background: linear-gradient(135deg, var(--color-primary), #d94a00); color: white;">
                            <div class="d-flex align-items-center">
                                <i class="ri-error-warning-line fs-4 me-2"></i>
                                <div>
                                    <strong>Error de autenticación</strong><br>
                                    {{ $errors->first() }}
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Formulario -->
                    <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                        @csrf

                        <!-- Campo Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold text-secondary">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px; border-color: #e0e0e0;">
                                    <i class="ri-mail-line" style="color: var(--color-primary);"></i>
                                </span>
                                <input type="email"
                                       class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="correo@ejemplo.com"
                                       required
                                       autofocus
                                       style="border-radius: 0 10px 10px 0; padding-left: 10px !important; border-color: #e0e0e0;">
                            </div>
                            @error('email')
                            <div class="invalid-feedback d-block">
                                <i class="ri-information-line me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Campo Contraseña -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="password" class="form-label fw-semibold text-secondary">Contraseña</label>

                            </div>
                            <div class="input-group position-relative">
                                <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px; border-color: #e0e0e0;">
                                    <i class="ri-lock-line" style="color: var(--color-primary);"></i>
                                </span>
                                <input type="password"
                                       class="form-control border-start-0 ps-0 password-input @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="••••••••"
                                       required
                                       style="border-radius: 0 10px 10px 0; padding-left: 10px !important; border-color: #e0e0e0;">
                                <button class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted password-addon"
                                        type="button"
                                        style="z-index: 10; text-decoration: none; right: 10px !important;"
                                        onclick="togglePassword()">
                                    <i class="ri-eye-line" id="togglePasswordIcon" style="color: var(--color-secondary);"></i>
                                </button>
                            </div>
                            @error('password')
                            <div class="invalid-feedback d-block">
                                <i class="ri-information-line me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Recordar sesión -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                                style="border-color: var(--color-primary);">
                                <label class="form-check-label text-secondary" for="remember">
                                    Recordar mi sesión
                                </label>
                            </div>
                        </div>

                        <!-- Botón de inicio -->
                        <button type="submit" class="btn w-100 py-3 mb-4 fw-semibold text-white border-0"
                                style="border-radius: 10px; background: linear-gradient(135deg, var(--color-primary), #d94a00); box-shadow: 0 10px 20px rgba(249, 90, 2, 0.2);">
                            <span class="d-flex align-items-center justify-content-center">
                                <i class="ri-login-circle-line me-2 fs-5"></i>
                                Iniciar Sesión
                            </span>
                        </button>

                        <!-- Separador -->
                        <div class="position-relative text-center mb-4">
                            <hr class="text-muted opacity-25">
                            <span class="position-absolute top-50 start-50 translate-middle px-3 small" style="background-color: #f3f6f9; color: var(--color-tertiary);">
                                o
                            </span>
                        </div>


                    </form>

                    <!-- Footer -->
                    <div class="text-center mt-5">
                        <p class="small mb-0" style="color: var(--color-tertiary);">
                            <i class="ri-copyright-line align-middle me-1"></i>
                            {{ date('Y') }} Capochi. Todos los derechos reservados.
                        </p>
                        <p class="small" style="color: var(--color-tertiary);">
                            Desarrollado por <a href="https://CelisWeb.com.ve" target="_blank" style="color: var(--color-primary); text-decoration: none;">CelisWeb</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Columna derecha - Hero/Branding -->
            <!-- Columna derecha - Hero/Branding -->
            <div class="col-lg-8 col-md-6 d-none d-md-block"
                 style="background: linear-gradient(135deg, #75a373 0%, #567954 100%)">
                <div class="h-100 d-flex align-items-center justify-content-center p-5">
                    <div class="text-center text-white" style="max-width: 700px;">

                        <!-- Logo o ícono principal -->
                        <div class="mb-4">
                            <img src="/build/images/logo-light.png" width="150px" />
                        </div>

                        <!-- Título y eslogan -->
                        <h1 class="display-5 fw-bold mb-3 d-none" >Capochi C.A.</h1>
                        <p class="lead mb-4" style="font-size: 1.3rem; opacity: 0.95;">
                            Carne, Pollo, Cochino y Embutidos de la mejor calidad
                        </p>

                        <!-- Tarjetas de productos/servicios -->
                        <div class="row g-4 mt-4">
                            <!-- Producto 1: Carnes -->
                            <div class="col-6 col-md-4">
                                <div class="card bg-white/10 border-0 backdrop-blur p-3 rounded-4">
                                    <div class="card-body text-center p-2">
                                        <div class="rounded-circle bg-white/20 p-3 d-inline-flex mb-2">
                                            <i class="mdi mdi-cow" style="font-size: 2rem; color: white;"></i>
                                        </div>
                                        <h5 class="text-white mb-1">Carnes</h5>
                                        <p class="text-white/80 small mb-0">Res, cerdo <br> y pollo</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Producto 2: Embutidos -->
                            <div class="col-6 col-md-4">
                                <div class="card bg-white/10 border-0 backdrop-blur p-3 rounded-4">
                                    <div class="card-body text-center p-2">
                                        <div class="rounded-circle bg-white/20 p-3 d-inline-flex mb-2">
                                            <i class="mdi mdi-cheese" style="font-size: 2rem; color: white;"></i>
                                        </div>
                                        <h5 class="text-white mb-1">Embutidos</h5>
                                        <p class="text-white/80 small mb-0">Salchichas, <br>  chorizos</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Producto 3: Víveres -->
                            <div class="col-6 col-md-4">
                                <div class="card bg-white/10 border-0 backdrop-blur p-3 rounded-4">
                                    <div class="card-body text-center p-2">
                                        <div class="rounded-circle bg-white/20 p-3 d-inline-flex mb-2">
                                            <i class="ri-shopping-basket-line" style="font-size: 2rem; color: white;"></i>
                                        </div>
                                        <h5 class="text-white mb-1">Víveres</h5>
                                        <p class="text-white/80 small mb-0">Alimentos Seleccionados</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sucursales destacadas -->
                        <div class="mt-5 pt-3">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                                <i class="ri-map-pin-line"></i>
                                <h6 class="text-white mb-0">16 sucursales a tu servicio</h6>
                            </div>

                            <!-- Grid de sucursales (ejemplo con algunas) -->
                            <div class="row g-2 justify-content-center">
                                <div class="col-auto">
                        <span class="badge bg-white/20 text-white px-3 py-2 rounded-pill">
                            <i class="ri-store-line me-1"></i> Mercado Municipal
                        </span>
                                </div>
                                <div class="col-auto">
                        <span class="badge bg-white/20 text-white px-3 py-2 rounded-pill">
                            <i class="ri-store-line me-1"></i> AV Carabobo
                        </span>
                                </div>
                                <div class="col-auto">
                        <span class="badge bg-white/20 text-white px-3 py-2 rounded-pill">
                            <i class="ri-store-line me-1"></i> Av Caracas
                        </span>
                                </div>
                                <div class="col-auto">
                        <span class="badge bg-white/20 text-white px-3 py-2 rounded-pill">
                            <i class="ri-store-line me-1"></i> Biruaca
                        </span>
                                </div>
                                <div class="col-auto">
                        <span class="badge bg-white/20 text-white px-3 py-2 rounded-pill">
                            <i class="ri-store-line me-1"></i> +12 más
                        </span>
                                </div>
                            </div>

                            <!-- Distribución a nivel nacional -->
                            <div class="mt-4 d-flex align-items-center justify-content-center gap-3">
                                <div class="d-flex align-items-center">
                                    <i class="ri-truck-line fs-4 me-2"></i>
                                    <span class="small">Distribución propia</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="ri-time-line fs-4 me-2"></i>
                                    <span class="small">Delivery Lunes-Domingo</span>
                                </div>
                            </div>
                        </div>

                        <!-- Mensaje de calidad -->
                        <div class="mt-5 pt-2 border-top d-none border-white/20">
                            <p class="small text-white/80 mb-0">
                                <i class="ri-star-fill me-1"></i>
                                Calidad y frescura garantizada desde nuestras granjas a tu mesa
                                <i class="ri-star-fill ms-1"></i>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para funcionalidades -->
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-off-line');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('ri-eye-off-line');
                icon.classList.add('ri-eye-line');
            }
        }

        // Fill demo credentials
        function fillDemoCredentials() {
            document.getElementById('email').value = 'demo@capochi.com';
            document.getElementById('password').value = 'Capochi123';

            // Animación simple
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="ri-check-line me-2"></i>Credenciales cargadas';
            btn.disabled = true;

            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 2000);
        }

        // Validación del formulario
        (function() {
            'use strict';

            const forms = document.querySelectorAll('.needs-validation');

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>

    <!-- Estilos adicionales -->
    <style>
        /* Variables de color */
        :root {
            --color-primary: #f95a02;
            --color-secondary: #75a373;
            --color-tertiary: #0f6a0a;
            --color-background: #faebd7;
        }

        /* Animaciones */
        .btn-primary {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(249, 90, 2, 0.3) !important;
        }

        .form-control {
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(249, 90, 2, 0.1);
            border-color: var(--color-primary) !important;
        }

        .input-group-text {
            transition: all 0.3s ease;
        }

        .form-control:focus + .input-group-text {
            border-color: var(--color-primary) !important;
        }

        /* Loading animation */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        /* Responsive */
        @media (max-width: 767.98px) {
            .col-lg-4 {
                padding: 2rem 1rem;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--color-background);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--color-primary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--color-secondary);
        }

        /* Glassmorphism effects */
        .rounded-3 {
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .rounded-3:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1) !important;
        }

        /* Checkbox personalizado */
        .form-check-input:checked {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(249, 90, 2, 0.25);
            border-color: var(--color-primary);
        }

        /* Alert personalizado */
        .alert-danger {
            border: none;
            position: relative;
            overflow: hidden;
        }

        .alert-danger::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: rgba(255, 255, 255, 0.3);
        }

        /* Fade-in animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bg-white, [class*="col-"] {
            animation: fadeIn 0.6s ease-out;
        }

        /* Enlaces */
        a:hover {
            color: var(--color-primary) !important;
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/pages/password-addon.init.js') }}"></script>
@endsection
