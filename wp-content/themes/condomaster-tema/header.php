<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header id="main-header">
    <div class="container">
        <div class="logo">
            <a href="<?php echo home_url(); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="Logo">
            </a>
        </div>

        <nav id="main-nav">
            <?php if (is_user_logged_in()) : ?>
                <?php 
                // Obtener el rol del usuario actual
                $user = wp_get_current_user();
                $roles = (array) $user->roles;
                
                if (in_array('administrator', $roles)) {
                    // Mostrar menú para administradores
                    wp_nav_menu(array('theme_location' => 'menu_administrador'));
                } elseif (in_array('supervisor_seguridad', $roles)) {
                    // Mostrar menú para supervisores de seguridad
                    wp_nav_menu(array('theme_location' => 'menu_supervisor'));
                } elseif (in_array('guardia_seguridad', $roles)) {
                    // Mostrar menú para guardias de seguridad
                    wp_nav_menu(array('theme_location' => 'menu_guardia'));
                } elseif (in_array('residente_propietario', $roles)) {
                    // Mostrar menú para residentes propietarios
                    wp_nav_menu(array('theme_location' => 'menu_propietario'));
                } elseif (in_array('residente_arrendatario', $roles)) {
                    // Mostrar menú para residentes arrendatarios
                    wp_nav_menu(array('theme_location' => 'menu_arrendatario'));
                } elseif (in_array('residente_huesped', $roles)) {
                    // Mostrar menú para residentes huéspedes
                    wp_nav_menu(array('theme_location' => 'menu_huesped'));
                } else {
                    // Mostrar un menú general o una redirección si el usuario no tiene un rol válido
                    wp_nav_menu(array('theme_location' => 'menu_predeterminado'));
                }
                ?>
            <?php else : ?>
                <!-- Menú para visitantes no logueados -->
                <?php wp_nav_menu(array('theme_location' => 'menu_visitante')); ?>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main id="content">
