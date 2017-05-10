<?php
/**
 * Samuweb Bidu is a bidding system
 *
 * You can setup the auctions, auctioneers and users.
 * Lets you view and edit the bids too.
 *
 * @package SWBidu
 * @author Samuel Nasta
 * @copyright Copyright (C) 2016
 *
 * @wordpress-plugin
 * Plugin Name:       Samuweb Bidu
 * Plugin URI:        http://github.com/samuelnasta/samuweb-bidu
 * Description:       Samuweb Bidu is a bidding system
 * Version:           1.0.0
 * Author:            Samuel Nasta
 * Author URI:        http://www.linkdein.com/in/samuweb
 * Text Domain:       samuweb-bidu
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * Text Domain:       samuweb-bidu
 */

if (!defined('ABSPATH')) {
    die;
}

////////
//
// ADMIN
//
////////

// Create table on plugin activation
global $samuweb_bidu_db_version;
$samuweb_bidu_db_version = '1.0';
register_activation_hook(__FILE__, 'samuweb_bidu_create_table');

function samuweb_bidu_create_table() {
    global $wpdb;
    global $samuweb_bidu_db_version;

    $table_name = $wpdb->prefix . 'samuweb_bids';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        bidded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
        user_id INT(11) NULL,
        user_name VARCHAR(100) NULL,
        city VARCHAR(100) NULL,
        province CHAR(2) NULL,
        auction_id INT(11) NULL,
        product_id INT(11) NOT NULL,
        value INT(11) NOT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    add_option('samuweb_bidu_db_version', $samuweb_bidu_db_version);
}





// Register Script
add_action('wp_enqueue_scripts', 'samuweb_bidu_script');

function samuweb_bidu_script() {
    wp_register_script('samuweb-bidu-script', plugin_dir_url(__FILE__) . 'samuweb-bidu.js', 'jquery', '1.0', true);
    wp_enqueue_script('samuweb-bidu-script');
}



// Register Style
add_action('wp_enqueue_scripts', 'samuweb_bidu_style');

function samuweb_bidu_style() {
    wp_register_style('samuweb-bidu-style', plugin_dir_url(__FILE__) . 'samuweb-bidu.css', false, '1.0');
    wp_enqueue_style('samuweb-bidu-style');
}



// Load CSS on admin pages
function samuweb_bidu_admin_style() {
    wp_register_style('samuweb-bidu-admin-style', plugin_dir_url(__FILE__) . 'samuweb-bidu-admin.css', false, '1.0');
    wp_enqueue_style('samuweb-bidu-admin-style');
}
add_action('admin_enqueue_scripts', 'samuweb_bidu_admin_style');



// Admin page
//add_action('admin_menu', 'samuweb_bidu_admin_actions');
//
//function samuweb_bidu_admin_actions() {
//    add_options_page('Configurações', 'Samuweb Bidu', 'manage_options', 'samuweb-bidu-settings', 'samuweb_bidu_admin_page');
//}
//function samuweb_bidu_admin_page() {
//    include('admin/samuweb-bidu-admin.php');
//}



// Settings
//add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'samuweb_bidu_settings_link');

//function samuweb_bidu_settings_link($links) {
//    $settings_link = '<a href="options-general.php?page=samuweb-bidu-settings">Configurações</a>';
//    array_unshift($links, $settings_link);
//    return $links;
//}



// Register Custom Post Types
require_once(plugin_dir_path(__FILE__) . 'samuweb-bidu-custom-post-types.php');

// Auctions management
require_once(plugin_dir_path(__FILE__) . 'samuweb-bidu-auctions.php');

// Bid management
require_once(plugin_dir_path(__FILE__) . 'samuweb-bidu-bids.php');

// User management
require_once(plugin_dir_path(__FILE__) . 'samuweb-bidu-custom-users.php');






////////////
//
// FUNCTIONS
//
////////////

// (HELPER) Show how many months and years since date of birth
function date_difference($date) {
    $date1 = new DateTime($date);
    $date2 = new DateTime('NOW');
    $interval = $date1->diff($date2);
    $diff_date = '';
    $pluralize_year = function($number){
        return $number > 1 ? 'anos' : 'ano';
    };
    $pluralize_month = function($number){
        return $number > 1 ? 'meses' : 'mês';
    };

    if($interval->y !== 0) { $diff_date = "%y " . $pluralize_year($interval->y); }
    if($interval->y !== 0 && $interval->m !== 0) { $diff_date .= ' e '; }
    if($interval->m !== 0) { $diff_date .= "%m " . $pluralize_month($interval->m); }
    echo $interval->format($diff_date);
}



// (HELPER) Wp_hash on Advanced Custom Field Passwords
add_filter('acf/update_value/type=password', 'encrypt_passwords', 10, 3);

function encrypt_passwords($value, $post_id, $field) {
    $value = wp_hash_password($value);
    return $value;
}


// (HELPER) Display gallery of images
function samuweb_bidu_show_gallery($gallery_id) {
?>
         <div id="<?php echo $gallery_id; ?>" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner" role="listbox">
                <?php
                $photo_count = 0;
                for($i = 1; $i <= 6; $i++):
                    $photo_number = 'photo_' . $i;

                    if(get_field($photo_number)):
                        $photo_count++;?>
                        <li class="item <?php if($photo_count == 1) echo 'active'; ?>">
                            <?php if(get_field('is_sold')) echo '<div class="ribbon"><span>Vendido</span></div>'; ?>
                            <img src="<?php echo get_field($photo_number)['sizes']['large']; ?>">
                        </li>
                    <?php endif;
                endfor; ?>
            </div>
            <?php if($photo_count > 1): ?>
            <a class="left carousel-control" href="#<?php echo $gallery_id; ?>" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Anterior</span>
            </a>
            <a class="right carousel-control" href="#<?php echo $gallery_id; ?>" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Próximo</span>
            </a>
            <?php endif; ?>
        </div>
    <?php
}



// Show last auctions thumbnails in home page
function samuweb_bidu_show_leiloes($how_many, $title, $id, $categories){
    $how_many = (isset($how_many)) ? $how_many : 2; ?>
    <div class="service-center">
    
    <div class="carousel" id="<?php echo $id; ?>" data-ride="carousel" data-interval="5000">
        <div class="carousel-inner">
            <!--<h4><?php //echo $title; ?></h4>-->
            <?php
            $is_first = 1;
            $last_leiloes = new WP_Query(
                array(
                    'orderby' => 'date',
                    'post_type' => array('leiloes', 'post'),
                    'posts_per_page' => $how_many,
                    'cat' => $categories
                )
            );

            while ($last_leiloes->have_posts()):
                $last_leiloes->the_post();
            ?>

            <div class="item <?php
            if($is_first === 1):
                echo 'active';
                $is_first = 0;
            endif; ?>">

                <div class="featured-item col-xs-12">
                    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                        <?php the_post_thumbnail('medium',array('class'=>'img-responsive col-xs-12')); ?>
                    </a>
                </div>
            </div>
        <?php endwhile;
        wp_reset_postdata(); ?>
        </div>
        <a data-slide="prev" href="#<?php echo $id; ?>" class="left carousel-control destaque-slide">‹</a>
        <a data-slide="next" href="#<?php echo $id; ?>" class="right carousel-control destaque-slide">›</a>
    </div>
    </div>
    <?php
}







// Sends email to the user whom bidded now and the user whom made the last bid before him
function send_email($user_id, $product_id, $auction_id, $value, $send_to_others){

    $user_info = get_userdata($user_id);
    $product_title = get_the_title($product_id);
    $auction_title = get_the_title($auction_id);

    if(!user_can($user_id, 'administrator')):
        $to = $user_info->user_email;
        $subject = 'Lance registrado no leilão Marcha News';
        $body = "<h2>Agradecemos pela participação no Leilão $auction_title</h2>
        <h3>Seu lance de R$$value,00 foi registrado com sucesso para o lote $product_title</h3>
        <p>Continue acompanhando nossos leilões!</p>
        <p>Atenciosamente,
        <br>Equipe Marcha News
        <br>http://marchanews.com.br</p>";
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            "From: {$user_info->user_name} <{$user_info->user_email}>",
            "CC: contato@bhwebsite.com.br",
            'Reply-to: No-reply <contato@roxa.com.br>',
        );

        wp_mail($to, $subject, $body, $headers);
    endif;

    
    if($send_to_others):
        global $wpdb;
        $table_name = $wpdb->prefix . "samuweb_bids";

        $rows = $wpdb->get_results("SELECT user_id, city, province, value FROM $table_name WHERE product_id = $product_id ORDER BY value DESC LIMIT 1");
        $row = $rows[0];
        
        if($row):
            $user_info = get_userdata($row->user_id);

            $product_link = get_permalink($product_id);
            $to = $user_info->user_email;
            $subject = 'Lance coberto no leilão Marcha News';
            $body = "<h2>O seu lance foi coberto</h2>
            <h3>Seu lance foi coberto para o lote <a href='$product_link'>$product_title</a> do leilão $auction_title</h3>

            <p>Continue acompanhando nossos leilões!</p>

            <p>Atenciosamente,
            <br>Equipe Marcha News
            <br>http://marchanews.com.br</p>";

            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                "From: {$user_info->user_name} <{$user_info->user_email}>",
                "CC: contato@bhwebsite.com.br",
                'Reply-to: No-reply <contato@roxa.com.br>',
            );

            wp_mail($to, $subject, $body, $headers);
        endif;
    endif;
}



// Custom columns that show in the listing of custom posts
add_filter('manage_leiloes_posts_columns', 'samuweb_bidu_leiloes_columns');
add_action('manage_leiloes_posts_custom_column' , 'samuweb_bidu_leiloes_column_values', 10, 2);
add_filter('manage_cavalos_posts_columns', 'samuweb_bidu_cavalos_columns');
add_action('manage_cavalos_posts_custom_column' , 'samuweb_bidu_cavalos_column_values', 10, 2);

function samuweb_bidu_leiloes_columns($columns) {
    unset($columns['author']);
    $columns['start_date'] = 'Começa';
    $columns['end_date'] = 'Termina';
    $columns['balance'] = 'Balanço';
    $columns['manage'] = 'Administrar';

    return $columns;
}


function samuweb_bidu_leiloes_column_values($column, $post_id) {
    switch($column):
        case 'start_date':
            if(get_field('start_date')) echo date("d/m/Y", strtotime(get_field('start_date'))) . ' ' . get_field('start_time');
            break;
        case 'end_date':
            if(get_field('end_date')) echo date("d/m/Y", strtotime(get_field('end_date'))) . ' ' . get_field('end_time');
            break;
        case 'balance':
            $redirect_link = add_query_arg(array('page' => 'samuweb_bidu_balance', 'id' => $post_id), admin_url('admin.php'));
            echo '<a href="' . $redirect_link . '">Ver balanço</a>';
            break;
        case 'manage':
            $redirect_link = add_query_arg(array('page' => 'samuweb_bidu_manage', 'id' => $post_id), admin_url('admin.php'));
            echo '<a href="' . $redirect_link . '">Administrar</a>';
            break;
    endswitch;
}


function samuweb_bidu_cavalos_columns($columns) {
    unset($columns['author']);
    $columns['belongs_to'] = 'Leilão';
    $columns['initial_bid'] = 'Lance inicial';
    $columns['last_bid'] = 'Lance atual';
    $columns['is_active'] = 'Inativo';
    $columns['is_sold'] = 'Vendido';

    return $columns;
}


function samuweb_bidu_cavalos_column_values($column, $post_id) {
    switch($column):
        case 'belongs_to':
            printf('<a href="%s">%s</a>',
                   get_the_permalink(get_field('belongs_to')->ID),
                   get_the_post_thumbnail(get_field('belongs_to')->ID, array(100,100))
            );
            break;
        case 'initial_bid':
            printf('R$ %d,00', get_field('price'));
            break;
        case 'last_bid':
            printf('<strong>R$ %d,00</strong>', samuweb_bidu_highest_bids($post_id));
            break;
        case 'is_active':
            $value = (get_field('is_active') == 1) ? '<span class="green">Sim</span>' : '-';
            echo $value;
            break;
        case 'is_sold':
            $value = (get_field('is_sold') == 1) ? '<span class="green">Sim</span>' : '-';
            echo $value;
            break;
    endswitch;
}


// Sort columns on custom post types
add_filter('manage_edit-leiloes_sortable_columns', 'samuweb_bidu_leiloes_sortable_columns');
add_action('pre_get_posts', 'samuweb_bidu_leiloes_orderby');
add_filter('manage_edit-cavalos_sortable_columns', 'samuweb_bidu_cavalos_sortable_columns');
add_action('pre_get_posts', 'samuweb_bidu_cavalos_orderby');

function samuweb_bidu_leiloes_sortable_columns($columns) {
	$columns['start_date'] = 'initial_bid';
	$columns['end_date'] = 'belongs_to';
	return $columns;
}


function samuweb_bidu_leiloes_orderby($query) {
    if(!is_admin()) return;
 
    $orderby = $query->get('orderby');
    $query->set('orderby','meta_value_num');
    switch($orderby):
        case 'start_date':
            $query->set('meta_key','start_date');
        break;

        case 'end_date':
            $query->set('meta_key','end_date');
        break;
    endswitch;
}


function samuweb_bidu_cavalos_sortable_columns($columns) {
	$columns['initial_bid'] = 'initial_bid';
	$columns['belongs_to'] = 'belongs_to';
	$columns['is_active'] = 'is_active';
	$columns['is_sold'] = 'is_sold';
	return $columns;
}


function samuweb_bidu_cavalos_orderby($query) {
    if(!is_admin()) return;
 
    $orderby = $query->get('orderby');
    $query->set('orderby','meta_value_num');
    switch($orderby):
        case 'initial_bid':
            $query->set('meta_key','price');
        break;

        case 'belongs_to':
            $query->set('meta_key','belongs_to');
        break;

        case 'is_active':
            $query->set('meta_key','is_active');
        break;

        case 'is_sold':
            $query->set('meta_key','is_sold');
        break;
    endswitch;
}