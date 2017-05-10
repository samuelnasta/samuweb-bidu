<?php
// Create meta boxes to show bid management in auction posts
add_action('add_meta_boxes', 'samuweb_bidu_create_bid_meta_box');
add_action('add_meta_boxes', 'samuweb_bidu_list_bid_meta_box');

function samuweb_bidu_create_bid_meta_box() {
    add_meta_box(
        'samuweb_bidu_create_bid_meta_box',
        'Adicionar lance',
        'samuweb_bidu_create_bid',
        'leiloes',
        'advanced',
        'high'
    );
}


function samuweb_bidu_list_bid_meta_box() {
    add_meta_box(
        'samuweb_bidu_list_bid_meta_box',
        'Ver lances para este leilão',
        'samuweb_bidu_list_bids',
        'leiloes',
        'advanced',
        'high'
    );
}





function samuweb_bidu_list_bids($post) {
?>
    <div class="wrap">
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . "samuweb_bids";
        $auction_id = $post->ID;

        $rows = $wpdb->get_results("
            SELECT * from $table_name
            WHERE auction_id = $auction_id
            ORDER BY product_id DESC");
        ?>
        <table class='wp-list-table widefat fixed striped posts'>
            <tr>
                <th><strong>Horário do lance</strong></th>
                <th><strong>Nome cavalo</strong></th>
                <th><strong>Nome usuário</strong></th>
                <th><strong>Cidade</strong></th>
                <th><strong>Valor do lance</strong></th>
                <th>&nbsp;</th>
            </tr>
            <?php foreach($rows as $row):

        if($row->user_id):
            $user_info = get_userdata($row->user_id);
            $user_name = $user_info->user_login;
            $city = get_the_author_meta('city', $row->user_id);
            $province = get_the_author_meta('province', $row->user_id);
        else:
            $user_name = $row->user_name;
            $city = $row->city;
            $province = $row->province;
        endif;

 ?>
                <tr data-row="<?php echo $row->id; ?>">
                    <td><?php echo $row->bidded_at; ?></td>
                    <td>
                    <?php
                        $post_product = get_post($row->product_id);
                        echo $post_product->post_title;
                    ?></td>
                    <td><?php echo $user_name; ?></td>
                    <td><?php echo $city; ?> (<?php echo strtoupper($province); ?>)</td>
                    <td><?php echo $row->value; ?></td>
                    <td><a class="delete-bid" data-id="<?php echo $row->id; ?>" href="javascript:;">Deletar</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

<script>
jQuery('.delete-bid').on('click', function(){
    $this = jQuery(this);
    if(confirm('Deseja excluir esse lance?')) {
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                action: 'delete_bid',
                id: jQuery(this).data('id')
            }
        }).done(function(response) {
            if(response.code == 1) {
                $this.parent().parent().fadeOut('slow');
            } else {
                window.alert('Ocorreu um erro e não podemos excluir esse lance.');
            }
        });
    }
});
</script>
<?php
}






function samuweb_bidu_create_bid($post) {
?>
    <div class="wrap">
        <?php if(isset($message)): ?>
            <div class="updated"><p><?php echo $message; ?></p></div>
        <?php endif; ?>

        <input type="hidden" id="auction-id" name="auction-id" value="<?php echo $post->ID; ?>">
        <table>
            <tr>
                <th>Nome do cavalo</th>
                <td>
                <?php
                $auction_id = $post->ID;
                $posts = get_posts(array(
                    'posts_per_page'	=> -1,
                    'post_type'			=> 'cavalos'
                ));

                if($posts): ?>
                    <select id="product-id" name="product-id">

                        <?php foreach($posts as $post): setup_postdata($post);
                        if(get_field('belongs_to', $post->ID)->ID == $auction_id):?>
                            <option value="<?php echo $post->ID; ?>"><?php echo $post->post_title; ?></option>
                        <?php endif;
                        endforeach; ?>

                    </select>
                    <?php wp_reset_postdata(); ?>

                <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Nome do usuário</th>
                <td><input type="text" id="user-name" name="user-name" /></td>
            </tr>
            <tr>
                <th>Cidade</th>
                <td><input type="text" id="city" name="city" /></td>
            </tr>
            <tr>
                <th>Estado</th>
                <td><input type="text" id="province" name="province" /></td>
            </tr>
            <tr>
                <th>Valor do lance</th>
                <td><input type="text" id="value" name="value" /></td>
            </tr>
        </table>
        <input class="button" type="submit" id="add-bid" name="add-bid" value="Salvar">
    </div>

<script>
jQuery('body').on('click', '#add-bid', function(event){
    event.preventDefault();
    jQuery. ajax({
        type: "POST",
        url: ajaxurl,
        data: {
            action: 'add_bid',
            auction_id: jQuery('#auction-id').val(),
            product_id: jQuery('#product-id').val(),
            user_name: jQuery('#user-name').val(),
            city: jQuery('#city').val(),
            province: jQuery('#province').val(),
            value: jQuery('#value').val(),
        }
    }).done(function(response) {
        if(response.code == 1) {
            window.alert('Lance registrado');
            window.location.reload(true);
        } else {
            window.alert('Ocorreu um erro e não podemos gravar esse lance.');
        }
    });
});
</script>
    <?php
}





function samuweb_bidu_create_bid_page() {
?>
    <div class="wrap">
        <?php settings_errors(); ?>

        <h1>Adicionar lance</h1>

        <table>
            <tr>
                <th>Nome do leilão</th>
                <td>
                <?php
                $auction_id = $post->ID;
                $posts = get_posts(array(
                    'posts_per_page'	=> -1,
                    'post_type'			=> 'leiloes'
                ));

                if($posts): ?>
                    <select id="auction-id" name="auction-id">
                        <option disabled selected value="">Selecione</option>
                        <?php foreach($posts as $post): setup_postdata($post);
                        if(get_field('belongs_to', $post->ID)->ID == $auction_id):?>
                            <option value="<?php echo $post->ID; ?>"><?php echo $post->post_title; ?></option>
                        <?php endif;
                        endforeach; ?>

                    </select>
                    <?php wp_reset_postdata(); ?>

                <?php endif; ?>
                </td>
            </tr>


            <tr>
                <th>Nome do cavalo</th>
                <td>
                    <select id="product-id" name="product-id">
                        <option disabled selected value=""></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Nome do usuário</th>
                <td><input type="text" id="user-name" name="user-name" /></td>
            </tr>
            <tr>
                <th>Cidade</th>
                <td><input type="text" id="city" name="city" /></td>
            </tr>
            <tr>
                <th>Estado</th>
                <td><input type="text" id="province" name="province" /></td>
            </tr>
            <tr>
                <th>Valor do lance</th>
                <td><input type="text" id="value" name="value" /></td>
            </tr>
        </table>
        <input class="button" type="submit" id="add-bid" name="add-bid" value="Salvar">
    </div>

<script>
jQuery('body').on('change','#auction-id',function(){
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
            action: 'get_cavalos_select',
            auction_id: jQuery('#auction-id option:selected').val()
        }
    }).done(function(result) {
        jQuery('#product-id').prop('disabled',false).html(result).focus();
    });
});



jQuery('body').on('click', '#add-bid', function(event){
    event.preventDefault();
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
            action: 'add_bid',
            auction_id: jQuery('#auction-id').val(),
            product_id: jQuery('#product-id').val(),
            user_name: jQuery('#user-name').val(),
            city: jQuery('#city').val(),
            province: jQuery('#province').val(),
            value: jQuery('#value').val(),
        }
    }).done(function(response) {
        if(response.code == 1) {
            window.alert('Lance registrado');
            window.location.reload(true);
        } else {
            window.alert('Ocorreu um erro e não podemos gravar esse lance.');
        }
    });
});
</script>
    <?php
}





// AJAX bid functions
add_action('wp_ajax_add_bid', 'samuweb_bidu_add_bid');
add_action('wp_ajax_delete_bid', 'samuweb_bidu_delete_bid');
add_action('wp_ajax_get_cavalos_select', 'samuweb_bidu_get_cavalos_select');

function samuweb_bidu_add_bid(){
    global $wpdb;
    $table_name = $wpdb->prefix . "samuweb_bids";

    $user_id = get_current_user_id();
    $auction_id = $_POST['auction_id'];
    $product_id = $_POST['product_id'];
    $user_name = $_POST['user_name'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $value = $_POST['value'];

    $data = array(
                'user_id' => $user_id,
                'auction_id' => $auction_id,
                'product_id' => $product_id,
                'user_name' => $user_name,
                'city' => $city,
                'province' => $province,
                'value' => $value,
            );

    $result = $wpdb->insert(
            $table_name,
            $data,
            array('%d', '%d', '%d', '%s', '%s', '%s', '%s')
    );
    if($result > 0) $response['code'] = 1;


    header('Content-Type: application/json');
    echo json_encode($response);

    exit();
}



function samuweb_bidu_delete_bid(){
    global $wpdb;
    $table_name = $wpdb->prefix . "samuweb_bids";
    $id = $_POST['id'];

    if(!empty($id)){
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %s", $id));
        $response['code'] = 1;
    }

    header('Content-Type: application/json');
    echo json_encode($response);

    exit();
}



function samuweb_bidu_get_cavalos_select(){
    $posts = get_posts(array(
        'posts_per_page'=> -1,
        'post_type'		=> 'cavalos'
    ));

    $options = '<option disabled selected value="">Selecione</option>';
    if($posts):
        foreach($posts as $post): setup_postdata($post);
            if(get_field('belongs_to', $post->ID)->ID == $_POST['auction_id']):
                $options .= sprintf('<option value="%d">%s</option>', $post->ID, $post->post_title);
            endif;
        endforeach;
        wp_reset_postdata();
    endif;
    echo $options;
    exit();
}




// Generate <li>s of the last bids
function samuweb_bidu_last_bids($product_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . "samuweb_bids";

    $rows = $wpdb->get_results("SELECT user_id, city, province, value FROM $table_name WHERE product_id = $product_id ORDER BY value DESC LIMIT 5");

    foreach($rows as $row):
        if(user_can($row->user_id, 'administrator')):
            $city = $row->city;
            $province = $row->province;
        else:
            $city = get_the_author_meta('city', $row->user_id);
            $province = get_the_author_meta('province', $row->user_id);
        endif; ?>
            <li><?php echo sprintf('%s (%s) <span class="bid-price">R$ %d,00</span>', $city, $province, $row->value); ?></li>
    <?php endforeach;
}



// Get highest bid
function samuweb_bidu_highest_bids($product_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . "samuweb_bids";

    $row = $wpdb->get_row("SELECT value FROM $table_name
                WHERE product_id = $product_id
                ORDER BY value DESC limit 1");

    return $row->value;
}







add_action('admin_notices', 'samuweb_bidu_admin_notice');
function samuweb_bidu_admin_notice() {
	
	$screen = get_current_screen();
	
	if ($screen->id === 'toplevel_page_samuweb_bidu_create_bid') {

		if (isset($_GET['reset-options'])) {
			
			if ($_GET['reset-options'] === 'true') : ?>
				
				<div class="notice notice-success is-dismissible">
					<p><?php _e('Default settings restored.', 'bbb'); ?></p>
				</div>
				
			<?php else : ?>
				
				<div class="notice notice-error is-dismissible">
					<p><?php _e('No changes made.', 'bbb'); ?></p>
				</div>
				
			<?php endif;
			
		}
	}
}