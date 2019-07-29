<?php
// create custom plugin settings menu
add_action('admin_menu', 'my_cool_plugin_create_menu');

function my_cool_plugin_create_menu() {

    //create new top-level menu
    add_menu_page('Atualizador de produtos', 'Atualizador', 'administrator', __FILE__, 'my_cool_plugin_settings_page' , plugins_url('/images/icon.png', __FILE__) );
}

function my_cool_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1>Atualizador de Produtos - Configurações</h1>

        <form method="post" action="options.php">

            <?php
            settings_fields( 'section' );
            do_settings_sections( 'theme-options' );

            submit_button();
            ?>

        </form>

    </div>
    <?php

}

function display_api_login_element() {
    ?>
    <input type="text" name="api_login" id="api_login" value="<?php echo get_option('api_login'); ?>" />
    <?php

}

function display_api_password_element() {
    ?>
    <input type="text" name="api_password" id="api_password" value="<?php echo get_option('api_password'); ?>" />
    <?php

}

function display_theme_panel_fields()
{
    add_settings_section("section", "Autenticação de API do WooCommerce", null, "theme-options");

    add_settings_field("api_login", "Api login key", "display_api_login_element", "theme-options", "section");
    add_settings_field("api_password", "Api password key", "display_api_password_element", "theme-options", "section");

    register_setting("section", "api_login");
    register_setting("section", "api_password");
}


add_action("admin_init", "display_theme_panel_fields");