<?php
/**
 * Plugin Name:       Table Data
 * Plugin URI:        https://sampadinfo.com/plugins/table-data/
 * Description:       Handle table data of wordpress admin post/page etc table.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sampad Debnath
 * Author URI:        https://sampadinfo.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tdata
 * Domain Path:       /languages
 */


require_once "class-person-table.php";

function load_text_domain()
{
    load_plugin_textdomain('tdata', false, dirname(__FILE__) . "/language");
}
add_action('plugins_loaded', 'load_text_domain');

function tdata_admin_page()
{
    add_menu_page(
        __('Table Data', 'tdata'),
        __('Table Data', 'tdata'),
        'manage_options',
        'table_data',
        'tdata_display',
        'dashicons-editor-table',
        26
    );
}

function tdata_display()
{
    include_once "dataset.php";

    // Name এবং Age column কে sortable করার জন্য url থেকে orderby এবং order এর value আনা।
    $order_by = $_REQUEST['orderby'] ?? '';
    $order = $_REQUEST['order'] ?? '';

    // column Name কে search করে খুজা।
    if (isset($_REQUEST['s']) && !empty($_REQUEST['s'])) {
        // array_filter হল store array থেকে callback function থেকে যে array আসে তা মিলিয়ে প্রিন্ট করে।
        $data = array_filter($data, 'tdata_search_by_name');
    }
    // column Gender কে search করে খুজা।
    if (isset($_REQUEST['filter_s']) && !empty($_REQUEST['filter_s'])) {
        $data = array_filter($data, 'tdata_filter_sex');
    }
    // Name এবং Age column কে sortable করা।
    if( 'data_age' == $order_by ){
        if('asc' == $order){
            usort($data, function($item1, $item2){
                return $item1['age']<=>$item2['age'];
            });
        }else{
            usort($data, function($item1, $item2){
                return $item2['age']<=>$item1['age'];
            });
        }
    }elseif ('data_name' == $order_by){
        if('asc' == $order){
            usort($data, function($item1, $item2){
                return $item1['name']<=>$item2['name'];
            });
        }else{
            usort($data, function($item1, $item2){
                return $item2['name']<=>$item1['name'];
            });
        }
    }

    $table = new personTable();
    $table->set_data($data);
    $table->prepare_items();
    ?>
    <div class="wrap">
        <h1><?php _e('Person Info', 'tdata'); ?></h1>
        <form method="GET">
            <?php
            $table->search_box('Search', 'search_box');
            $table->display();
            ?>
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>">
        </form>
    </div>
    <?php
}
// return করবে Name কে যাকে আমি search box এ search করেছি।
function tdata_search_by_name($item)
{
    $name = strtolower($item['name']); // Make a string lowercase
    $search_name = sanitize_text_field($_REQUEST['s']);
    // strpos $name sting এর value সাথে search করা value মিল থাকলে $item দিয়ে name data টা দেখাও।
    if (strpos($name, $search_name) !== false) {
        return true;
    }
    return false;
}
// return করবে option value like M/F কে যাকে আমি filter box এ select করেছি।
function tdata_filter_sex($item){
    $sex = $_REQUEST['filter_s'] ?? 'all';
    if('all' == $sex){
        return true;
    }else{
        if($sex == $item['sex']){
            return true;
        }
    }
    return false;
}

add_action('admin_menu', 'tdata_admin_page');

