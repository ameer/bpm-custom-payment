<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://ameer.ir
 * @since      1.0.0
 *
 * @package    Cpm
 * @subpackage Cpm/admin/partials
 */
// If this file is called directly, abort.
if (!defined('WPINC')) die;
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap" id="cpm-admin-app">
    <h2><?php _e('Custom Payment Plugin Options', 'cpm') ?></h2>

    <form method="post" name="<?php echo $this->plugin_name; ?>" action="options.php">
        <?php
        //Grab all options
        $options = get_option($this->plugin_name);
        $terminal_id = (isset($options['terminal_id']) && !empty($options['terminal_id'])) ? esc_attr($options['terminal_id']) : '';
        $username = (isset($options['username']) && !empty($options['username'])) ? esc_attr($options['username']) : '';
        $password = (isset($options['password']) && !empty($options['password'])) ? esc_attr($options['password']) : '';
        $edtaj = ( isset( $options['edtaj'] ) && ! empty( $options['edtaj'] ) ) ? 1 : 0;
        $formal_rate_percent = (isset($options['formal_rate_percent']) && !empty($options['formal_rate_percent'])) ? esc_attr($options['formal_rate_percent']) : '';
        $formal_account_id = (isset($options['formal_account_id']) && !empty($options['formal_account_id'])) ? esc_attr($options['formal_account_id']) : '';
        $informal_account_id = (isset($options['informal_account_id']) && !empty($options['informal_account_id'])) ? esc_attr($options['informal_account_id']) : '';

        settings_fields($this->plugin_name);
        do_settings_sections($this->plugin_name);

        ?>
        <fieldset>
            <p><?php esc_attr_e('Insert merchant ID that received from behpardakht', 'cpm'); ?></p>
            <legend class="screen-reader-text">
                <span><?php esc_attr_e('Insert merchant ID that received from behpardakht', 'cpm'); ?></span>
            </legend>
            <input type="text" class="cpm-terminal_id" id="<?php echo $this->plugin_name; ?>-terminal_id" name="<?php echo $this->plugin_name; ?>[terminal_id]" value="<?php if (!empty($terminal_id)) echo $terminal_id; else echo ''; ?>" />
        </fieldset>
        <fieldset>
            <p><?php esc_attr_e('Enter gateway username', 'cpm'); ?></p>
            <legend class="screen-reader-text">
                <span><?php esc_attr_e('Enter gateway username', 'cpm'); ?></span>
            </legend>
            <input type="text" class="cpm-username" id="<?php echo $this->plugin_name; ?>-username" name="<?php echo $this->plugin_name; ?>[username]" value="<?php if (!empty($username)) echo $username; else echo ''; ?>" />
        </fieldset>
        <fieldset>
            <p><?php esc_attr_e('Enter gateway password', 'cpm'); ?></p>
            <legend class="screen-reader-text">
                <span><?php esc_attr_e('Enter gateway password', 'cpm'); ?></span>
            </legend>
            <input type="password" class="cpm-password" id="<?php echo $this->plugin_name; ?>-password" name="<?php echo $this->plugin_name; ?>[password]" value="<?php if (!empty($password)) echo $password; else echo ''; ?>" />
        </fieldset>
        <fieldset>
            <p><?php esc_attr_e('Interest Rates', 'cpm'); ?></p>
            <legend class="screen-reader-text">
                <span><?php esc_attr_e('Interest Rates', 'cpm'); ?></span>
            </legend>
            <input type="text" class="cpm-formal_rate_percent" id="<?php echo $this->plugin_name; ?>-formal_rate_percent" name="<?php echo $this->plugin_name; ?>[formal_rate_percent]" value="<?php if (!empty($formal_rate_percent)) echo $formal_rate_percent; else echo ''; ?>" />
        </fieldset>
        <fieldset>
            <p><?php esc_attr_e('Formal Account ID', 'cpm'); ?></p>
            <legend class="screen-reader-text">
                <span><?php esc_attr_e('Formal Account ID', 'cpm'); ?></span>
            </legend>
            <input type="text" class="cpm-formal_account_id" id="<?php echo $this->plugin_name; ?>-formal_account_id" name="<?php echo $this->plugin_name; ?>[formal_account_id]" value="<?php if (!empty($formal_account_id)) echo $formal_account_id; else echo ''; ?>" />
        </fieldset>
        <fieldset>
            <p><?php esc_attr_e('Informal Account ID', 'cpm'); ?></p>
            <legend class="screen-reader-text">
                <span><?php esc_attr_e('Informal Account ID', 'cpm'); ?></span>
            </legend>
            <input type="text" class="cpm-informal_account_id" id="<?php echo $this->plugin_name; ?>-informal_account_id" name="<?php echo $this->plugin_name; ?>[informal_account_id]" value="<?php if (!empty($informal_account_id)) echo $informal_account_id; else echo ''; ?>" />
        </fieldset>
        <fieldset>
            <p><?php esc_attr_e('Enable Edtaj', 'cpm'); ?></p>
            <legend class="screen-reader-text">
                <span><?php esc_attr_e('Enable Edtaj', 'cpm'); ?></span>
            </legend>
            <label for="<?php echo $this->plugin_name; ?>-edtaj">
            <input type="checkbox" id="<?php echo $this->plugin_name; ?>-edtaj" name="<?php echo $this->plugin_name; ?>[edtaj]" value="1" <?php checked( $edtaj, 1 ); ?> />
        </label>
        </fieldset>
        <?php submit_button(__('Save all changes', 'cpm'), 'primary', 'submit', TRUE); ?>
    </form>
</div>