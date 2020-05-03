<?php
/**
 * Plugin Name:       Posts To QR Code
 * Plugin URI:        https://sampadinfo.com/plugins/posts-to-qrcode/
 * Description:       Display QR code under every post.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sampad Debnath
 * Author URI:        https://sampadinfo.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       post-qrcode
 * Domain Path:       /languages
 **/

$pqrc_countries = [
    __('America','post-qrcode'),
    __('Bangladesh','post-qrcode'),
    __('India','post-qrcode'),
    __('Naygeria','post-qrcode'),
    __('Oman','post-qrcode'),
    __('Pakistan','post-qrcode')
];


function pqrc_init(){
    global $pqrc_countries;
    $pqrc_countries = apply_filters('pqrc_countries', $pqrc_countries);
}
add_action('init', 'pqrc_init');

/*function add_pqrc_checkbox_countries($countries){
    // add country Honkong
    array_push($countries, __('Honkong', 'post-qrcode'));
    // $countries = array_diff($countries, array('Naygeria')); delete Naygeria
    return $countries;
}
add_filter('pqrc_countries', 'add_pqrc_checkbox_countries');*/

function post_qr_code_load_textdomain()
{
    load_plugin_textdomain('post-qrcode', false, dirname(__FILE__) . '/languages');
}

add_action('plugins_loaded', 'post_qr_code_load_textdomain');


function pqrc_display_post_qr_code($content)
{
    $current_post_id = get_the_ID();
    $current_post_title = get_the_title($current_post_id);
    $current_post_url = urlencode(get_the_permalink($current_post_id));
    $current_post_type = get_post_type($current_post_id);

    //Post Type Check
    $excluded_post_types = apply_filters('pqrc_excluded_post_types', []);
    if (in_array($current_post_type, $excluded_post_types)) { // in_array = ২য় array value এর মধ্যে ১ম array value মিল আছে কি না।
        // দুইটা array value post মিল আছে তাই শুধু $content return করবে।
        return $content;
    }
    /* pqrc_excluded_post_types এই filter কে modify করতে হলে theme এর function.php file এ নিচের code টি লিখতে হবে
        function select_post_type( $post_type ){
            $post_type[] = 'post';
            return $post_type;
        }
        add_filter('pqrc_excluded_post_types', 'select_post_type');
    */

    // Dimension Hook

    // from theme settings
    $height = get_option('pqrc_height') ? get_option('pqrc_height') : '180';
    $width = get_option('pqrc_width') ? get_option('pqrc_width') : '180';

    $dimension = apply_filters('pqrc_qrcode_dimension', "{$height}x{$width}");
    /* pqrc_qrcode_dimension এই filter কে modify করতে হলে theme এর function.php file এ নিচের code টি লিখতে হবে
        function pqrcode_size( $size ){
            $size = '220x220';
            return $size;
        }
        add_filter('pqrc_qrcode_dimension', 'pqrcode_size');
    */

    // Image Attribute



    $image_src = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=%s&ecc=L&qzone=1&data=%s', $dimension, $current_post_url);

    $content .= sprintf('<div class="qr_img"><img src="%s" title="%s"></div>', $image_src, $current_post_title);


    return $content;
}

add_filter('the_content', 'pqrc_display_post_qr_code');

// QR code height এবং width এর option তৈরি করার জন্য admin page এ settings > general দুইটি field অ্যাড করা হল
function pqrc_settings_init(){
    // section এর জন্য
    add_settings_section('pqrc_section', __('Post QR Code', 'post-qrcode'), 'pqrc_section_callback', 'general'); //Add a new section to a settings page.

    // text field এর জন্য
    add_settings_field('pqrc_height', __('QR code height', 'post-qrcode'), 'pqrc_display_height_width', 'general', 'pqrc_section', array('pqrc_height')); // Add a new field to a section of a settings page.
    add_settings_field('pqrc_width', __('QR code width', 'post-qrcode'), 'pqrc_display_height_width', 'general', 'pqrc_section', array('pqrc_width')); // বার বার callback function না অ্যাড করার জন্য এই array() টা use করলে ই হয়।

    register_setting('general', 'pqrc_height', ['sanitize_callback' => 'esc_attr'] ); // Registers a setting and its data.
    register_setting('general', 'pqrc_width', ['sanitize_callback' => 'esc_attr'] );

    // select field এর জন্য
    add_settings_field('pqrc_select', __('Select Option', 'post-qrcode'), 'pqrc_display_select', 'general', 'pqrc_section');
    register_setting('general', 'pqrc_select', ['sanitize_callback' => 'esc_attr'] );

    // Multiple checkbox field
    add_settings_field('pqrc_checkbox', __('Select Checkbox', 'post-qrcode'), 'pqrc_display_checkbox', 'general', 'pqrc_section');
    register_setting('general', 'pqrc_checkbox');

    // Toggle field
    add_settings_field( 'pqrc_toggle', __( 'Toggle Field', 'post-qrcode' ), 'pqrc_display_toggle_field', 'general', 'pqrc_section' );
    register_setting( 'general', 'pqrc_toggle' );
}
add_action('admin_init', 'pqrc_settings_init');

function pqrc_section_callback(){
    echo "<p>".__('Settings for Posts To QR Code plugin', 'post-qrcode')."</p>";
}
// বার বার callback function না add করে একটা function use করলে ই হয়।
function pqrc_display_height_width($args){
    $option = get_option($args[0]); // এখানে ০ মানে প্রথম array
    printf( "<input type='text' id='%s' name='%s' value='%s'>",$args[0], $args[0], $option );
}

function pqrc_display_select(){
    $id = 'pqrc_select';
    $option = get_option($id);
    global $pqrc_countries;

    printf('<select id="%s" name="%s">', $id, $id);

    foreach($pqrc_countries as $country){
        $selected = '';
        if($option == $country){
            $selected = 'selected';
        }
        printf('<option value="%s" %s>%s</option>', $country, $selected, $country);
    }
    echo "</select>";
}

function pqrc_display_checkbox(){
    $option = get_option('pqrc_checkbox');
    global $pqrc_countries;
    foreach($pqrc_countries as $country){
        $selected = '';
        if( is_array($option) && in_array($country, $option)){
            $selected = 'checked';
        }
        printf('<input type="checkbox" name="pqrc_checkbox[]" value="%s" %s/>%s<br/>', $country, $selected, $country );
    }
}

function pqrc_display_toggle_field() {
    $option = get_option('pqrc_toggle');
    echo '<div id="toggle1"></div>';
    echo "<input type='hidden' name='pqrc_toggle' id='pqrc_toggle' value='".$option."'/>";
}

/*function pqrc_display_height(){
    $id = 'pqrc_height';
    $height = get_option( $id );

    printf( "<input type='text' id='%s' name='%s' value='%s'>",$id, $id, $height ); // Output a formatted string
}

function pqrc_display_width(){
    $id = 'pqrc_width';
    $width = get_option( $id );

    printf( "<input type='text' id='%s' name='%s' value='%s'>",$id, $id, $width );
}*/


function pqrc_assets( $screen ) {
    if ( 'options-general.php' == $screen ) {
        wp_enqueue_style( 'pqrc-minitoggle-css', plugin_dir_url( __FILE__ ) . "/assets/css/minitoggle.css" );
        wp_enqueue_script( 'pqrc-minitoggle-js', plugin_dir_url( __FILE__ ) . "/assets/js/minitoggle.js", array( 'jquery' ), "1.0", true );
        wp_enqueue_script( 'pqrc-main-js', plugin_dir_url( __FILE__ ) . "/assets/js/pqrc-main.js", array( 'jquery' ), time(), true );
    }
}
add_action( 'admin_enqueue_scripts', 'pqrc_assets' );



