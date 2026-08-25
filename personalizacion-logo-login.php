<?php
/*
Plugin Name: Estándar Personalización Login
Description: Plugin universal para Branding del Login Inicial de WordPress, con selector de imagen de logo desde el Personalizador.
Version: 3.0
Author: José Luis Melferber
*/

if ( ! defined( 'ABSPATH' ) ) exit;

// 1. CONFIGURACIÓN DEL PERSONALIZADOR
add_action( 'customize_register', function( $wp_customize ) {
    $wp_customize->add_section( 'seccion_login_personalizado', array(
        'title'    => 'Login Personalizado',
        'priority' => 30,
    ));

    $wp_customize->add_setting( 'logo_login_personalizado', array(
        'transport' => 'refresh',
    ));

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'logo_login_personalizado', array(
        'label'    => 'Selecciona el Logo para el Login',
        'section'  => 'seccion_login_personalizado',
    )));
});

// 2. LOGICA DE VISTA PREVIA (INYECTAR INTERFAZ DE LOGIN)
add_action( 'wp_head', function() {
    // Solo actuamos si estamos dentro del Personalizador
    if ( is_customize_preview() ) {
        $logo_url = get_theme_mod( 'logo_login_personalizado' );
        if ( ! $logo_url ) {
            $logo_url = plugin_dir_url( __FILE__ ) . 'logo-login.png';
        }
        ?>
        <style type="text/css">
            /* Ocultamos el sitio web para simular el login */
            body.customize-support #page, body.customize-support #wrapper { display: none !important; }
            body.customize-support {
                background: #f1f1f1 !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
            }
            /* Dibujamos la caja de login falsa para previsualizar */
            .preview-login-box {
                background: white;
                padding: 26px 24px 34px;
                font-weight: 400;
                box-shadow: 0 1px 3px rgba(0,0,0,.13);
                width: 320px;
                text-align: center;
            }
            .preview-logo {
                background-image: url('<?php echo esc_url($logo_url); ?>');
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center bottom;
                height: 100px;
                width: 100%;
                margin-bottom: 25px;
                display: block;
            }
            .preview-fields {
                border: 1px solid #ddd;
                height: 40px;
                margin-bottom: 16px;
                background: #fbfbfb;
            }
            .preview-button {
                background: #2271b1;
                height: 30px;
                width: 80px;
                float: right;
                border-radius: 3px;
            }
        </style>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var loginMarkup = '<div class="preview-login-box">' +
                                  '<div class="preview-logo"></div>' +
                                  '<div class="preview-fields"></div>' +
                                  '<div class="preview-fields"></div>' +
                                  '<div class="preview-button"></div>' +
                                  '</div>';
                document.body.innerHTML = loginMarkup;
            });
        </script>
        <?php
    }
});

// 3. CARGA REAL EN LA PÁGINA DE ACCESO (Lo que ve el usuario final)
function login_real_assets() { 
    $logo_url = get_theme_mod( 'logo_login_personalizado' );
    if ( ! $logo_url ) {
        $logo_url = plugin_dir_url( __FILE__ ) . 'logo-login.png';
    }
    ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url('<?php echo esc_url( $logo_url ); ?>') !important; 
            height: 100px;
            width: 100%;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center bottom;
            padding-bottom: 25px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'login_real_assets' );

// 4. SEGURIDAD Y LIMPIEZA (Tus estándares de siempre)
add_filter( 'login_headerurl', function() { return home_url(); });
add_filter( 'login_display_language_dropdown', '__return_false' );
add_filter( 'login_errors', function() { return 'Error: Credenciales incorrectas.'; });