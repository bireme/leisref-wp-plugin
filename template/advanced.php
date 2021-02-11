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

$leisref_search_url = $leisref_service_url . 'api/leisref/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&start=' . $start . '&lang=' . $lang;
$leisref_search_url.= "&facet.field=act_type&f.act_type.facet.limit=-1&facet.field=collection&f.collection.facet.limit=-1";
$leisref_search_url.= "&facet.field=scope&f.scope.facet.limit=-1&facet.field=scope_state&f.scope_state.facet.limit=-1";

$response = @file_get_contents($leisref_search_url);
if ($response){
    $response_json = json_decode($response);
    //var_dump($response_json);
    $total = $response_json->diaServerResponse[0]->response->numFound;
    $start = $response_json->diaServerResponse[0]->response->start;
    $legislation_list = $response_json->diaServerResponse[0]->response->docs;

    $act_type_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->act_type;
    $scope_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->scope;
    $scope_region_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->scope_region;
    $scope_state_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->scope_state;
    $collection_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->collection;
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

              <div class="row-fluid">
                <input value='<?php echo $query; ?>' name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Enter one or more words', 'leisref'); ?>">
                <input id="searchsubmit" value="<?php _e('Search', 'leisref'); ?>" type="submit">
              </div>

              <div class="row-fluid">
                <?php _e('Search by act number', 'leisref'); ?><br/>
                <input value="" name="act_number" class="input-search" id="act_number" type="text" placeholder="">
              </div>

              <?php
                $order = explode(';', $leisref_config['available_filter']);
                foreach($order as $index=>$content) {
                  $content = trim($content);
              ?>

                <?php if ($content == 'Act type') : ?>
                  <div class="row-fluid widget_categories">
                      <div class="row-fluid border-bottom">
                          <h1 class="h1-header"><?php echo translate_label($leisref_texts, 'act_type', 'filter'); ?></h1>
                      </div>
                      <ul class="two-columns-list">
                          <?php
                            $type_list_translated = translate_filter_options($act_type_list, $lang);
                          ?>
                          <?php foreach ($type_list_translated as $item) { ?>
                              <li class="cat-item">
                                <input name="advanced_filter[act_type][]" value="<?php echo $item['original'][0] ?>" type="checkbox" id="<?php echo $item['label'] ?>">
                                <label for="<?php echo $item['label']; ?>"><?php echo $item['label']; ?></label>
                              </li>
                          <?php } ?>
                      </ul>
                  </div>
                <?php endif; ?>

                <?php if ($content == 'State') :  ?>
                  <div class="row-fluid widget_categories">
                      <header class="row-fluid border-bottom">
                          <h1 class="h1-header"><?php echo translate_label($leisref_texts, 'scope_state', 'filter'); ?></h1>
                      </header>
                      <ul class="two-columns-list">
                          <?php
                              $state_list_translated = translate_filter_options($scope_state_list, $lang);
                          ?>
                          <?php foreach ($state_list_translated as $item) { ?>
                              <li class="cat-item">
                                <input name="advanced_filter[scope_state][]" value="<?php echo $item['original'][0] ?>" type="checkbox" id="<?php echo $item['label'] ?>">
                                <label for="<?php echo $item['label']; ?>"><?php echo $item['label']; ?></label>
                              </li>
                          <?php } ?>
                      </ul>
                  </div>
                <?php endif; ?>

                <?php if ($content == 'Scope') :  ?>
                  <div class="row-fluid widget_categories">
                      <header class="row-fluid border-bottom">
                          <h1 class="h1-header"><?php echo translate_label($leisref_texts, 'scope', 'filter'); ?></h1>
                      </header>
                      <ul class="two-columns-list">
                          <?php
                              $scope_list_translated = translate_filter_options($scope_list, $lang);
                          ?>
                          <?php foreach ($scope_list_translated as $item) { ?>
                              <li class="cat-item">
                                <input name="advanced_filter[scope][]" value="<?php echo $item['original'][0] ?>" type="checkbox" id="<?php echo $item['label'] ?>">
                                <label for="<?php echo $item['label']; ?>"><?php echo $item['label']; ?></label>
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
                      <ul class="two-columns-list">
                          <?php
                              $collection_list_translated = translate_filter_options($collection_list, $lang);
                          ?>
                          <?php foreach ($collection_list_translated as $item) { ?>
                              <li class="cat-item">
                                <input name="advanced_filter[collection][]" value="<?php echo $item['original'][0] ?>" type="checkbox" id="<?php echo $item['label'] ?>">
                                <label for="<?php echo $item['label']; ?>"><?php echo $item['label']; ?></label>
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
