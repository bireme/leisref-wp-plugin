<?php
/*
Template Name: LeisRef Home
*/

require_once(LEISREF_PLUGIN_PATH . '/lib/Paginator.php');

global $leisref_service_url, $leisref_plugin_slug;

$leisref_config = get_option('leisref_config');
$leisref_initial_filter = $leisef_config['initial_filter'];

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

$query = ( isset($_GET['s']) ? $_GET['s'] : $_GET['q'] );
$query = stripslashes($query);
$user_filter = stripslashes($_GET['filter']);
$page = ( isset($_GET['page']) ? $_GET['page'] : 1 );
$total = 0;
$count = 10;
$filter = '';

if ($leisref_initial_filter != ''){
    if ($user_filter != ''){
        $filter = $leisref_initial_filter . ' AND ' . $user_filter;
    }else{
        $filter = $leisref_initial_filter;
    }
}else{
    $filter = $user_filter;
}
$start = ($page * $count) - $count;

$leisref_search = $leisref_service_url . 'api/leisref/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&start=' . $start . '&lang=' . $lang;

$response = @file_get_contents($leisref_search);
if ($response){
    $response_json = json_decode($response);
    //var_dump($response_json);
    $total = $response_json->diaServerResponse[0]->response->numFound;
    $start = $response_json->diaServerResponse[0]->response->start;
    $legislation_list = $response_json->diaServerResponse[0]->response->docs;

    $descriptor_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->descriptor_filter;
    $act_type_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->act_type;
    $publication_year = $response_json->diaServerResponse[0]->facet_counts->facet_fields->publication_year;
}

$page_url_params = real_site_url($leisref_plugin_slug) . '?q=' . urlencode($query)  . '&filter=' . urlencode($filter);
$feed_url = real_site_url($leisref_plugin_slug) . 'legislation-feed?q=' . urlencode($query) . '&filter=' . urlencode($user_filter);

$pages = new Paginator($total, $start);
$pages->paginate($page_url_params);
$fulltext_lang['pt-br'] = __('Portuguese','leisref');
$fulltext_lang['es'] = __('Spanish','leisref');
$fulltext_lang['en'] = __('English','leisref');
?>

<?php get_header('leisref');?>
	<div id="content" class="row-fluid">
		<div class="ajusta2">
            <div class="row-fluid breadcrumb">
                <a href="<?php echo real_site_url(); ?>"><?php _e('Home','leisref'); ?></a> >
                <a href="<?php echo real_site_url($leisref_plugin_slug); ?>"><?php _e('Legislation Directory', 'leisref') ?> </a> >
                <?php _e('Search result', 'leisref') ?>
            </div>

			<section id="conteudo">
                <?php if ( isset($total) && strval($total) == 0) :?>
                    <h1 class="h1-header"><?php _e('No results found','leisref'); ?></h1>
                <?php else :?>
    				<header class="row-fluid border-bottom">
					   <h1 class="h1-header"><?php _e('Legislation found','leisref'); ?>: <?php echo $total; ?></h1>
			            <!--div class="pull-right">
				            <a href="<?php echo $feed_url ?>" target="blank"><img src="<?php echo LEISREF_PLUGIN_URL; ?>template/images/icon_rss.png" class="rss_feed" ></a>
                        </div-->
                        <?php if ($query != '' || $user_filter != ''){ echo $pages->display_pages(); } ?>
    				</header>
    				<div class="row-fluid">
                        <?php foreach ( $legislation_list as $resource) { ?>
    					    <article class="conteudo-loop">

        						<div class="row-fluid">
        							<h2 class="h2-loop-tit">
                                        <?php print_lang_value($resource->act_type, $site_language); ?>
                                        nº <?php echo $resource->act_number[0]; ?>, <?php _e(' of ', 'leisref'); ?>
                                        <?php echo format_act_date($resource->issue_date[0], $lang); ?>
                                    </h2>
        						</div>

                                <?php if ($resource->source_name): ?>
                                    <div class="row-fluid">
                                        <?php _e('Source','leisref'); ?>: <?php print_lang_value($resource->source_name, $lang) ;?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($resource->official_ementa): ?>
                                    <div class="row-fluid">
                                        <?php _e('Ementa','leisref'); ?>: <?php echo $resource->official_ementa[0];?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($resource->organ_issuer): ?>
                                    <div class="row-fluid">
                                        <?php _e('Organ issuer','leisref'); ?>: <?php print_lang_value($resource->organ_issuer[0], $lang);?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($resource->scope): ?>
                                    <div class="row-fluid">
                                        <?php _e('Act scope','leisref'); ?>:
                                        <?php print_lang_value($resource->scope, $lang);?>
                                        <?php echo ($resource->scope_state != ''  ? ' - ' . $resource->scope_state[0] : ''); ?>
                                        <?php echo ($resource->scope_city != ''  ? '- ' . $resource->scope_city[0] : ''); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($resource->relationship_active): ?>
                                    <?php foreach ( $resource->relationship_active as $rel) { ?>
                                        <div class="row-fluid">
                                            <?php
                                                $rel_parts = explode("@", $rel);
                                                $rel_relation = $rel_parts[0];
                                                $rel_act_type = $rel_parts[1];
                                                $rel_act_number = $rel_parts[2];
                                                $rel_act_date = $rel_parts[3];
                                                $rel_act_apparatus = $rel_parts[4];
                                                $rel_act_link = $rel_parts[5];
                                            ?>
                                            <?php
                                                print_lang_value($rel_relation, $lang);
                                                if ($rel_act_link != ''){
                                                    echo '<a href="' . real_site_url($leisref_plugin_slug) . 'detail/' . $rel_act_link . '">';
                                                }
                                                echo '&nbsp';
                                                print_lang_value($rel_act_type, $lang);
                                                echo '&nbsp';
                                                echo $rel_act_number . ', ' . __('of', 'leisref'). '&nbsp';
                                                echo format_act_date($rel_act_date, $lang) . '&nbsp';
                                                if ($rel_act_link != ''){
                                                    echo '</a>';
                                                }
                                                if ($rel_act_apparatus != ''){
                                                    echo '- ' . $rel_act_apparatus;
                                                }
                                            ?>
                                        </div>
                                    <?php } ?>
                                <?php endif; ?>


                                <?php if ($resource->relationship_passive): ?>
                                    <?php foreach ( $resource->relationship_passive as $rel) { ?>
                                        <div class="row-fluid">
                                            <?php
                                                $rel_parts = explode("@", $rel);
                                                $rel_relation = $rel_parts[0];
                                                $rel_act_type = $rel_parts[1];
                                                $rel_act_number = $rel_parts[2];
                                                $rel_act_date = $rel_parts[3];
                                                $rel_act_link = $rel_parts[4];
                                            ?>
                                            <?php
                                                print_lang_value($rel_relation, $lang);
                                                if ($rel_act_link != ''){
                                                    echo '<a href="' . real_site_url($leisref_plugin_slug) . 'detail/' . $rel_act_link . '">';
                                                }
                                                echo '&nbsp';
                                                print_lang_value($rel_act_type, $lang);
                                                echo '&nbsp';
                                                echo $rel_act_number . ', ' . __('of', 'leisref'). '&nbsp';
                                                echo format_act_date($rel_act_date, $lang) . '&nbsp';
                                                if ($rel_act_link != ''){
                                                    echo '</a>';
                                                }
                                                if ($rel_act_apparatus != ''){
                                                    echo '- ' . $rel_act_apparatus;
                                                }
                                            ?>
                                        </div>
                                    <?php } ?>
                                <?php endif; ?>


                                <?php if ($resource->descriptor || $resource->keyword ) : ?>
                                    <div id="conteudo-loop-tags" class="row-fluid margintop10">
                                        <i class="ico-tags"> </i>
                                        <?php echo implode(", ", $resource->descriptor); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($resource->fulltext): ?>
                                    <div class="row-fluid">
                                        <?php foreach ( $resource->fulltext as $fulltext) { ?>
                                            <?php
                                                $document_url_parts = explode("|", $fulltext);
                                                $document_lang = $document_url_parts[0];
                                                $document_url = $document_url_parts[1];
                                            ?>
                                            <span class="more">
                                                <a href="<?php echo $document_url ?>" target="_blank">
                                                    Texto em <?php echo $fulltext_lang[$document_lang] ?>
                                                </a>
                                            </span>&nbsp;&nbsp;
                                        <?php } ?>
                                    </div>
                                <?php endif; ?>

        					</article>
                        <?php } ?>
    				</div>
                    <div class="row-fluid">
                        <?php if ($query != '' || $user_filter != ''){ echo $pages->display_pages(); } ?>
                    </div>
                <?php endif; ?>
			</section>
			<aside id="sidebar">
			<section class="header-search">
        		<?php if ($leisref_config['show_form']) : ?>
            		<form role="search" method="get" id="searchform" action="<?php echo real_site_url($leisref_plugin_slug); ?>">
            			<input value='<?php echo $query ?>' name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Search', 'leisref'); ?>...">
            			<input id="searchsubmit" value="<?php _e('Search', 'leisref'); ?>" type="submit">
            		</form>
        		<?php endif; ?>
    	    </section>

            <?php if (count($descriptor_list) > 0) :?>
				<section class="row-fluid marginbottom25 widget_categories">
					<header class="row-fluid border-bottom marginbottom15">
						<h1 class="h1-header"><?php _e('Subjects','direve'); ?></h1>
					</header>
					<ul>
                        <?php foreach ( $descriptor_list as $descriptor) { ?>
                            <?php
                                $filter_link = '?';
                                if ($query != ''){
                                    $filter_link .= 'q=' . $query . '&';
                                }
                                $filter_link .= 'filter=descriptor:"' . $descriptor[0] . '"';
                                if ($user_filter != ''){
                                    $filter_link .= ' AND ' . $user_filter ;
                                }
                            ?>
                            <li class="cat-item">
                                <a href='<?php echo $filter_link; ?>'><?php echo $descriptor[0] ?></a>
                                <span class="cat-item-count"><?php echo $descriptor[1] ?></span>
                            </li>
                        <?php } ?>
					</ul>
				</section>
            <?php endif; ?>
            <?php if (count($act_type_list) > 0) :?>
                <section class="row-fluid marginbottom25 widget_categories">
                    <header class="row-fluid border-bottom marginbottom15">
                        <h1 class="h1-header"><?php _e('Act type','leiref'); ?></h1>
                    </header>
                    <ul>
                        <?php foreach ( $act_type_list as $type) { ?>
                            <?php
                                $filter_link = '?';
                                if ($query != ''){
                                    $filter_link .= 'q=' . $query . '&';
                                }
                                $filter_link .= 'filter=act_type:"' . $type[0] . '"';
                                if ($user_filter != ''){
                                    $filter_link .= ' AND ' . $user_filter ;
                                }
                            ?>
                            <li class="cat-item">
                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($type[0], $site_language); ?></a>
                                <span class="cat-item-count"><?php echo $type[1] ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                </section>
            <?php endif; ?>
            <?php if (count($publication_year) > 0) :?>
                <section class="row-fluid marginbottom25 widget_categories">
                    <header class="row-fluid border-bottom marginbottom15">
                        <h1 class="h1-header"><?php _e('Year','leiref'); ?></h1>
                    </header>
                    <ul>
                        <?php foreach ( $publication_year as $year) { ?>
                            <?php
                                $filter_link = '?';
                                if ($query != ''){
                                    $filter_link .= 'q=' . $query . '&';
                                }
                                $filter_link .= 'filter=publication_year:"' . $year[0] . '"';
                                if ($user_filter != ''){
                                    $filter_link .= ' AND ' . $user_filter ;
                                }
                            ?>
                            <li class="cat-item">
                                <a href='<?php echo $filter_link; ?>'><?php echo $year[0] ?></a>
                                <span class="cat-item-count"><?php echo $year[1] ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                </section>
            <?php endif; ?>

			<?php dynamic_sidebar('leisref-home');?>

            </aside>
			<div class="spacer"></div>
		</div>
	</div>
<?php get_footer();?>
