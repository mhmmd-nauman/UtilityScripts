<?php 
/**
 * Add new register fields for WooCommerce registration.
 *
 * @return string Register fields HTML.
 */
function cs_wc_extra_register_fields() {
    ?>
    <p class="form-row form-row-wide">
        <label for="reg_billing_first_name"><?php _e( 'CNPJ', 'textdomain' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_cnpj" id="reg_billing_cnpj" value="<?php if ( ! empty( $_POST['billing_cnpj'] ) ) esc_attr_e( $_POST['billing_cnpj'] ); ?>" />
    </p>
    <?php
}
 
add_action( 'woocommerce_register_form_start', 'cs_wc_extra_register_fields' );
 
/**
 * Validate the extra register fields.
 *
 * @param  string $username          Current username.
 * @param  string $email             Current email.
 * @param  object $validation_errors WP_Error object.
 *
 * @return void
 */
function cs_wc_validate_extra_register_fields( $username, $email, $validation_errors ) {
    if ( isset( $_POST['billing_cnpj'] ) && empty( $_POST['billing_cnpj'] ) ) {
        $validation_errors->add( 'billing_cnpj_error', __( '<strong>Erro</strong>: Digite o seu CNPJ.', 'textdomain' ) );
    }
    if (!preg_match("/^(\d{2}\.?\d{3}\.?\d{3}\/?\d{4}-?\d{2})$/",$_POST['billing_cnpj'])) {
        $validation_errors->add( 'billing_cnpj_error', __( '<strong>Erro</strong>: Digite o seu CNPJ.', 'textdomain' ) );
    }
}
 
add_action( 'woocommerce_register_post', 'cs_wc_validate_extra_register_fields', 10, 3 );
 
/**
 * Save the extra register fields.
 *
 * @param  int  $customer_id Current customer ID.
 *
 * @return void
 */
function cs_wc_save_extra_register_fields( $customer_id ) {
    if ( isset( $_POST['billing_cnpj'] ) ) {
        // WordPress default first name field.
        update_user_meta( $customer_id, 'billing_cnpj', sanitize_text_field( $_POST['billing_cnpj'] ) );
        $userdata = array();
        $userdata['ID'] = $customer_id;
        $userdata['role'] = 'pending_vendor';
        wp_update_user($userdata);
        // WooCommerce billing first name. vendor
        //update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
    }
    
}
 
add_action( 'woocommerce_created_customer', 'cs_wc_save_extra_register_fields' );

function new_modify_user_table( $column ) {
    $column['billing_cnpj'] = 'CNPJ';
    return $column;
}
add_filter( 'manage_users_columns', 'new_modify_user_table' );

function new_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'billing_cnpj' :
            return get_the_author_meta( 'billing_cnpj', $user_id );
            break;
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );
