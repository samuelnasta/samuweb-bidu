<?php
// Register Custom Post Types
add_action('init', 'samuweb_bidu_register_leiloes');
add_action('init', 'samuweb_bidu_register_leiloeiros');
add_action('init', 'samuweb_bidu_register_cavalos');
add_theme_support('post-thumbnails', array('page','post','leiloes','leiloeiros','cavalos'));

function samuweb_bidu_register_leiloes() {
    register_post_type('leiloes', array(
        'labels' => array(
                'name' => 'Leilões',
                'singular_name' => 'Leilão'
            ),
        'capability_type' => 'post',
        'has_archive' => true,
        'menu_icon' => plugin_dir_url(__FILE__) . 'admin/images/samuweb-bidu-admin-auction.png',
        'menu_position' => 4,
        'public' => true,
        'rewrite' => array(
                'slug' => 'leiloes',
                'with_front' => FALSE
            ),
        'supports' => array(
                'editor',
                'revisions',
                'thumbnail',
                'title'
            ),
        'taxonomies' => array('category')
        )
    );
}


function samuweb_bidu_register_leiloeiros() {
    register_post_type('leiloeiros', array(
        'labels' => array(
                'name' => 'Leiloeiros',
                'singular_name' => 'Leiloeiro'
            ),
        'capability_type' => 'post',
        'has_archive' => true,
        'menu_icon' => plugin_dir_url(__FILE__) . 'admin/images/samuweb-bidu-admin-auctioneer.png',
        'menu_position' => 4,
        'public' => true,
        'rewrite' => array(
                'slug' => 'leiloeiros',
                'with_front' => FALSE
            ),
        'supports' => array(
                'editor',
                'revisions',
                'thumbnail',
                'title'
            )
        )
    );
}


function samuweb_bidu_register_cavalos() {
    register_post_type('cavalos', array(
        'labels' => array(
                'name' => 'Cavalos',
                'singular_name' => 'Cavalo'
            ),
        'capability_type' => 'post',
        'has_archive' => true,
        'menu_icon' => plugin_dir_url(__FILE__) . 'admin/images/samuweb-bidu-admin-product.png',
        'menu_position' => 4,
        'public' => true,
        'rewrite' => array(
                'slug' => 'cavalos',
                'with_front' => FALSE
            ),
        'supports' => array(
                'editor',
                'revisions',
                'thumbnail',
                'title'
            )
        )
    );
}






// Custom post type templates
add_filter('page_template', 'samuweb_bidu_template_lances');
add_filter('single_template', 'samuweb_bidu_template_leiloes');
add_filter('single_template', 'samuweb_bidu_template_cavalos');

function samuweb_bidu_template_lances($single) {
    global $wp_query, $post;

    if (is_page('meus-lances')){
        if(file_exists(dirname(__FILE__) . '/templates/template-lances.php'))
            return dirname(__FILE__) . '/templates/template-lances.php';
    }
    return $single;
}


function samuweb_bidu_template_leiloes($single) {
    global $wp_query, $post;

    if ($post->post_type == 'leiloes'){
        if(file_exists(dirname(__FILE__) . '/templates/template-leiloes.php'))
            return dirname(__FILE__) . '/templates/template-leiloes.php';
    }
    return $single;
}


function samuweb_bidu_template_cavalos($single) {
    global $wp_query, $post;

    if ($post->post_type == 'cavalos'){
        if(file_exists(dirname(__FILE__) . '/templates/template-cavalos.php'))
            return dirname(__FILE__) . '/templates/template-cavalos.php';
    }
    return $single;
}





// Adds the new bid link in the menu
add_action('admin_menu', 'samuweb_bidu_create_bid_menu');
function samuweb_bidu_create_bid_menu() {
    add_menu_page(
        'Adicionar lance',
        'Adicionar lance',
        'manage_options',
        'samuweb_bidu_create_bid',
        'samuweb_bidu_create_bid_page',
        plugin_dir_url(__FILE__) . 'admin/images/samuweb-bidu-admin-new-bid.png',
        9
    );
}


// Adds the balance page
add_action('admin_menu', 'samuweb_bidu_balance');
function samuweb_bidu_balance() {
    add_menu_page(
        'Ver balanços',
        'Ver balanços',
        'manage_options',
        'samuweb_bidu_balance',
        'samuweb_bidu_balance_page',
        plugin_dir_url(__FILE__) . 'admin/images/samuweb-bidu-admin-balance.png',
        9
    );
}



// Adds auction management page
add_action('admin_menu', 'samuweb_bidu_manage');
function samuweb_bidu_manage() {
    add_menu_page(
        'Administrar leilão',
        'Administrar leilão',
        'manage_options',
        'samuweb_bidu_manage',
        'samuweb_bidu_manage_page',
        plugin_dir_url(__FILE__) . 'admin/images/samuweb-bidu-admin-balance.png',
        9
    );
}