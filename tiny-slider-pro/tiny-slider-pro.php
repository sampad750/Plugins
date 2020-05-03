<?php
/**
 * Plugin Name:       Tiny Slider Pro
 * Plugin URI:        https://sampadinfo.com/plugins/tiny-slider-pro/
 * Description:       Tiny Slider.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sampad Debnath
 * Author URI:        https://sampadinfo.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tiny-slider-pro
 * Domain Path:       /languages
 **/

function tiny_slider_load_textdomain()
{
    load_plugin_textdomain('tiny-slider-pro', false, dirname(__FILE__) . "/languages");
}
add_action('plugins_loaded', 'tiny_slider_load_textdomain');




function tiny_slider_init(){
    add_image_size( 'slide_img_size', 800, 600, true );
}
add_action('init', 'tiny_slider_init');




function tiny_slider_assets(){
    wp_enqueue_style('tiny-slider-css', '//cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.8.4/tiny-slider.css', null, '1.0');
    wp_enqueue_script('tiny-slider-js', '//cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.8.4/min/tiny-slider.js', null, '1.0', true);
    wp_enqueue_script( 'tiny-slider-main-js', plugin_dir_url(__FILE__)."/assets/js/main.js", array('jquery'), '1.0', true );
}
add_action('wp_enqueue_scripts', 'tiny_slider_assets');


function tiny_slider_shortcode_tslider($args, $content){
    $defaults = [
        'height' => 600,
        'width' => 800,
        'id' => ''
    ];
    $args = shortcode_atts( $defaults, $args );
    $content = do_shortcode($content);

    $shortcode_output = <<<EOD
    <div id="{$args['id']}" style="height: {$args['height']}; width: {$args['width']}">
    <div class="slider">
    {$content}
</div>
    </div>
EOD;

    return $shortcode_output;

}
add_shortcode('tslider', 'tiny_slider_shortcode_tslider');

function tiny_slider_shortcode_tslide($args){
    $defaults = [
        'caption' => '',
        'id' => '',
        'size' => 'slide_img_size'
    ];
    $args = shortcode_atts( $defaults, $args );
    $img_src = wp_get_attachment_image_src( $args['id'], $args['size'] );

    $shortcodeoutput = <<<EOD
<div class="slide">
<p><img src="{$img_src[0]}" alt="{$args['caption']}"></p>
<p>{$args['caption']}</p>
</div>
EOD;

return $shortcodeoutput;
}
add_shortcode('tslide', 'tiny_slider_shortcode_tslide');

/*shortcode
==================
[tslider][tslide caption="caption one" id="17"/] [tslide caption="caption one" id="16"/]  [tslide caption="caption one" id="15"/] [/tslider]*/