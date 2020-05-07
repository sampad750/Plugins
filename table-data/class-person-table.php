<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class personTable extends WP_List_Table
{
    private $_items;

    // set_data হল কোথায় থেকে data show হবে। mysql database হলে সেখান থেকে data আনতে হবে।
    function set_data($data)
    {
        $this->_items = $data;

    }

    // table data তে columns set করার method এই method name change করা যাবে না।
    function get_columns()
    {
        return [
            'cb' => '<input type="checkbox">',
            'name' => __('Name', 'tdata'),
            'sex' => __('Gender', 'tdata'),
            'email' => __('Email', 'tdata'),
            'age' => __('Age', 'tdata')
        ];
    }
    // column কে sortable করার জন্য
    function get_sortable_columns()
    {
        return [
            'age' => ['data_age', true], // এখানে age হল column key, আর data_age হল তার id
            'name' => ['data_name', true]
        ];
    }

// এখানে column_cb মানে _cb হল get_columns method এর return একটা key যাতে আমরা কিছু অ্যাড করতে পারি।
// $item['id'] id usr করা হয়েছে যে data return করা হয়েছে তা ক্রমানয়ে 12345 id চলে আসে।
    function column_cb($item)
    {
        return "<input type='checkbox' value='{$item['id']}' />";
    }

    /*    function column_email($item) // email column এ কোন কিছু অ্যাড করতে চাইলে।
        {
            return "<strong>{$item['email']}</strong>";
        }*/
    // column এর content filter করে দেখানোর জন্য
    function extra_tablenav($which)
    {
        if ('top' == $which):
            ?>
            <div class="actions alignleft">
                <select name="filter_s" id="filter_s">
                    <option value="all">All</option>
                    <option value="M">Males</option>
                    <option value="F">Females</option>
                </select>
                <?php submit_button(__('Filter', 'tdata'), 'primary', 'submit', false); ?>
            </div>
        <?php
        endif;
    }
    // full table টায় কি কি show হবে তা prepare করে
    function prepare_items()
    {
        $this->_column_headers = array(
            $this->get_columns(),
            array(), // add array for sortable
            $this->get_sortable_columns() // add for sortable
        );

        $paged = $_REQUEST['paged'] ?? 1;
        $per_page = 3;
        $total_items = count($this->_items);
        $data_chunks = array_chunk($this->_items, $per_page);
        $this->items = $data_chunks[$paged - 1];

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => 2,
            'total_pages' => ceil($total_items / $per_page)
        ]);
    }

// $item হল data এর প্রত্যেকটা array আর $column_name হল array এর key যা column এ ডাটা দেখাবে।
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

}