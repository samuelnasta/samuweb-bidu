<?php
if(!is_user_logged_in()) header('Location: /login/');

get_template_part('includes/header'); ?>
<!-- cabeçalho -->
<div class="about">
      <div class="container">
        <section class="title-section">
            <h1 class="title-header">
            <?php the_title()?></h1>
        </section>
       </div>
</div>
<!-- / cabeçalho -->


<?php
global $wpdb;
$table_name = $wpdb->prefix . "samuweb_bids";
$user_id = get_current_user_id();
$start = 0;
$posts_per_page = 10;
$current = get_query_var('paged') ? get_query_var('paged', 1) : 1;
$start = ($current - 1) * $posts_per_page;

$total_results = $wpdb->get_var("
    SELECT COUNT(1) as count from $table_name
    WHERE user_id = $user_id
    ORDER BY bidded_at DESC");

$rows = $wpdb->get_results("
    SELECT * from $table_name
    WHERE user_id = $user_id
    ORDER BY bidded_at DESC
    LIMIT $start, $posts_per_page");
?>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-8">

            <table class="bid-table">
                <tr>
                    <th><strong>Horário do lance</strong></th>
                    <th><strong>Leilão</strong></th>
                    <th><strong>Cavalo</strong></th>
                    <th><strong>Lance</strong></th>
                    <th><strong>Valor total</strong></th>
                </tr>
                <?php
                foreach ($rows as $row):
                    if($row->user_id):
                        $user_info = get_userdata($row->user_id);
                        $user_name = $user_info->user_login;
                    else:
                        $user_name = $row->user_name;
                    endif;
                    $total_value = $row->value * get_field('payments', $row->auction_id);
                    ?>
                <tr data-row="<?php echo $row->id; ?>">
                    <td><?php echo date("d/m/Y - H:i", strtotime($row->bidded_at)); ?></td>
                    <td><a href="<?php echo get_permalink($row->auction_id); ?>"><?php echo get_the_title($row->auction_id); ?></a></td>
                    <td><a href="<?php echo get_permalink($row->product_id); ?>"><?php echo get_the_title($row->product_id); ?></a></td>
                    <td>R$ <?php echo $row->value; ?>,00</td>
                    <td>R$ <?php echo number_format($total_value, false, ',', '.'); ?>,00</td>
                </tr>
            <?php endforeach; ?>
            </table>


            <div class="pagination-wrapper">
                <ul class="pagination">
                    <?php
                $pages = paginate_links(array(
                    'base' => @add_query_arg('paged','%#%'),
                    'format' => '?paged=%#%',
                    'prev_text' => 'Anterior',
                    'next_text' => 'Próximo',
                    'type'  => 'array',
                    'total' => ceil($total_results / $posts_per_page),
                    'current' => max(1, get_query_var('paged'))
                ));







                if(is_array($pages)):
                    $i = 0;
                    foreach($pages as $page):
                        $is_active = '';
                        if($current == 1 && $i == 0) $is_active = ' class="active"';
                        if($current != 1 && $current == $i) $is_active = ' class="active"';
                        echo "<li $is_active>$page</li>";
                        $i++;
                    endforeach;
                endif;
                ?>
                </ul>
            </div>
        </div>

        <div class="col-xs-12 col-sm-4">
            <?php get_template_part('includes/sidebar'); ?>
        </div>
    </div>
</div> <!-- /.container -->


<?php get_template_part('includes/footer'); ?>
