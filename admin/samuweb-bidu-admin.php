<?php
if(!is_admin()) {
    die('Sem autorização.');
}

// Onde alterar o plugin WP-User-Manager
//      wp-user-manager/includes/forms/class-wpum-form-register.php
//
//        public static function do_registration()
//            if( array_key_exists( 'address' , $values['register'] ) )
//                update_user_meta( $user_id, 'address', $values['register']['address'] );
//            if( array_key_exists( 'city' , $values['register'] ) )
//                update_user_meta( $user_id, 'city', $values['register']['city'] );
//            if( array_key_exists( 'province' , $values['register'] ) )
//                update_user_meta( $user_id, 'province', $values['register']['province'] );
//            if( array_key_exists( 'phone' , $values['register'] ) )
//                update_user_meta( $user_id, 'phone', $values['register']['phone'] );
//            if( array_key_exists( 'cpf' , $values['register'] ) )
//                update_user_meta( $user_id, 'cpf', $values['register']['cpf'] );


?>
<div class="wrap">
    <h2>Configurações do plugin Samuweb Bidu</h2>

    <div class="tool-box">
        <h2>Step 1</h2>

        <select id="link-to">
            <option disabled selected>Pertence a qual leilão?</option>
            <?php
            $args=array(
                'post_type' => 'auctions',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'caller_get_posts'=> 1,
                'orderby' => 'date'
            );

            $my_query = null;
            $my_query = new WP_Query($args);
            if( $my_query->have_posts() ) {
              while ($my_query->have_posts()) : $my_query->the_post(); ?>
                <option value="<?php the_id(); ?>"><?php the_title();?></option>
                <?php
              endwhile;
            }
            wp_reset_query();
            ?>
        </select>
    </div>
</div>