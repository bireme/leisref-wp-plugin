<?php
/*
Template Name: LeisRef Home
*/
global $leisref_service_url, $leisref_plugin_slug, $leisref_plugin_title, $leisref_texts;

require_once(LEISREF_PLUGIN_PATH . '/lib/Paginator.php');

$leisref_config = get_option('leisref_config');
$leisref_initial_filter = $leisref_config['initial_filter'];

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

$query = ( isset($_GET['s']) ? $_GET['s'] : $_GET['q'] );
$query = stripslashes($query);
$user_filter = stripslashes($_GET['filter']);
$page = ( isset($_GET['page']) ? $_GET['page'] : 1 );
$total = 0;
$count = 1;
$filter = '';

if ($leisref_initial_filter != ''){
   $filter = $leisref_initial_filter;
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
    $scope_region_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->scope_region;
    $language_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->language;
    $collection_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->collection;
    $publication_year = $response_json->diaServerResponse[0]->facet_counts->facet_fields->publication_year;
}

$home_url = isset($leisref_config['home_url_' . $lang]) ? $leisref_config['home_url_' . $lang] : real_site_url();
$plugin_breadcrumb = isset($leisref_config['plugin_title_' . $lang]) ? $leisref_config['plugin_title_' . $lang] : $leisref_config['plugin_title'];

$fulltext_lang['pt-br'] = __('Portuguese','leisref');
$fulltext_lang['es'] = __('Spanish','leisref');
$fulltext_lang['en'] = __('English','leisref');

?>

<?php get_header('leisref');?>

    <div id="content" class="row-fluid">
	  <div class="ajusta2">
          <div class="row-fluid breadcrumb">
              <a href="<?php echo $home_url ?>"><?php _e('Home','leisref'); ?></a> >
              <?php if ($query == '' && $filter == ''): ?>
                  <?php echo $plugin_breadcrumb ?>
              <?php else: ?>
                  <a href="<?php echo real_site_url($biblio_plugin_slug); ?>"><?php echo $plugin_breadcrumb ?> </a> >
                  <?php _e('Advanced Search', 'leisref') ?>
              <?php endif; ?>
          </div>
          <!-- Start sidebar leisref-header -->
          <div class="row-fluid">
              <?php dynamic_sidebar('leisref-header');?>
          </div>
          <div class="spacer"></div>
          <!-- end sidebar leisref-header -->
            <section class="header-search">

            <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($leisref_plugin_slug); ?>">
              <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
              <input type="hidden" name="sort" id="sort" value="<?php echo $_GET['sort']; ?>">
              <input type="hidden" name="format" id="format" value="<?php echo $format ? $format : 'summary'; ?>">
              <input type="hidden" name="count" id="count" value="<?php echo $count; ?>">

              <input value='<?php echo $query; ?>' name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Enter one or more words', 'leisref'); ?>">
              <input id="searchsubmit" value="<?php _e('Search', 'leisref'); ?>" type="submit">

              <?php
                $order = explode(';', $leisref_config['available_filter']);
                foreach($order as $index=>$content) {
                  $content = trim($content);
              ?>

                <?php if ($content == 'Act type') :  ?>
                  <div class="row-fluid widget_categories">
                      <div class="row-fluid border-bottom">
                          <h1 class="h1-header"><?php echo translate_label($leisref_texts, 'act_type', 'filter'); ?></h1>
                      </div>
                      <ul>
                          <?php foreach ($act_type_list as $type) { ?>
                              <li class="cat-item">
                                <input name="advanced_filter[act_type][]" value="<?php echo $type[0] ?>" type="checkbox">
                                <?php print_lang_value($type[0], $site_language)?>
                              </li>
                          <?php } ?>
                      </ul>
                  </div>
                <?php endif; ?>

                <?php if ($content == 'Collection') :  ?>
                  <div class="row-fluid widget_categories">
                      <header class="row-fluid border-bottom">
                          <h1 class="h1-header"><?php echo translate_label($leisref_texts, 'collection', 'filter'); ?></h1>
                      </header>
                      <ul>
                          <?php foreach ($collection_list as $collection) { ?>
                              <li class="cat-item">
                                <input name="advanced_filter[collection][]" value="<?php echo $collection[0] ?>" type="checkbox">
                                <?php print_lang_value($collection[0], $site_language)?>
                              </li>
                          <?php } ?>
                      </ul>
                  </div>
                <?php endif; ?>


              <?php } ?>


              <div class="row-fluid">
                <input id="searchsubmit" value="<?php _e('Search', 'leisref'); ?>" type="submit">
              </div>
            </form>

</div>
<div class="spacer"></div>

<?php get_footer();?>
