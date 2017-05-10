<?php
// Balance page
function samuweb_bidu_balance_page() {
?>
    <div class="wrap">
        <h1>Balanço</h1>
        <?php if(!$_GET['id']): ?>
        
            <h1>Escolha um leilão na página de leilões</h1>
            <select id="auction-id" name="auction-id">
                <option disabled selected value="">Selecione</option>
                <?php foreach($posts as $post): setup_postdata($post);
                if(get_field('belongs_to', $post->ID)->ID == $auction_id):?>
                    <option value="<?php echo $post->ID; ?>"><?php echo $post->post_title; ?></option>
                <?php endif;
                endforeach; ?>
            </select>


        <?php else:
            global $post;
            $post = get_post($_GET['id']);
            setup_postdata($post);
        ?>

        <header class="auction-info">
            <div class="featured-image">
                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
            </div>
            <h1><a href="<?php the_permalink(); ?>"><?php the_title()?></a></h1>

            <p>Período de <?php echo date("d/m/Y", strtotime(get_field('start_date'))) . ' ' . get_field('start_time') ?>hs até <?php echo date("d/m/Y", strtotime(get_field('end_date'))) . ' ' . get_field('end_time') ?>hs</p>
            <p><?php the_field('payments'); ?> parcelas</p>


        <?php
            $args = array(
                'posts_per_page'    => -1,
                'post_type'         => 'cavalos',
                'meta_query'        => array(
                    'relation'          => 'AND',
                        array(
                            'key'       => 'lot'
                        ), 
                        array(
                            'key'       => 'belongs_to',
                            'value'     => $post->ID,
                        ),
                    ),
                'order'             => 'ASC'
            );


            $the_query = new WP_Query($args);

            $sold_items = 0;
            $defended_items = 0;
            $unbidden = 0;
            $total_items = $the_query->post_count;
            $total_revenue = 0;

            if($the_query->have_posts()):
                while($the_query->have_posts()):
                    $the_query->the_post();
                    $is_sold = get_field('is_sold');
                    $is_last_bid = is_last_bid_admin($post->ID);
                    if($is_sold):
                        $sold_items++;
                        $total_revenue += samuweb_bidu_highest_bids($post->ID);
                    endif;
                    if($is_last_bid) $defended_items++;
                    if(!$is_sold && !$is_last_bid) $unbidden++;
                endwhile;
            endif;

            $payments = get_field('payments', get_field('belongs_to'));
        ?>
        <div class="box-info">
            <p><strong>Total de lotes: </strong><?php echo $total_items; ?></p>
            <p><strong>Vendidos: </strong><?php echo $sold_items; ?></p>
            <p><strong>Defendidos: </strong><?php echo $defended_items; ?></p>
            <p><strong>Sem interesse: </strong><?php echo $unbidden; ?></p>

            <p><strong>Faturamento vendidos: </strong> R$<?php echo number_format($total_revenue * $payments, 0, '', '.'); ?>,00</p>
            <?php if($sold_items): ?><p><strong>Média: </strong> R$<?php echo number_format(($total_revenue * $payments) / $sold_items, 0, '', '.'); ?>,00</p><?php endif; ?>
        </div>
    </header>
    
 
        <ul id="balance-list">
            <?php
            $the_query = new WP_Query($args);

            if($the_query->have_posts()):
                while($the_query->have_posts()):
                    $the_query->the_post();
                    ?>
                    <li>
                        <div class="product-title">
                            <span class="lot-number">#<?php the_field('lot'); ?></span>
                            <h4><?php the_title(); ?></h4>
                        </div>

                        <div class="thumb"><?php the_post_thumbnail('thumbnail'); ?></div>
                        <div class="product-info">
                            <?php
                                $last_bid = samuweb_bidu_highest_bids($post->ID);
                                $highest_bid = ($last_bid) ? $last_bid : get_field('price');
                                $highest_bid_total = $highest_bid * get_field('payments', get_field('belongs_to'));

                                printf('<p><strong>Último lance:</strong> R$ %d,00</p>', $highest_bid);
                                printf('<p><strong>Total da venda:</strong> R$ %s,00</p>', number_format($highest_bid_total, 0, '', '.'));
                            ?>
                            <?php if(is_last_bid_admin($post->ID)) echo '<p class="defense-bid" title="Defendido">Defendido</p>'; ?>
                            <?php if(get_field('is_sold')) echo '<p class="is-sold">Vendido</p>'; ?>
                        </div>
                    </li>

                <?php
                endwhile;
            endif;
            ?>
        </ul>

<?php endif; ?>

    </div>
    <?php
}







function samuweb_bidu_manage_page() {
?>
    <div class="wrap" id="top">
        <h1>Administrar leilão</h1>

        <?php if(!$_GET['id']): ?>
            <h1>Escolha um leilão na página de leilões</h1>
            <select id="auction-id" name="auction-id">
                <option disabled selected value="">Selecione</option>
                <?php foreach($posts as $post): setup_postdata($post);
                if(get_field('belongs_to', $post->ID)->ID == $auction_id):?>
                    <option value="<?php echo $post->ID; ?>"><?php echo $post->post_title; ?></option>
                <?php endif;
                endforeach; ?>
            </select>

        <?php else:
            global $post;
            $post = get_post($_GET['id']);
            setup_postdata($post);
        ?>

        <header class="auction-info">
            <div class="featured-image">
                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
            </div>
            <h1><a href="<?php the_permalink(); ?>"><?php the_title()?></a></h1>

            <p>Período de <?php echo date("d/m/Y", strtotime(get_field('start_date'))) . ' ' . get_field('start_time') ?> até <?php echo date("d/m/Y", strtotime(get_field('end_date'))) . ' ' . get_field('end_time') ?></p>
            <p><?php the_field('payments'); ?> parcelas</p>
            <p><a href="#box-last-bids">Ver últimos lances</a></p>
        </header>





<?php add_thickbox(); ?>
<div id="thickbox-add-bid" style="display:none;"><p>Carregando...</p></div>




        <table class="wp-list-table widefat fixed striped posts">
            <tr>
                <th style="width: 5%">Lote</th>
                <th style="width: 25%">Nome cavalo</th>
                <th style="width: 12.5%">Lance inicial</th>
                <th style="width: 12.5%">Último lance</th>
                <th style="width: 15%">Total atual</th>
                <th style="width: 10%">Situação</th>
                <th style="width: 12.5%">Ver lances</th>
                <th style="width: 7.5%">Editar</th>
            </tr>

            <?php


            $args = array(
                'posts_per_page'    => -1,
                'post_type'         => 'cavalos',
                'meta_query'        => array(
                    'relation'          => 'AND',
                        array(
                            'key'       => 'lot'
                        ), 
                        array(
                            'key'       => 'belongs_to',
                            'value'     => $post->ID,
                        ),
                    ),
                'order'             => 'ASC'
            );

            $the_query = new WP_Query($args);

            if($the_query->have_posts()):
                while($the_query->have_posts()):
                    $the_query->the_post();

                        $last_bid = samuweb_bidu_highest_bids($post->ID);
                        $highest_bid = ($last_bid) ? $last_bid : get_field('price');
                        $highest_bid_total = $highest_bid * get_field('payments', get_field('belongs_to'));
                    ?>

                    <tr data-row="<?php echo $row->id; ?>" <?php echo ($is_bid_from_admin) ? 'class="row-admin"' : ''; ?>>
                        <td><?php the_field('lot'); ?></td>
                        <td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
                        <td>R$ <?php the_field('price') ?>,00</td>
                        <td><?php printf('<p>R$ %d,00</p>', $highest_bid); ?></td>
                        <td><?php printf('<p><strong>R$ %s,00</strong></p>', number_format($highest_bid_total, 0, '', '.')); ?></td>
                        <td>
                            <?php if(is_last_bid_admin($post->ID)) echo '<p class="defense-bid" title="Defendido">Defendido</p>'; ?>
                            <?php if(get_field('is_sold')) echo '<p class="is-sold">Vendido</p>'; ?>
                        </td>
                        <td>
                        <?php
                            $label_button_bids = ($last_bid) ? 'Ver lances' : 'Adicionar lance';
                            $ajax_url = add_query_arg(array('action' => 'last_bids_box', 'id' => $post->ID), 'admin-ajax.php');  
                        ?>
                            <a href="<?php echo $ajax_url; ?>#TB_inline?height=600&width=750&inlineId=thickbox-add-bid" class="thickbox" data-id="<?php echo $post->ID; ?>" title="<?php the_title(); ?>"><?php echo $label_button_bids; ?></a>
                        </td>
                        <td><a href="/wp-admin/post.php?post=<?php echo $post->ID; ?>&action=edit">Editar</a></td>
                    </tr>
                <?php
                endwhile;
            endif;
            ?>
        </table>

        <div id="box-last-bids">
            <a href="#top" class="link-top">Voltar ao topo</a>
            <h2>Últimos lances</h2>

            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . "samuweb_bids";
            $auction_id = $post->ID;

            $rows = $wpdb->get_results("
                SELECT * from $table_name
                WHERE auction_id = {$_GET['id']}
                ORDER BY bidded_at DESC
                LIMIT 10");
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
                $user_name = $user_info->first_name . ' ' . $user_info->last_name;
                $city = get_the_author_meta('city', $row->user_id);
                $province = get_the_author_meta('province', $row->user_id);
            else:
                $user_name = $user_info->first_name . ' ' . $user_info->last_name;
                $city = $row->city;
                $province = $row->province;
            endif;

            $is_bid_from_admin = false;
            if(user_can($row->user_id,'administrator')) $is_bid_from_admin = true;
            ?>

                <tr data-row="<?php echo $row->id; ?>" <?php echo ($is_bid_from_admin) ? 'class="row-admin"' : ''; ?>>
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
    <?php endif; ?>
    </div>
<script>
jQuery('.thickbox').on('click', function(){
    jQuery('#thickbox-add-bid').html('<p>Carregando...</p>');
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
            action: 'last_bids_box',
            id: jQuery(this).data('id')
        }
    }).done(function(result) {
        jQuery("#TB_ajaxContent").html(result);
    });
});

jQuery(document).on('click', '.delete-bid', function(){
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