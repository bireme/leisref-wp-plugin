<?php
    $document_url = '#';
    $detail_page = (isset($resource_id) ? true: false);
?>

<div class="row-fluid">
    <h2 class="h2-loop-tit">
        <a href="<?php echo real_site_url($leisref_plugin_slug); ?>resource/?id=<?php echo $resource->id; ?>">
            <?php 
                if (isset($resource->title)) {
                if ($resource->title) : ?>
                <?php echo $resource->title ?>
            <?php else: ?>
                <?php leisref_print_lang_value($resource->act_type, $site_language); ?>
                Nº <?php echo $resource->act_number[0]; ?>
                <?php
                    if ($resource->issue_date[0]) {
                        echo '- ' . format_act_date($resource->issue_date[0], $site_language);
                    }
                ?>
            <?php endif; ?>
            <?php }else{ ?>
                <?php leisref_print_lang_value($resource->act_type, $site_language); ?>
                Nº <?php echo $resource->act_number[0]; ?>
                <?php
                    if ($resource->issue_date[0]) {
                        echo '- ' . format_act_date($resource->issue_date[0], $site_language);
                    }
                ?>
            <?php } ?>
        </a>
    </h2>
</div>

<div class="row-fluid">
    <?php /*
    if ($resource->official_ementa){ 
        echo $resource->official_ementa[0];
    }elseif($resource->unofficial_ementa){ 
        echo $resource->unofficial_ementa[0];
    }*/?>

    <?php
    /*Ajuste de ementa usando coalescencia */
    $ementa = $resource->official_ementa[0] ?? $resource->unofficial_ementa[0] ?? null;
    if (isset($ementa) && $ementa !== '') echo $ementa;
    ?>
</div>

<?php if ($resource->source_name): ?>
    <div class="row-fluid">
        <?php _e('Source','leisref'); ?>: <strong><?php leisref_print_lang_value($resource->source_name, $site_language) ;?></strong>
    </div>
<?php endif; ?>

<?php if ($resource->publication_date) : ?>
    <div class="row-fluid">
        <?php _e('Publication date','leisref'); ?>:
        <strong><?php echo format_date($resource->publication_date); ?></strong>
    </div>
<?php endif; ?>

<?php if (isset($resource->organ_issuer) && $resource->organ_issuer): ?>
    <div class="row-fluid">
        <?php _e('Organ issuer','leisref'); ?>: <strong><?php leisref_print_lang_value($resource->organ_issuer[0], $site_language);?></strong>
    </div>
<?php endif; ?>


<?php if ($resource->scope) : ?>
    <div class="row-fluid">
        <?php _e('Act scope','leisref'); ?>:
        <strong>
        <?php leisref_print_lang_value($resource->scope, $site_language);?>
        <?php if (isset($resource->scope_state) && $resource->scope_state) { echo ' - '; leisref_print_lang_value($resource->scope_state, $site_language); } ?>
        <?php if (isset($resource->scope_city) && $resource->scope_city) { echo ' - '; leisref_print_lang_value($resource->scope_city, $site_language); } ?>
        <?php if ($resource->scope_region) echo ' / '; leisref_print_lang_value($resource->scope_region, $site_language) ;?>
        </strong>
    </div>
<?php endif; ?>

<?php if ($resource->language && $detail_page) : ?>
    <div class="row-fluid">
        <?php _e('Language','leisref'); ?>:
        <strong><?php leisref_print_lang_value($resource->language, $site_language);?></strong>
    </div>
<?php endif; ?>

<?php if (isset($resource->collection) && $resource->collection && $detail_page) : ?>
    <div class="row-fluid">
        <?php _e('Collection','leisref'); ?>:
        <strong><?php leisref_print_lang_value($resource->collection, $site_language);?></strong>
    </div>
<?php endif; ?>


<?php if (isset($resource->relationship_active)): ?>
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
                $rel_act_title = $rel_parts[6];
            ?>
            <?php
                leisref_print_lang_value($rel_relation, $site_language);
                if ($rel_act_link != ''){
                    echo '<a href="' . real_site_url($leisref_plugin_slug) . 'resource/?id=' . $rel_act_link . '">';
                }
                echo '&nbsp';
                leisref_print_lang_value($rel_act_type, $site_language);
                echo '&nbsp';
                if ($rel_act_title){
                    echo $rel_act_title;
                }else{
                    echo $rel_act_number;
                }
                if ($rel_act_date != 'None'){
                    echo ', ' . __('of', 'leisref'). '&nbsp';
                    echo format_act_date($rel_act_date, $site_language) . '&nbsp';
                }
                if ($rel_act_link != ''){
                    echo '</a>';
                }
                if ($rel_act_apparatus != ''){
                    echo ' (' . $rel_act_apparatus . ')' ;
                }
            ?>
        </div>
    <?php } ?>
<?php endif; ?>
<?php endif; ?>

<?php if (isset($resource->relationship_passive)): ?>
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
                $rel_act_title = $rel_parts[5];
                $rel_act_apparatus = $rel_parts[6];
            ?>
            <?php
                if ($rel_act_apparatus != ''){
                    echo '(' . $rel_act_apparatus . ') ';
                }
                leisref_print_lang_value($rel_relation, $site_language);
                if ($rel_act_link != ''){
                    echo '<a href="' . real_site_url($leisref_plugin_slug) . 'resource/?id=' . $rel_act_link . '">';
                }
                echo '&nbsp';
                leisref_print_lang_value($rel_act_type, $site_language);
                echo '&nbsp';
                if ($rel_act_title){
                    echo $rel_act_title;
                }else{
                    echo $rel_act_number;
                }
                if ($rel_act_date != 'None'){
                    echo ', ' . __('of', 'leisref'). '&nbsp';
                    echo format_act_date($rel_act_date, $site_language) . '&nbsp';
                }
                if ($rel_act_link != ''){
                    echo '</a>';
                }
            ?>
        </div>
    <?php } ?>
<?php endif; ?>
<?php endif; ?>


<?php if ((isset($resource->descriptor) && $resource->descriptor) || (isset($resource->keyword) && $resource->keyword)) : ?>
    <div id="conteudo-loop-tags" class="row-fluid margintop10">
        <i class="ico-tags"> </i>
        <?php
            $subjects = array();
            foreach ( $resource->descriptor as $index => $subject ):
                echo "<a href='" . real_site_url($leisref_plugin_slug) . "?q=descriptor:\"" . $subject . "\"'>" . $subject . "</a>";
                echo $index != count($resource->descriptor)-1 ? ', ' : '';
            endforeach;
        ?>
    </div>
<?php endif; ?>

<?php if (!$detail_page) : ?>
    <a href="<?php echo real_site_url($leisref_plugin_slug); ?>resource/?id=<?php echo $resource->id; ?>" class="read-more" style="float:right;"><?php _e('Read more','leisref') ?></a>
<?php endif; ?>


<?php if ($resource->fulltext): ?>
    <div class="row-fluid">
        <?php foreach ( $resource->fulltext as $fulltext) : ?>
            <?php
                $document_url_parts = explode("|", $fulltext);
                $document_lang = $document_url_parts[0];
                $document_url = $document_url_parts[1];
                $document_path = parse_url($document_url, PHP_URL_PATH);
                $document_ext = pathinfo($document_path, PATHINFO_EXTENSION);
                $extension_list = array('pdf', 'html', 'htm', 'doc');
            ?>
            <?php if (  $document_ext == '' || in_array($document_ext, $extension_list) ) :?>
                <span class="more">
                    <a href="<?php echo $document_url ?>" target="_blank">
                        <?php _e('Text in','leisref') ?> <?php echo $fulltext_lang[$document_lang] ?>
                        <?php if ($document_ext) echo ' (' . strtoupper($document_ext) . ')';?>
                    </a>
                </span>&nbsp;&nbsp;
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
