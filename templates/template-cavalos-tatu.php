<?php get_template_part('includes/header'); ?>
<?php $auction_post = get_post(get_field('belongs_to')->ID); ?>

<script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>

<section class="product container">
    <div class="row">
            <?php
            $auction_id = get_field('belongs_to')->ID;
            $posts_in_same_auction = [];
            $posts_ids = [];
            $the_query = new WP_Query(
                array(
                    'numberposts'	=> -1,
                    'post_type'		=> 'cavalos',
                    'meta_key'		=> 'belongs_to',
                    'meta_value'	=> $auction_id
                )
            );

            if($the_query->have_posts()):
                while($the_query->have_posts()): $the_query->the_post();
                    $lot_numbers[get_field('lot')] = get_the_ID();
                    $title[get_the_ID()] = get_the_title();
                endwhile;
                wp_reset_postdata();

                asort($lot_numbers);

                foreach($lot_numbers as $key => $value):
                    $posts_ids[] = $value;

                endforeach;


                if($posts_ids):
                    while(current($posts_ids) != $post->ID):
                        next($posts_ids);
                    endwhile;
                endif;

                $prev = key($posts_ids) - 1;
                $next = key($posts_ids) + 1;
                $count = count($posts_ids) - 1;

                if($prev >= 0) $prev_link = sprintf('<a href="%s" class="prev-product-link"> < Anterior</a>', get_permalink($posts_ids[$prev]));
                if($next <= $count) $next_link = sprintf('<a href="%s" class="next-product-link">Próximo > </a>', get_permalink($posts_ids[$next]));
            endif;

    ?>
        <div class="col-xs-3 col-sm-3">
            <?php echo $prev_link; ?>
        </div>
        <div class="col-xs-6 col-sm-6">
            <a class="back-link" href="<?php echo get_permalink($auction_post->ID); ?>">Voltar para <?php echo $auction_post->post_title; ?></a>
        </div>
        <div class="col-xs-3 col-sm-3">
            <?php echo $next_link; ?>
        </div>
    </div>

    <div class="product-header row">
        <div class="col-xs-12 col-sm-12">
            <h1 class="product-name"><span class="lot-number"><?php the_field('lot'); ?></span> <?php the_title(); ?></h1>
        </div>
    </div>


    <div class="row">
        <div class="gallery">
            <div class="col-xs-12 col-sm-6">
                <?php samuweb_bidu_show_gallery('product-carousel');?>
            </div>

            <div class="col-xs-12 col-sm-6">
                <?php if(get_field('video')): ?>
                <div class="product-video col-xs-12 col-sm-12">
                    <iframe
                        src="https://player.vimeo.com/video/<?php the_field('video'); ?>?title=0&byline=0&portrait=0"
                        frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen>
                    </iframe>
                </div>
                <?php endif; ?>

               <?php
                    $bid_raise =
                        (samuweb_bidu_highest_bids($post->ID) != '') ?
                        samuweb_bidu_highest_bids($post->ID) + get_field('bid_raise', get_field('belongs_to')->ID) :
                        get_field('bid_raise', get_field('belongs_to')->ID);
                    $payments = get_field('payments', get_field('belongs_to')->ID);
                    $total_price = $bid_raise * $payments;
                ?>



                <?php if(get_field('is_sold') == true ): ?>


                <div class="form-bid">
                    <h3>Vendido por R$ <?php echo number_format($total_price, false, ',', '.') ; ?></h3>
                    <p class="btn cta-bid cta-bid-disabled">Leilão encerrado</p>
                </div>


                



                <?php else: ?>
                
                <div class="form-bid">R$
                    <input type="number" class="form-control" id="value" min="<?php echo $bid_raise; ?>" step="<?php echo get_field('bid_raise', get_field('belongs_to')->ID) ?>" value="<?php echo $bid_raise; ?>"><small>,00</small>
                    <input type="hidden" id="auction-id" name="auction-id" value="<?php echo $auction_id; ?>">
                    <input type="hidden" id="product-id" name="product-id" value="<?php echo $post->ID; ?>">
                    <input type="hidden" id="user-id" name="user-id" value="<?php echo $user_ID; ?>">
                    <input type="hidden" id="min-bid" name="min-bid" value="<?php echo samuweb_bidu_highest_bids($post->ID); ?>">

                    <a href="<?php if(is_user_logged_in()): echo 'javascript:;'; else: echo '/login/'; endif; ?>" class="btn btn-primary cta-bid" id="cta">Dê seu lance</a>


                    <div class="bid-info">
                        <p><strong>Lance total (x<span id="payments"><?php echo get_field('payments', get_field('belongs_to')->ID); ?></span>):</strong> R$ <span id="total-price"><?php echo number_format($total_price, false, ',', '.') ; ?></span>,00</p>
                    </div>
                </div>

                <?php endif; ?>
            </div>
        </div>
    </div>


    <div class="product-info row">
        <ul class="col-xs-12 col-sm-3">
            <li>
                <h5>Sexo:</h5>
                <p><?php the_field('sex'); ?></p>
            </li>
            <li>
                <h5>Idade:</h5>
                <p><?php date_difference(get_field('birthday')); ?></p>
            </li>
            <li>
                <h5>Raça:</h5>
                <p><?php the_field('race'); ?></p>
            </li>
            <li>
                <h5>Pelagem:</h5>
                <p><?php the_field('coat_color'); ?></p>
            </li>
        </ul>
        <ul class="col-xs-12 col-sm-3">
            <li>
                <h5>Localização:</h5>
                <p><?php the_field('location'); ?></p>
            </li>
            <li>
                <h5>Proprietário:</h5>
                <p><?php the_field('owner'); ?></p>
            </li>
            <li>
                <h5>Criador:</h5>
                <p><?php the_field('breeder'); ?></p>
            </li>
            <li>
                <h5>Prêmios:</h5>
                <p><?php the_field('awards'); ?></p>
            </li>
        </ul>



        <div class="bid-box col-xs-12 col-sm-6">
            <h3>Últimos lances:</h3>
            <hr>
            <ul class="last-bids">
                <?php samuweb_bidu_last_bids($post->ID); ?>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="description col-xs-12 col-sm-6">
            <h2>Descrição</h2>
            <?php echo apply_filters('the_content', $post->post_content); ?>
        </div>

        <ul class="genealogy col-xs-12 col-sm-6">
            <h2>Filiação</h2>
            <li>
                <p><?php the_field('name'); ?></p>
                <ul>
                    <li>
                        <p><?php echo (get_field('father')) ? get_field('father') : 'Não disponível'; ?></p>
                        <ul>
                            <li>
                                <p><?php echo (get_field('paternal_grandfather')) ? get_field('paternal_grandfather') : 'Não disponível'; ?></p>
                            </li>
                            <li>
                                <p><?php echo (get_field('paternal_grandmother')) ? get_field('paternal_grandmother') : 'Não disponível'; ?></p>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <p><?php echo (get_field('mother')) ? get_field('mother') : 'Não disponível'; ?></p>
                        <ul>
                            <li>
                                <p><?php echo (get_field('maternal_grandfather')) ? get_field('maternal_grandfather') : 'Não disponível'; ?></p>
                            </li>
                            <li>
                                <p><?php echo (get_field('maternal_grandmother')) ? get_field('maternal_grandmother') : 'Não disponível'; ?></p>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </div>


<?php if(function_exists('the_views')) : ?>
    <p class="video-count">Essa página já foi visualizada <?php the_views(); ?> vezes.</p>
<?php endif; ?>
</section>



<?php get_template_part('includes/footer'); ?>
