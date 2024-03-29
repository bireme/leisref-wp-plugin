<?php
/*
Template Name: LeisRef Detail
*/

global $leisref_service_url, $leisref_plugin_slug, $leisref_plugin_title, $leisref_texts, $similar_docs_url;

$leisref_config = get_option('leisref_config');
$resource_id = sanitize_text_field($_GET['id']);

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

$leisref_addthis_id = $leisref_config['addthis_profile_id'];
$leisref_service_request = $leisref_service_url . 'api/leisref/search/?id=' . $resource_id . '&op=related&lang=' . $lang;

$response = @file_get_contents($leisref_service_request);

if ($response){
    $response_json = json_decode($response);
    $resource = $response_json->diaServerResponse[0]->match->docs[0];
    // create param to find similars
    $similar_text = $resource->title;
    if (isset($resource->mj)){
        $similar_text .= ' ' . implode(' ', $resource->mj);
    }
    if (isset($resource->official_ementa)){
        $similar_text .= ' ' . $resource->official_ementa[0];
    }elseif (isset($resource->unofficial_ementa)){
        $similar_text .= ' ' . $resource->unofficial_ementa[0];
    }

    $similar_docs_url = $similar_docs_url . '?adhocSimilarDocs=' . urlencode($similar_text);
    $similar_docs_request = ( $leisref_config['default_filter_db'] ) ? $similar_docs_url . '&sources=' . $leisref_config['default_filter_db'] : $similar_docs_url;
    $similar_query = urlencode($similar_docs_request);
    $related_query = urlencode($similar_docs_url);
}

$home_url = isset($leisref_config['home_url_' . $lang]) ? $leisref_config['home_url_' . $lang] : real_site_url();
$plugin_breadcrumb = isset($leisref_config['plugin_title_' . $lang]) ? $leisref_config['plugin_title_' . $lang] : $leisref_config['plugin_title'];

$fulltext_lang['pt-br'] = __('Portuguese','leisref');
$fulltext_lang['es'] = __('Spanish','leisref');
$fulltext_lang['en'] = __('English','leisref');

?>

<?php get_header('leisref'); ?>

    <div id="content" class="row-fluid">
        <div class="ajusta2">
            <div class="row-fluid breadcrumb">
                <a href="<?php echo $home_url ?>"><?php _e('Home','leisref'); ?></a> >
                <a href="<?php echo real_site_url($leisref_plugin_slug); ?>"><?php echo $plugin_breadcrumb ?> </a> >
                <?php if ($resource->title) : ?>
                    <?php echo $resource->title ?>
                <?php else: ?>
                    <?php leisref_print_lang_value($resource->act_type, $site_language); ?>
                    Nº <?php echo $resource->act_number[0]; ?>
                    <?php
                        if ($resource->issue_date[0]) {
                            echo '- ' . format_act_date($resource->issue_date[0], $lang);
                        }
                    ?>
                <?php endif; ?>
            </div>

            <section id="conteudo">
                <a href="javascript:history.back()"><?php _e('Back','leisref'); ?></a> | <a href="<?php echo real_site_url($leisref_plugin_slug); ?>"><?php _e('New search','leisref'); ?></a>

                <header class="row-fluid border-bottom">
                    <h1 class="h1-header"><?php echo $resource->title; ?></h1>
                </header>
                <div class="row-fluid">
                    <article class="conteudo-loop">
                        <?php include('metadata.php') ?>

                        <footer class="row-fluid margintop05">
                            <i class="ico-compartilhar"><?php _e('Share','leisref'); ?></i>
                            <ul class="conteudo-loop-icons">
                                <li class="conteudo-loop-icons-li">
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
                <div class="row-fluid">
                    <div id="loader" class="loader" style="display: inline-block;"></div>
                </div>
                <div class="row-fluid">
                    <div id="async" class="related-docs">

                    </div>
                </div>
<?php
$sources = ( $leisref_config['extra_filter_db'] ) ? $leisref_config['extra_filter_db'] : '';
$url = LEISREF_PLUGIN_URL.'template/related.php?query='.$related_query.'&sources='.$sources.'&lang='.$lang;
?>
<script type="text/javascript">
    show_related("<?php echo $url; ?>");
</script>
            </section>
            <aside id="sidebar">
                <section class="row-fluid marginbottom25 widget_categories">
                    <header class="row-fluid border-bottom marginbottom15">
                        <h1 class="h1-header"><?php _e('Related articles','leisref'); ?></h1>
                    </header>
                    <ul id="ajax">

                    </ul>
<?php
$url = LEISREF_PLUGIN_URL.'template/similar.php?query='.$similar_query.'&lang='.$lang;
?>
<script type="text/javascript">
    show_similar("<?php echo $url; ?>");
</script>
                </section>
             </aside>
        </div>
    </div>

<?php get_footer();?>
