<?php
if (!defined('WPINC')) {
    die;
}
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
require_once plugin_dir_path(__FILE__) . '../includes/gateways/bpm/payment.php';
/**
 * Create Table
 */
if (!class_exists('Cpm_Trx_Table')) :
    class Cpm_Trx_Table extends WP_List_Table
    {

        /**
         * Get a list of columns.
         *
         * @return array
         */
        public function get_columns()
        {
            return array(
                'display_name'      => wp_strip_all_tags(__('Fullname', 'cpm')),
                'trx_amount'      => wp_strip_all_tags(__('Amount', 'cpm')),
                'saleOrderId'   => wp_strip_all_tags(__('Sale Order ID', 'cpm')),
                'trx_datetime'   => wp_strip_all_tags(__('Date and Time', 'cpm')),
                'trx_resCode'   => wp_strip_all_tags(__('Transaction Status', 'cpm')),
                'trx_saleReferenceId'   => wp_strip_all_tags(__('Sale Reference ID', 'cpm')),
            );
        }

        /**
         * Prepares the list of items for displaying.
         */
        public function prepare_items()
        {
            $columns  = $this->get_columns();
            $hidden   = array();
            $sortable = array();
            $primary  = 'display_name';
            $this->_column_headers = array($columns, $hidden, $sortable, $primary);
        }

        /**
         * Generates content for a single row of the table.
         * 
         * @param object $item The current item.
         * @param string $column_name The current column name.
         */
        protected function column_default($item, $column_name)
        {
            switch ($column_name) {
                case 'display_name':
                    return esc_html($item['display_name']);
                case 'trx_amount':
                    return esc_html($item['trx_amount']);
                case 'saleOrderId':
                    return esc_html($item['saleOrderId']);
                case 'trx_datetime':
                    return esc_html($item['trx_datetime']);
                case 'trx_resCode':
                    return esc_html(Cpm_BPM::get_error_message($item['trx_resCode']));
                case 'trx_saleReferenceId':
                    return esc_html($item['trx_saleReferenceId']);
                default:
                    return 'Unknown';
            }
        }
        public function no_items()
        {
            _e('No Transactions avaliable.', 'cpm');
        }
        /**
         * Generates custom table navigation to prevent conflicting nonces.
         * 
         * @param string $which The location of the bulk actions: 'top' or 'bottom'.
         */
        protected function display_tablenav($which)
        {
?>
            <div class="tablenav <?php echo esc_attr($which); ?>">

                <div class="alignright actions bulkactions">
                    <?php $this->bulk_actions($which); ?>
                </div>
                <?php
                $this->extra_tablenav($which);
                $this->pagination($which);

                ?>

                <br class="clear" />
            </div>
<?php
        }

        /**
         * Generates content for a single row of the table.
         *
         * @param object $item The current item.
         */
        public function single_row($item)
        {
            echo '<tr>';
            $this->single_row_columns($item);
            echo '</tr>';
        }
    }
endif;
