<aside class="sidebar">
    <!-- Información del usuario -->
    <div class="user-info">
        <div class="user-avatar">J</div>
        <div class="user-details">
            <div class="user-name"><?= $_SESSION['user_name'] ?? 'Invitado' ?></div>
            <div class="user-role"><?= $_SESSION['user_rol'] ?? 'Sin rol' ?></div>
        </div>
    </div>
    <!-- Menú -->
    <nav class="sidebar-menu">
        <ul>
            <!-- Dashboard -->
            <li class="<?php echo ($current_page == 'dashboard') ? 'active' : '' ?>">
                <a href="/dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <!-- Cargos -->
            <li class="<?php echo ($current_page == 'cargos') ? 'active' : '' ?>">
                <a href="/cargos">
                    <i class="fas fa-sitemap"></i>
                    <span>Cargos</span>
                </a>
            </li>
            <!-- Empleados -->
            <li class="<?php echo ($current_page == 'empleados') ? 'active' : '' ?>">
                <a href="/empleados">
                    <i class="fas fa-user-tie"></i>
                    <span>Empleados</span>
                </a>
            </li>
            <!-- Asistencias -->
            <li class="<?php echo ($current_page == 'asistencias') ? 'active' : '' ?>">
                <a href="/asistencias">
                    <i class="fas fa-user-check"></i>
                    <span>Asistencias</span>
                </a>
            </li>
            <!-- Usuarios -->
            <li class="<?php echo ($current_page == 'usuarios') ? 'active' : '' ?>">
                <a href="/usuarios">
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            <!-- Empresa -->
            <li class="<?php echo ($current_page == 'empresa') ? 'active' : '' ?>">
                <a href="/empresa/configuracion">
                    <i class="fas fa-building"></i>
                    <span>Empresa</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>