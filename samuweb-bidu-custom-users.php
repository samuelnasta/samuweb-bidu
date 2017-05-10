<?php
// Show extra fields in admin user page
add_action('show_user_profile', 'samuweb_bidu_user_extra_fields');
add_action('edit_user_profile', 'samuweb_bidu_user_extra_fields');

function samuweb_bidu_user_extra_fields($user) {
?>
    <table class="form-table">
        <tr>
            <th><label for="address">Endereço</label></th>
            <td><input type="text" name="address" value="<?php echo esc_attr(get_the_author_meta('address', $user->ID)); ?>" class="regular-text" /></td>
        </tr>

        <tr>
            <th><label for="city">Cidade</label></th>
            <td><input type="text" name="city" value="<?php echo esc_attr(get_the_author_meta('city', $user->ID)); ?>" class="regular-text" /></td>
        </tr>

        <tr>
            <th><label for="province">Estado</label></th>
            <td><input type="text" name="province" value="<?php echo esc_attr(get_the_author_meta('province', $user->ID)); ?>" class="regular-text" /></td>
        </tr>

        <tr>
            <th><label for="phone">Telefone</label></th>
            <td><input type="text" name="phone" value="<?php echo esc_attr(get_the_author_meta('phone', $user->ID)); ?>" class="regular-text" /></td>
        </tr>

        <tr>
            <th><label for="cpf">CPF / CNPJ</label></th>
            <td><input type="text" name="cpf" value="<?php echo esc_attr(get_the_author_meta('cpf', $user->ID)); ?>" class="regular-text" /></td>
        </tr>
    </table>
<?php
}


// Save user extra fields
add_action('personal_options_update', 'samuweb_bidu_user_save_extra_fields');
add_action('edit_user_profile_update', 'samuweb_bidu_user_save_extra_fields');

function samuweb_bidu_user_save_extra_fields($user_id) {
    if ( !current_user_can('edit_user', $user_id))
        return false;

    update_usermeta($user_id, 'address', $_POST['address']);
    update_usermeta($user_id, 'city', $_POST['city']);
    update_usermeta($user_id, 'province', $_POST['province']);
    update_usermeta($user_id, 'phone', $_POST['phone']);
    update_usermeta($user_id, 'cpf', $_POST['cpf']);
}



// Remove via CSS all unused fields
add_action('admin_head-user-edit.php', 'samuweb_bidu_user_remove_unused_fields');
add_action('admin_head-profile.php',  'samuweb_bidu_user_remove_unused_fields');
function samuweb_bidu_user_remove_unused_fields() {
?>
<style>
.form-table:nth-of-type(1), h2:nth-of-type(1), h2:nth-of-type(4), h2:nth-of-type(5), tr.user-display-name-wrap, tr.user-description-wrap, tr.user-url-wrap {
    display: none;
}
</style>
<?php
}
