<?php
/*
Template Name: LeisRef Detail
*/

global $leisref_service_url, $leisref_plugin_slug;

$leisref_config = get_option('leisref_config');
$request_uri = $_SERVER["REQUEST_URI"];
$request_parts = explode('/', $request_uri);
$leisref_id = end($request_parts);

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

$leisref_addthis_id = $leisref_config['addthis_profile_id'];
$leisref_service_request = $leisref_service_url . 'api/leisref/search/?id=' . $leisref_id . '&op=related&lang=' . $lang;

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
                <a href="<?php echo real_site_url(); ?>"><?php _e('Home','leisref'); ?></a> >
                <a href="<?php echo real_site_url($leisref_plugin_slug); ?>"><?php _e('Legislation Directory', 'leisref') ?> </a> >
                <?php _e('Act','leisref'); ?>
            </div>

            <section id="conteudo">
                <header class="row-fluid border-bottom">
                    <h1 class="h1-header"><?php echo $resource->title; ?></h1>
                </header>
                <div class="row-fluid">
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
                                            echo '<a href="' . $rel_act_link . '">';
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
                                            echo '<a href="' . $rel_act_link . '">';
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

                        <footer class="row-fluid margintop05">
                            <ul class="conteudo-loop-icons">
                                <li class="conteudo-loop-icons-li">
                                    <i class="ico-compartilhar"> </i>
                                    <!-- AddThis Button BEGIN -->
                                    <a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=300&amp;pubid=<?php echo $leisref_addthis_id; ?>"><?php _e('Share','leisref'); ?></a>
                                    <script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
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
