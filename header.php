<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Velký_mlýn
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="/wp-content/themes/velkymlyn/slick/slick.css"/>
	<link rel="stylesheet" type="text/css" href="/wp-content/themes/velkymlyn/slick/slick-theme.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
<link rel="manifest" href="/site.webmanifest" />

<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<header id="masthead" class="site-header">
    <div class="container custom-container">
        <div class="d-flex align-items-end justify-content-between">
            <div class="site-branding">
                <a href="/">
                    <img src="/wp-content/themes/velkymlyn/image/logo_298.png" class="img-fluid logo" alt="Velký Mlýn Logo">
                </a>
            </div>

            <!-- Navigation -->
				<nav id="site-navigation" class="main-navigation d-none d-lg-block">
					<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
						<?php esc_html_e( 'Primary Menu', 'velkymlyn' ); ?>
					</button>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'menu-1',
							'menu_id'        => 'primary-menu',
						)
					);
					?>
				</nav>

            <!-- Mobile menu button -->
            <button class="btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </div>
	<!-- Offcanvas Mobile Menu -->
	<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
		<div class="offcanvas-header">
			<h5 class="offcanvas-title" id="mobileMenuLabel"><?php esc_html_e( 'Menu', 'velkymlyn' ); ?></h5>
			<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Zavřít"></i>
</button>
		</div>
		<div class="offcanvas-body">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					'menu_id'        => 'mobile-menu',
					'menu_class'     => 'nav flex-column',
				)
			);
			?>
		</div>
	</div>
</header>
<script>
document.addEventListener('DOMContentLoaded', function() {
	const offcanvasMenu = document.getElementById('mobileMenu');
	const menuLinks = offcanvasMenu.querySelectorAll('a');
	menuLinks.forEach(link => {
		link.addEventListener('click', function() {
			const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasMenu);
			if (offcanvas) offcanvas.hide();
		});
	});
});
</script>