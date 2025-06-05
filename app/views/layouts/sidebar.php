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
                    <i class="fas fa-user-tie"></i>
                    <span>Cargos</span>
                </a>
            </li>
            <!-- Estudiantes -->
            <li class="<?php echo ($current_page == 'students') ? 'active' : '' ?>">
                <a href="/students">
                    <i class="fas fa-user-graduate"></i>
                    <span>Estudiantes</span>
                </a>
            </li>
            <!-- Materias -->
            <li class="<?php echo ($current_page == 'subjects') ? 'active' : '' ?>">
                <a href="/subjects">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Materias</span>
                </a>
            </li>
            <!-- Libros -->
            <li class="<?php echo ($current_page == 'books') ? 'active' : '' ?>">
                <a href="/books">
                    <i class="fas fa-book"></i>
                    <span>Libros</span>
                </a>
            </li>
            <!-- Autores -->
            <li class="<?php echo ($current_page == 'authors') ? 'active' : '' ?>">
                <a href="/authors">
                    <i class="fas fa-feather-alt"></i>
                    <span>Autores</span>
                </a>
            </li>
            <!-- Editoriales -->
            <li class="<?php echo ($current_page == 'publishers') ? 'active' : '' ?>">
                <a href="/publishers">
                    <i class="fas fa-building"></i>
                    <span>Editoriales</span>
                </a>
            </li>
            <!-- Administración -->
            <li class="<?php echo ($current_page == 'administracion') ? 'active' : '' ?>">
                <a href="<?php echo URL_ROOT; ?>/administracion">
                    <i class="fas fa-cog"></i>
                    <span>Administración</span>
                </a>
            </li>
            <!-- Reportes -->
            <li class="<?php echo ($current_page == 'reportes') ? 'active' : '' ?>">
                <a href="<?php echo URL_ROOT; ?>/reportes">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reportes</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>