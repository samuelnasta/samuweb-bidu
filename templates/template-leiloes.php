<?php get_template_part('includes/header'); ?>
<script type="text/javascript">
$( document ).ready(function() {
    $("[rel='tooltip']").tooltip();    
 
    $('.thumbnail').hover(
        function(){
            $(this).find('.caption').slideDown(250); //.fadeIn(250)
        },
        function(){
            $(this).find('.caption').slideUp(250); //.fadeOut(205)
        }
    ); 
});
</script>
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

<div class="container">

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <?php the_post_thumbnail('full'); ?>
        </div>

        <div class="auction-info col-xs-12 col-sm-6">
            <a class="back-link-auction" href="/leiloes"><strong>Ver todos os leilões</strong></a>
            <div class="tempo">
		<h5 style="text-align:center;font-family: 'Open Sans', sans-serif;">TEMPO PARA ENCERRAMENTO</h5>
            <p id="clock" data-countdown="<?php if(get_field('end_date')) echo date("Y/m/d", strtotime(get_field('end_date'))); ?> <?php the_field('end_time'); ?>">--:--:--:--</p>
            <small><span>dias</span><span>horas</span><span>minutos</span><span>segundos</span></small>
</div><br/>


            <ul class="auction-dates">
                <li><strong>Início:</strong> <?php if(get_field('start_date')) echo date("d/m/Y", strtotime(get_field('start_date'))); ?> <?php the_field('start_time'); ?></li>
                <li><strong>Fim:</strong> <?php if(get_field('end_date'))  echo date("d/m/Y", strtotime(get_field('end_date'))); ?> <?php the_field('end_time'); ?></li>
            </ul>


            <ul class="terms-box">
                <li><strong>Regulamento</strong></li>
                <li><a href="javascript:;" id="show-terms" data-toggle="modal" data-target="#myModal">Ver o regulamento</a></li>
            </ul>
            
            <ul class="terms-box">
                <li><strong>Informações</strong></li>
                <li><a href="javascript:;" id="show-terms" data-toggle="modal" data-target="#myModal2">Ver informações</a></li>
            </ul>
            
	<p style="margin:40px 0 0 10px">Para dar um lance é necessário se <a href="http://marchanews.com.br/cadastro/">CADASTRAR</a> no novo sistema</p>

            <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Regulamento</h4>
                        </div>
                        <div class="modal-body terms">
                            <?php the_field('terms'); ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            

	
            <!-- Modal -->
            <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Informações</h4>
                        </div>
                        <div class="modal-body terms">
                            <img src="http://marchanews.com.br/wp-content/uploads/2017/04/Informacoes.jpg">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>


    <hr>


    <div class="row">

        <div class="filters col-xs-12 col-sm-12">
            <button class="btn btn-default active" data-toggle="portfilter" data-target="all">Mostrar todos</button>
            <button class="btn btn-default" data-toggle="portfilter" data-target="Fêmea">Apenas Fêmeas</button>
            <button class="btn btn-default" data-toggle="portfilter" data-target="Macho">Apenas Machos</button>
            <button class="btn btn-default" data-toggle="portfilter" data-target="Embrião">Apenas Embriões</button>
            <button class="btn btn-default" data-toggle="portfilter" data-target="Cobertura">Apenas Coberturas</button>
        </div>

    </div>
    <div class="row">
        <ul class="product-list col-xs-12 col-sm-12">
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
                while($the_query->have_posts()): $the_query->the_post();
                    if(!get_field('is_active')): ?>


                <li data-tag="<?php echo the_field('sex'); ?>">
                    <h5><?php the_field('lot'); ?> . <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                <a href="<?php the_permalink(); ?>">
                <div class="thumbnail">
                <div class="caption">
                    
                    <p><small>Tipo: </small><?php the_field('sex'); ?></p>
                    <p><small>Idade: </small><?php date_difference(get_field('birthday')); ?></p>
                    <p><small>Localização: </small><?php the_field('location'); ?></p>
                    <p><small>Criador: </small><?php the_field('breeder'); ?></p>
                    <p><small>Lance Inicial: </small> R$ <?php the_field('price'); ?>,00</p>
                    <ul>
                        <li><a href="<?php the_permalink(); ?>" class="label label-default" style="font-size:13px" title="Informações">Veja mais</a></li>   
                    <li style="margin-left:50%"><?php if(get_field('video')): ?><a href="http://vimeo.com/<?php the_field('video'); ?>" target="_blank" title="Assistir vídeo"><i class="fa fa-youtube-play fa-2x" aria-hidden="true"></i></a></p><?php endif; ?></li>
                    </ul>
                </div>
                    <div class="thumb">
                        <?php if(get_field('is_sold')) echo '<div class="ribbon"><span>Vendido</span></div>'; ?>
                        <ul>
                        <!--<li><a href="<?php the_permalink(); ?>" class="label label-default" style="font-size:13px" title="Informações">Veja mais</a></li> -->  
                    <li style="margin-left:50%;position:absolute; float:right;text-align: right;bottom: -20px;right: 20px;"><?php if(get_field('video')): ?><a href="http://vimeo.com/<?php the_field('video'); ?>" target="_blank" title="Assistir vídeo"><i class="fa fa-youtube-play fa-2x" aria-hidden="true"></i></a></p><?php endif; ?></li>
                    </ul>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                    </div>
                </div>
                </li></a>
                <?php
                    endif;
                endwhile;
                wp_reset_postdata();
                else:
                    echo '<p>Nenhum item neste leilão</p>';
            endif; ?>
        </ul>
    </div> <br/>
<div style="font-size:14px">
Número de visualizações deste leilão: 
<?php if(function_exists('bac_PostViews')) { 
    echo bac_PostViews(get_the_ID()); 
}?>
</div>
    <hr>

    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <h3>Leiloeiros:</h3>
            <ul style="display: inline;">
            <li style="float: left;margin-right: 50px">
            <?php
            $auctioneer_count = 0;
                if(get_field('auctioneer_1')):
                $auctioneer_count++; ?>
                <p><?php echo get_field('auctioneer_1')->post_title; ?></p>
                <p><?php echo get_the_post_thumbnail(get_field('auctioneer_1')->ID, 'thumbnail'); ?></p></li>
                <?php
            endif;
            if(get_field('auctioneer_2')):
                $auctioneer_count++; ?>
                <li><p><?php echo get_field('auctioneer_2')->post_title; ?></p>
                <p><?php echo get_the_post_thumbnail(get_field('auctioneer_2')->ID, 'thumbnail'); ?></p>
            <?php endif;
                if($auctioneer_count == 0) echo '<p>Nenhum leiloeiro relacionado.</p>'; ?>
                </li>
                </ul>
        </div>
    </div>
</div> <!-- /.container -->

<?php get_template_part('includes/footer'); ?>
