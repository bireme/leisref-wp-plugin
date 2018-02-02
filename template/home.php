<?php
/*
Template Name: LeisRef Home
*/
global $leisref_service_url, $leisref_plugin_slug, $leisref_plugin_title, $leisref_texts;

require_once(LEISREF_PLUGIN_PATH . '/lib/Paginator.php');

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

if ( $user_filter != '' ) {
    $user_filter_list = preg_split("/ AND /", $user_filter);
    $applied_filter_list = array();
    foreach($user_filter_list as $filter){
        preg_match('/([a-z_]+):(.+)/',$filter, $filter_parts);
        if ($filter_parts){
            // convert to internal format
            $applied_filter_list[$filter_parts[1]][] = str_replace('"', '', $filter_parts[2]);
        }
    }
}

$response = @file_get_contents($leisref_search);
if ($response){
    $response_json = json_decode($response);
    //var_dump($response_json);
    $total = $response_json->diaServerResponse[0]->response->numFound;
    $start = $response_json->diaServerResponse[0]->response->start;
    $legislation_list = $response_json->diaServerResponse[0]->response->docs;

    $descriptor_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->descriptor_filter;
    $act_type_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->act_type;
    $scope_region_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->scope_region;
    $language_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->language;
    $collection_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->collection;
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
    <div class="row-fluid breadcrumb">
        <a href="<?php echo $home_url ?>"><?php _e('Home','leisref'); ?></a> >
        <?php if ($query == '' && $filter == ''): ?>
            <?php echo $leisref_plugin_title ?>
        <?php else: ?>
            <a href="<?php echo real_site_url($leisref_plugin_slug); ?>"><?php echo $leisref_plugin_title ?> </a> >
            <?php _e('Search result', 'leisref') ?>
        <?php endif; ?>
    </div>

	<div id="content" class="row-fluid">
		<div class="ajusta2">
            <section class="header-search">
                <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($leisref_plugin_slug); ?>">
                    <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
                    <input type="hidden" name="sort" id="sort" value="<?php echo $_GET['sort']; ?>">
                    <input type="hidden" name="format" id="format" value="<?php echo $format ? $format : 'summary'; ?>">
                    <input type="hidden" name="count" id="count" value="<?php echo $count; ?>">
                    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
                    <input value='<?php echo $query; ?>' name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Enter one or more words', 'leisref'); ?>">
                    <input id="searchsubmit" value="<?php _e('Search', 'leisref'); ?>" type="submit">
                </form>
                <div class="pull-right rss">
                    <a href="<?php echo $feed_url ?>" target="blank"><img src="<?php echo LEISREF_PLUGIN_URL; ?>template/images/icon_rss.png" ></a>
                </div>
            </section>
            <div class="content-area result-list">
    			<section id="conteudo">
                    <?php if ( isset($total) && strval($total) == 0) :?>
                        <h1 class="h1-header"><?php _e('No results found','leisref'); ?></h1>
                    <?php else :?>
        				<header class="row-fluid border-bottom">
    					   <h1 class="h1-header"><?php _e('Total','leisref'); ?>: <?php echo $total; ?></h1>
        				</header>
        				<div class="row-fluid">
                            <?php foreach ( $legislation_list as $resource) { ?>
        					    <article class="conteudo-loop">
                                    <?php include('metadata.php') ?>
            					</article>
                            <?php } ?>
        				</div>
                        <div class="row-fluid">
                            <?php echo $pages->display_pages(); ?>
                        </div>
                    <?php endif; ?>
    			</section>
    			<aside id="sidebar">

                    <?php dynamic_sidebar('leisref-home');?>

                    <?php if (strval($total) > 0) :?>
                        <div id="filter-link" style="display: none">
                            <div class="mobile-menu" onclick="animateMenu(this)">
                                <a href="javascript:showHideFilters()">
                                    <div class="menu-bar">
                                        <div class="bar1"></div>
                                        <div class="bar2"></div>
                                        <div class="bar3"></div>
                                    </div>
                                    <div class="menu-item">
                                        <?php _e('Filters','leisref') ?>
                                    </div>
                                </a>
                           </div>
                        </div>

                        <div id="filters">
                            <?php if ($applied_filter_list) :?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid marginbottom15">
                                        <h1 class="h1-header"><?php echo _e('Selected filters', 'leisref') ?></h1>
                                    </header>
                                    <form method="get" name="searchFilter" id="formFilters" action="<?php echo real_site_url($leisref_plugin_slug); ?>">
                                        <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
                                        <input type="hidden" name="sort" id="sort" value="<?php echo $sort; ?>">
                                        <input type="hidden" name="format" id="format" value="<?php echo $format; ?>">
                                        <input type="hidden" name="count" id="count" value="<?php echo $count; ?>">
                                        <input type="hidden" name="q" id="query" value="<?php echo $query; ?>" >
                                        <input type="hidden" name="filter" id="filter" value="" >

                                        <?php foreach ( $applied_filter_list as $filter => $filter_values ) :?>
                                            <h2><?php echo translate_label($leisref_texts, $filter, 'filter') ?></h2>
                                            <ul>
                                            <?php foreach ( $filter_values as $value ) :?>
                                                <input type="hidden" name="apply_filter" class="apply_filter"
                                                        id="<?php echo md5($value) ?>" value='<?php echo $filter . ':"' . $value . '"'; ?>' >
                                                <li>
                                                    <span class="filter-item">
                                                        <?php
                                                            if ($filter != 'descriptor' && $filter != 'publication_year'){
                                                                echo print_lang_value($value, $site_language);
                                                            }else{
                                                                echo $value;
                                                            }
                                                        ?>
                                                    </span>
                                                    <span class="filter-item-del">
                                                        <a href="javascript:remove_filter('<?php echo md5($value) ?>')">
                                                            <img src="<?php echo LEISREF_PLUGIN_URL; ?>template/images/del.png">
                                                        </a>
                                                    </span>
                                                </li>
                                            <?php endforeach; ?>
                                            </ul>
                                        <?php endforeach; ?>
                                    </form>
                                </section>
                            <?php endif; ?>

                            <?php if ( in_array('descriptor', $leisref_config['available_filter']) && $descriptor_list ): ?>
                			    <section class="row-fluid marginbottom25 widget_categories">
                					<header class="row-fluid border-bottom marginbottom15">
                						<h1 class="h1-header"><?php echo translate_label($leisref_texts, 'descriptor', 'filter') ?></h1>
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

                            <?php if ( in_array('act_type', $leisref_config['available_filter']) && $act_type_list ): ?>
                                <section class="row-fluid marginbottom25 widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($leisref_texts, 'act_type', 'filter') ?></h1>
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
                                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($type[0], $site_language)?></a>
                                                <span class="cat-item-count"><?php echo $type[1] ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                            <?php if ( in_array('scope_region', $leisref_config['available_filter']) && $scope_region_list ): ?>
                                <section class="row-fluid marginbottom25 widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($leisref_texts, 'scope_region', 'filter') ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $scope_region_list as $region) { ?>
                                            <?php
                                                $filter_link = '?';
                                                if ($query != ''){
                                                    $filter_link .= 'q=' . $query . '&';
                                                }
                                                $filter_link .= 'filter=scope_region:"' . $region[0] . '"';
                                                if ($user_filter != ''){
                                                    $filter_link .= ' AND ' . $user_filter ;
                                                }
                                            ?>
                                            <li class="cat-item">
                                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($region[0], $site_language)?></a>
                                                <span class="cat-item-count"><?php echo $region[1] ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                            <?php if ( in_array('collection', $leisref_config['available_filter']) && $collection_list ): ?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($leisref_texts, 'collection', 'filter'); ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $collection_list as $collection ) { ?>
                                            <li class="cat-item">
                                                <?php
                                                    $filter_link = '?';
                                                    if ($query != ''){
                                                        $filter_link .= 'q=' . $query . '&';
                                                    }
                                                    $filter_link .= 'filter=collection:"' . $collection[0] . '"';
                                                    if ($user_filter != ''){
                                                        $filter_link .= ' AND ' . $user_filter ;
                                                    }
                                                ?>
                                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($collection[0], $site_language); ?></a>
                                                <span class="cat-item-count"><?php echo $collection[1]; ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                            <?php if ( in_array('language', $leisref_config['available_filter']) && $language_list ): ?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($leisref_texts, 'language', 'filter'); ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $language_list as $lang ) { ?>
                                            <li class="cat-item">
                                                <?php
                                                    $filter_link = '?';
                                                    if ($query != ''){
                                                        $filter_link .= 'q=' . $query . '&';
                                                    }
                                                    $filter_link .= 'filter=language:"' . $lang[0] . '"';
                                                    if ($user_filter != ''){
                                                        $filter_link .= ' AND ' . $user_filter ;
                                                    }
                                                ?>
                                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($lang[0], $site_language); ?></a>
                                                <span class="cat-item-count"><?php echo $lang[1]; ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                            <?php if (count($publication_year) > 0) :?>
                                <section class="row-fluid marginbottom25 widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($leisref_texts, 'year', 'filter') ?></h1>
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

                    <?php endif; ?>

                </aside>
    			<div class="spacer"></div>
            </div> <!-- close DIV.result-area -->
		</div> <!-- close DIV.ajusta2 -->
	</div>
<?php get_footer();?>