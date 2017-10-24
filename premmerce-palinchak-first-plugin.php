<?php
/**
 * Plugin Name: Palinchak
 * Plugin URI: http://site-2.xyz/
 * Description: Palinchak Test plugin
 * Version:  1.0
 * Author: Palinchak
 * Author URI: http://site-2.xyz/
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  domain-name
 * Domain Path:  /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


register_activation_hook(__FILE__, function () {

});
register_deactivation_hook(__FILE__, function () {

});
register_uninstall_hook(__FILE__, '');


add_action('admin_menu', function () {


    add_menu_page('Title Plagin', 'Plagin palinchak menu item1', 'manage_options', 'plugin-slug-pal', function () {
//        delete_option('chek_key');
        if(!get_option('chek_key')){
            add_option('chek_key', ['first_check' => 'on'], '', 'yes');
        }

//        delete_option('select_key');
        if(!get_option('select_key')){
            add_option('select_key', ['No'=>'selected', 'YES'=>''], '', 'yes');
        }
        if(!get_option('some_input')){
            add_option('some_input', 'some-input text', '', 'yes');
        }

        mod_settings_init();
    }
        , 'dashicons-dashboard', 1);

//    add_action( 'admin_init', 'mod_settings_init' );


    add_submenu_page('plugin-slug-pal', 'sub Title Plagin', 'subMenu', 'manage_options', 'sub_menu_pal', function () {
        echo '<h1>My plugin sub page</h1>';

//        sub_menu_api_settings();
//        add_action( 'admin_init', 'sub_menu_api_settings' );
        sub_menu_api_settings();
    });
});

function sub_menu_api_settings(){

    register_setting('prem_palinchak', 'sub_menu_pal');
    $options = get_option('chek_key');

    add_settings_section(
        'some_settings_section',
        'TITLE some_settings_section',
        function(){
            echo '<p>Check some data</p>';
        },
       'sub_menu_pal'
    );

    add_settings_field(
        'check_first',
        'Lable for check_first',
        'renderCheckboxHtml',
        'sub_menu_pal',
        'some_settings_section',
        [
            'label_text' => 'Add perfect style to your site',
            'label_for'  => 'chek_key',
            'value'      => isset($options) ?$options: null,
        ]
    );

// Перевірка прав доступу
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Перевірити чи користувач засабмітив налаштування
    // Wordpress додає "settings-updated" до $_GET
    if ( isset( $_GET['settings-updated'] ) ) {
        // Додати повідомлення "updated"
        add_settings_error( 'premmerce_messages', 'premmerce_message', 'Settings Saved', 'updated' );
    }

    // Показати повідомлення повідомлення error/update
    settings_errors( 'premmerce_messages' );


    ?>   <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php"  method="post">
            <?php
            // Поля для безпеки для зареєстрованих налаштувань
            settings_fields( 'sub_menu_pal' );

            // Вивід секцій налаштувань та їх полів
            do_settings_sections( 'sub_menu_pal' );
            do_settings_fields('sub_menu_pal', 'sub_menu_pal');

            // Кнопка збереження
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
    <?php


}
function renderCheckboxHtml($args){
    ?>
    <label for='<?php echo $args['label_for'] ?>'>
    <input type='checkbox' name='<?php echo $args['label_for'] ?>' <?php echo checked($args['value'], 'on') ?>/>
    <?php echo $args['label_text'] ?>
    </label><?php
}
function mod_settings_init()
{
    $data = $_POST;
    if (isset($data['submitted']) && isset($data['first_check'])) {
        update_option('chek_key', isset($data['first_check']) ? $data['first_check'] : []);
    }elseif(isset($data['submitted']) && !isset($data['first_check'])){
        update_option('chek_key', '');
    }

    if (isset($data['submitted']) && isset($data['first_selct'])) {
        if(!empty(get_option('select_key'))){
            foreach(get_option('select_key') as $val=>$select){
                if($val==$data['first_selct']){
                    $new_set[$val]=$data['first_selct'];
                }else{
                    $new_set[$val]='';
                }
            }
        }
        update_option('select_key', $new_set);
    }
    if (isset($data['submitted']) && isset($data['some_input'])) {

        update_option('some_input', isset($data['some_input']) ? $data['some_input'] : []);
    }


    echo '<h1>My plugin  page</h1>';
    $arg_check = get_option('chek_key');
    $args_select = get_option('select_key');
    $args_input = get_option('some_input');

    ?>
    <form method="post">
        <input type="hidden" value="1" name="submitted">
        <div class="wrap">
            <h1>Mod options</h1>
            <table class="form-table">
                <tbody>
                <tr>
                    <th>option1</th>
                    <td>
                        <label>
                            <input type="checkbox"
                                   name="first_check" <?php echo checked($arg_check, 'on') ?> >
                            <?php _e('Some text') ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>option</th>
                    <td>
                        <label>
                            <select name="first_selct">
                                <?php foreach ($args_select as $key => $val) { ?>
                                    <option value="<?php echo $key ?>" <?php selected($args_select[$key], $key ); ?>  >
                                        <?php echo $key ?>
                                    </option>

                                <?php } ?>
                                <?php _e('Some text') ?>
                            </select>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th>Input</th>
                    <td>
                        <label>
                            <input name="some_input" value="<?=$args_input?>"/>
                                <?php _e('Description input') ?>
                        </label>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
        <?php submit_button() ?>
    </form>
    <?php
}
