<?php
/*
Template Name: LeisRef Detail
*/

global $leisref_service_url, $leisref_plugin_slug, $leisref_plugin_title, $leisref_texts;

$leisref_config = get_option('leisref_config');
$resource_id   = $_GET['id'];

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

$leisref_addthis_id = $leisref_config['addthis_profile_id'];
$leisref_service_request = $leisref_service_url . 'api/leisref/search/?id=' . $resource_id . '&op=related&lang=' . $lang;

$response = @file_get_contents($leisref_service_request);

if ($response){
    $response_json = json_decode($response);

    $resource = $response_json->diaServerResponse[0]->match->docs[0];
    $related_list = $response_json->diaServerResponse[0]->response->docs;
}

$fulltext_lang['pt-br'] = __('Portuguese','leisref');
$fulltext_lang['es'] = __('Spanish','leisref');
$fulltext_lang['en'] = __('English','leisref');

?>

<?php get_header('leisref'); ?>

<div id="content" class="row-fluid">
        <div class="ajusta2">
            <div class="row-fluid breadcrumb">
                <a href="<?php echo $home_url ?>"><?php _e('Home','leisref'); ?></a> >
                <a href="<?php echo real_site_url($leisref_plugin_slug); ?>"><?php echo $leisref_plugin_title ?> </a> >
                <?php _e('Act','leisref'); ?>
            </div>

            <section id="conteudo">
                <header class="row-fluid border-bottom">
                    <h1 class="h1-header"><?php echo $resource->title; ?></h1>
                </header>
                <div class="row-fluid">
                    <article class="conteudo-loop">
                        <?php include('metadata.php') ?>

                        <footer class="row-fluid margintop05">
                            <ul class="conteudo-loop-icons">
                                <li class="conteudo-loop-icons-li">
                                    <i class="ico-compartilhar"> </i>
                                    <!-- AddThis Button BEGIN -->
                                    <div class="addthis_toolbox addthis_default_style">
                                        <a class="addthis_button_facebook"></a>
                                        <a class="addthis_button_delicious"></a>
                                        <a class="addthis_button_google_plusone_share"></a>
                                        <a class="addthis_button_favorites"></a>
                                        <a class="addthis_button_compact"></a>
                                    </div>
                                    <script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>
                                    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $leisref_addthis_id; ?>"></script>
                                    <!-- AddThis Button END -->
                                </li>
                                <li class="conteudo-loop-icons-li">
                                    <!-- AddThisEvent Button BEGIN -->
                                    <script type="text/javascript" src="https://addthisevent.com/libs/1.5.8/ate.min.js"></script>
                                </li>
                            </ul>
                        </footer>
                    </article>
                </div>
            </section>
        <aside id="sidebar">
                    <section class="header-search">
                            <?php if ($leisref_config['show_form']) : ?>
                            <form role="search" method="get" id="searchform" action="<?php echo real_site_url($leisref_plugin_slug); ?>">
                                    <input value="<?php echo $query ?>" name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Search', 'leisref'); ?>...">
                                    <input id="searchsubmit" value="<?php _e('Search', 'leisref'); ?>" type="submit">
                            </form>
                            <?php endif; ?>
                    </section>
            </aside>
        </div>
    </div>

<?php get_footer();?>
