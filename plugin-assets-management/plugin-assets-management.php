<?php
/**
 * Plugin Name:       Plugin Assets Management
 * Plugin URI:        https://sampadinfo.com/plugins/plugin-assets-management/
 * Description:       Handle the plugin assets management with this plugin.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sampad Debnath
 * Author URI:        https://sampadinfo.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       pam
 * Domain Path:       /languages
 */

define('PAM_DIR', plugin_dir_url(__FILE__));
define('PAM_ASSETS_DIR', plugin_dir_url(__FILE__) . 'assets');
define('PAM_ASSETS_ADMIN_DIR', plugin_dir_url(__FILE__) . 'assets/admin');
define('PAM_ASSETS_PUBLIC_DIR', plugin_dir_url(__FILE__) . 'assets/public');

class plugin_assets_management{

    private $version;

    function __construct()
    {
        $this->version = time();

        add_action('plugins_loaded', [$this, 'load_text_domain'] );

        add_action('wp_enqueue_scripts', [$this, 'load_public_assets']);

        add_action('admin_enqueue_scripts', [$this, 'load_admin_assets'], 10 ); // 10 এর যত বেশি হবে তত পরে print হবে।

        //add_action('init', 'overwrite_assets'); // কোন css/js কে overwrite করতে
    }

    function load_text_domain(){
        load_plugin_textdomain('pam', false, PAM_DIR . "/language");
    }

/*    function overwrite_assets(){ // কোন css/js কে overwrite করতে
        wp_deregister_style('handle'); // css/js handle
        wp_register_style('handle', 'link/url'); // css/js handle আর url
    }*/

    function load_public_assets(){
        // dependency array('jquery','new-handel') মানে jquery,new-handel এই দুইটা Handle js আগে enqueue হবে তারপর এটা enqueue হবে।
        $js_files = [
          'main-js' => [
              'path' => PAM_ASSETS_PUBLIC_DIR . "/js/main.js",
              'dep' => ['jquery'],
          ]
        ];
        foreach ($js_files as $handle=>$js_file) {
            wp_enqueue_script($handle, $js_file['path'], $js_file['dep'], $this->version, true);
        }

        // php এর জন্য inline css লেখা যাবে না। এই ভাবে লিখতে হবে।
/*        $attachment_image_src = wp_get_attachment_image_src(207, 'media');
        $data = <<<EOD
            .class_name{
                background-image: url{$attachment_image_src[0]};
            }
EOD;
        wp_add_inline_style('handle', $data); // কোন file এ অ্যাড হবে তার handle*/


        // php এর ডাটা javascript পাঠাতে হলে
        $data = array( // php data
            'name' => 'Sampad Debnath',
            'link' => 'http://www.sampadinfo.com'
        );
        $moredata = array( // php data
            'name' => 'Ankit Debnath',
            'link' => 'http://www.ankit.com'
        );
        wp_localize_script('main-js', 'add_php_data_to_js', $data); // js code: add_php_data_to_js.name echo: Sampad Debnath
        wp_localize_script('main-js', 'add_php_more_data_to_js', $moredata);

    }

    function load_admin_assets($screen){
        $_screen = get_current_screen(); // edit.php?post_type=page মান all page এর link এ দেখাতে চাইলে।
        if( 'edit.php' == $screen && $_screen->post_type == 'page' ) { // এই js file টা admin কোন page এ দেখাবে তার condition
            wp_enqueue_script('admin-main-js', PAM_ASSETS_ADMIN_DIR . "/js/admin-main.js", array('jquery'), $this->version, true);
        }
    }
}
new plugin_assets_management;