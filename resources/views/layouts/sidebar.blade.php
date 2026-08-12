<!-- ========== App Menu ========== -->
<style>
    .navbar-menu .navbar-nav .nav-link:hover{
        font-weight: unset !important;
        text-decoration: underline;
    }
</style>
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="/index" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="50">
            </span>
        </a>
        <a href="/index" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="50">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar" style="background: #f95a02;">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">{{ __('t-menu') }}</span></li>

                <li class="nav-item">
                    <a href="/" class="nav-link menu-link">
                        <i class="ri-home-5-line"></i> Inicio
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#reportesside" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="reportesside">
                        <i class="bi bi-speedometer2"></i> <span data-key="t-products">Reportes</span>
                    </a>
                    <div class="collapse menu-dropdown" id="reportesside">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="/resumenVentas" class="nav-link">
                                    <i class="bi bi-graph-up me-2"></i> Resumen de ventas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/reporte/instpagobs" class="nav-link">
                                    <i class="bi bi-credit-card me-2"></i> Rep. Inst Pago Bs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/reporte/instpagodolares" class="nav-link">
                                    <i class="bi bi-currency-dollar me-2"></i> Rep. Inst Pago Dolares
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/ventas/productos/sucursales" class="nav-link">
                                    <i class="bi bi-bar-chart-steps me-2"></i> Kilos/Unds Vendidas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/ventas/resultado" class="nav-link">
                                    <i class="bi bi-pie-chart me-2"></i> Resultado General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a  href="{{ route('mermas.sucursales') }}" class="nav-link">
                                    <i class="bi bi-trash3 me-2"></i> Mermas por sucursal
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/operaciones/productos/sucursales" class="nav-link">
                                    <i class="bi bi-arrow-left-right me-2"></i> Operaciones por sucursal
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarProducts" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarProducts">
                        <i class="bi bi-box-seam"></i> <span data-key="t-products">Inventario</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarProducts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="/productos" class="nav-link">
                                    <i class="bi bi-grid-3x3-gap-fill me-2"></i> Productos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/instancias" class="nav-link">
                                    <i class="bi bi-folder2-open me-2"></i> Categorias
                                </a>
                            </li>
                            <li class="nav-item d-none">
                                <a href="/existencias" class="nav-link">
                                    <i class="bi bi-database me-2"></i> Inventarios Mcia
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/inventario/historico" class="nav-link">
                                    <i class="bi bi-calendar-check me-2"></i> Panel de Inventarios
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarClientes" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarClientes">
                        <i class="bi bi-person-bounding-box"></i> <span data-key="t-orders">Clientes</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarClientes">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="/clientes" class="nav-link">
                                    <i class="bi bi-people me-2"></i> Listado Clientes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/cxc" class="nav-link">
                                    <i class="bi bi-receipt me-2"></i> Cuentas x Cobrar
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="/instpago">
                        <i class="bi bi-cash-coin"></i> Inst de Pago
                    </a>
                </li>

            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>
<div class="vertical-overlay"></div>
