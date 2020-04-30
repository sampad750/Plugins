<?php
/**
 * Plugin Name:       Word Count
 * Plugin URI:        https://sampadinfo.com/plugins/word-count/
 * Description:       Handle word count of blog post text  and reading time with this plugin.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sampad Debnath
 * Author URI:        https://sampadinfo.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       word-count
 * Domain Path:       /languages
 **/
/*function wordcount_activation_hook(){
}
register_activation_hook(__FILE__, 'wordcount_activation_hook');

function wordcount_deactivation_hook(){
}
register_deactivation_hook(__FILE__, 'wordcount_deactivation_hook');*/

function wordcount_load_textdomain(){
    load_plugin_textdomain('word-count', false, dirname(__FILE__) . '/languages');
}
add_action('plugins_loaded', 'wordcount_load_textdomain');

// wordcount_count_word
function wordcount_count_word($content){
    $stripped = strip_tags($content); // strip_tags html এর tag remove করে।
    $wordn = str_word_count($stripped); // শুধু text কতটা word আছে তা count করবে number/symbol না।
    $label = __('Total number of words', 'word-count');

    // apply_filters দিয়ে tag তৈরি করা হয় যা দিয়ে add_filter করে callback function মাধ্যমে modify করা যায় ।
    $label = apply_filters('wordcount_head', $label);
    /* wordcount_head এই filter কে modify করতে হলে theme এর function.php file এ নিচের code টি লিখতে হবে
        function wc_head_content( $text ){
            //$text = 'Show post content number'; // এটা ও করা যায়
            $text = strtoupper($text);
            return $text;
        }
        add_filter('wordcount_head', 'wc_head_content');*/

    $tag = apply_filters('wordcount_tag', 'h2');
    /* wordcount_tag এই filter কে modify করতে হলে theme এর function.php file এ নিচের code টি লিখতে হবে
        function wc_change_tag($tag){
            $tag = 'h1';
            return $tag;
        }
    add_filter('wordcount_tag', 'wc_change_tag');*/

    $content .= sprintf( '<%s> %s: %s</%s>', $tag, $label, $wordn, $tag ); // sprintf : Return a formatted string

    return $content;
}
add_filter('the_content', 'wordcount_count_word');

// wordcount_reading_time
function wordcount_reading_time($time){
    $stripped = strip_tags($time);
    $wordn = str_word_count($stripped);

    $reading_minute = ceil( $wordn/200 ); // ceil মানে ৩.৪ হলে ৪ count করবে
    $reading_secounds = floor( $wordn%200 / (200/60) ); // floor মানে ৩.৪ হলে ৩ count করবে

    $is_visible = apply_filters( 'wc_reading_time_visible' , 1 );

    if($is_visible){
        $label = __( 'Total Reading Time', 'word-count' );

        $label = apply_filters('wc_reading_time_heading', $label);
        /* wc_reading_time_heading এই filter কে modify করতে হলে theme এর function.php file এ নিচের code টি লিখতে হবে
         function wc_reading_text($reading_text){
            $reading_text = 'Count Reading Time';
            return $reading_text;
        }
        add_filter('wc_reading_time_heading', 'wc_reading_text');*/

        $tag = apply_filters( 'wc_reading_tag', 'h4');
        /* wc_reading_tag এই filter কে modify করতে হলে theme এর function.php file এ নিচের code টি লিখতে হবে
        function wc_reading_tag($reading_tag){
            $reading_tag = 'h2';
            return $reading_tag;
        }
        add_filter('wc_reading_tag', 'wc_reading_tag');*/

        $time .= sprintf( '<%s> %s: %s minute %s secound</%s>', $tag, $label, $reading_minute, $reading_secounds, $wordn, $tag );
    }

    return $time;
}
add_filter('the_content', 'wordcount_reading_time');


