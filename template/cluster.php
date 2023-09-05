<?php

ini_set('display_errors', '0');

$lang = $_POST['lang'];
$site_lang = $_POST['site_lang'];
$query = $_POST['query'];
$filter = $_POST['filter'];
$user_filter = $_POST['uf'];
$act_number = $_POST['act_number'];
$fb = $_POST['fb'];
$cluster = $_POST['cluster'];
$cluster_fb = ( $_POST['cluster'] ) ? $_POST['cluster'].':'.$fb : '';
$count = 1;

$leisref_search = $leisref_service_url . 'api/leisref/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&fb=' . $cluster_fb . '&lang=' . $lang . '&count=' . $count;

// echo "<pre>"; print_r($leisref_search); echo "</pre>"; die();

$response = @file_get_contents($leisref_search);
if ($response){
    $response_json = json_decode($response);
    // echo "<pre>"; print_r($response_json); echo "</pre>"; die();
    $total = $response_json->diaServerResponse[0]->response->numFound;
    $descriptor_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->descriptor_filter;
    $act_type_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->act_type;
    $scope_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->scope;
    $scope_region_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->scope_region;
    $scope_state_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->scope_state;
    $language_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->language;
    $collection_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->collection;
    $publication_year = $response_json->diaServerResponse[0]->facet_counts->facet_fields->publication_year;
    $database_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->indexed_database;
}

?>

<?php if($cluster == 'collection'): ?>
    <ul class="filter-list">
        <?php foreach ( $collection_list as $collection ) { ?>
            <li class="cat-item">
                <?php $filter_link = mount_filter_link('collection', $collection[0], $query, $user_filter, $act_number); ?>
                <a href='<?php echo $filter_link; ?>'><?php leisref_print_lang_value($collection[0], $site_lang); ?></a>
                <span class="cat-item-count"><?php echo $collection[1]; ?></span>
            </li>
        <?php } ?>
    </ul>
<?php endif; ?>

<?php if($cluster == 'descriptor_filter'): ?>
    <ul class="filter-list">
        <?php foreach ( $descriptor_list as $descriptor ) { ?>
            <?php
                $filter_link = mount_filter_link('descriptor', $descriptor[0], $query, $user_filter, $act_number);
            ?>
            <?php if ( filter_var($descriptor[0], FILTER_VALIDATE_INT) === false ) : ?>
                <li class="cat-item">
                    <a href='<?php echo $filter_link; ?>'><?php echo $descriptor[0] ?></a>
                    <span class="cat-item-count"><?php echo $descriptor[1] ?></span>
                </li>
            <?php endif; ?>
        <?php } ?>
    </ul>
<?php endif; ?>

<?php if($cluster == 'act_type'): ?>
    <ul class="filter-list">
        <?php foreach ( $act_type_list as $type ) { ?>
            <?php
                $filter_link = mount_filter_link('act_type', $type[0], $query, $user_filter, $act_number);
            ?>
            <li class="cat-item">
                <a href='<?php echo $filter_link; ?>'><?php leisref_print_lang_value($type[0], $site_lang); ?></a>
                <span class="cat-item-count"><?php echo $type[1] ?></span>
            </li>
        <?php } ?>
    </ul>
<?php endif; ?>

<?php if($cluster == 'scope'): ?>
    <ul class="filter-list">
        <?php foreach ( $scope_list as $scope ) { ?>
            <?php
                $filter_link = mount_filter_link('scope', $scope[0], $query, $user_filter, $act_number);
            ?>
            <li class="cat-item">
                <a href='<?php echo $filter_link; ?>'><?php leisref_print_lang_value($scope[0], $site_lang); ?></a>
                <span class="cat-item-count"><?php echo $scope[1] ?></span>
            </li>
        <?php } ?>
    </ul>
<?php endif; ?>

<?php if($cluster == 'scope_region'): ?>
    <ul class="filter-list">
        <?php foreach ( $scope_region_list as $region ) { ?>
            <?php
                $filter_link = mount_filter_link('scope_region', $region[0], $query, $user_filter, $act_number);
            ?>
            <li class="cat-item">
                <a href='<?php echo $filter_link; ?>'><?php leisref_print_lang_value($region[0], $site_lang); ?></a>
                <span class="cat-item-count"><?php echo $region[1] ?></span>
            </li>
        <?php } ?>
    </ul>
<?php endif; ?>

<?php if($cluster == 'scope_state'): ?>
    <ul class="filter-list">
        <?php foreach ( $scope_state_list as $state ) { ?>
            <?php
                $filter_link = mount_filter_link('scope_state', $state[0], $query, $user_filter, $act_number);
            ?>
            <li class="cat-item">
                <a href='<?php echo $filter_link; ?>'><?php leisref_print_lang_value($state[0], $site_lang); ?></a>
                <span class="cat-item-count"><?php echo $state[1] ?></span>
            </li>
        <?php } ?>
    </ul>
<?php endif; ?>

<?php if($cluster == 'language'): ?>
    <ul class="filter-list">
        <?php foreach ( $language_list as $lang ) { ?>
            <li class="cat-item">
                <?php
                    $filter_link = mount_filter_link('language', $lang[0], $query, $user_filter, $act_number);
                ?>
                <a href='<?php echo $filter_link; ?>'><?php leisref_print_lang_value($lang[0], $site_lang); ?></a>
                <span class="cat-item-count"><?php echo $lang[1]; ?></span>
            </li>
        <?php } ?>
    </ul>
<?php endif; ?>

<?php if($cluster == 'publication_year'): ?>
    <ul class="filter-list">
        <?php foreach ( $publication_year as $year ) { ?>
            <?php
                $filter_link = mount_filter_link('publication_year', $year[0], $query, $user_filter, $act_number);
            ?>
            <li class="cat-item">
                <a href='<?php echo $filter_link; ?>'><?php echo $year[0] ?></a>
                <span class="cat-item-count"><?php echo $year[1] ?></span>
            </li>
        <?php } ?>
    </ul>
<?php endif; ?>

<?php if($cluster == 'indexed_database'): ?>
    <ul class="filter-list">
        <?php foreach ( $database_list as $db ) { ?>
            <?php
                $filter_link = mount_filter_link('indexed_database', $db[0], $query, $user_filter, $act_number);
            ?>
            <li class="cat-item">
                <a href='<?php echo $filter_link; ?>'><?php leisref_print_lang_value($db[0], $site_lang); ?></a>
                <span class="cat-item-count"><?php echo $db[1] ?></span>
            </li>
        <?php } ?>
    </ul>
<?php endif; ?>