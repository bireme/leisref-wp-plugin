<?php
$detail_page = (isset($resource_id) ? true: false);
?>

<div class="row-fluid">
    <h2 class="h2-loop-tit">
        <a href="<?php echo real_site_url($leisref_plugin_slug); ?>resource/?id=<?php echo $resource->id; ?>">
            <?php if ($resource->title) : ?>
                <?php echo $resource->title ?>
            <?php else: ?>
                <?php print_lang_value($resource->act_type, $site_language); ?>
                nº <?php echo $resource->act_number[0]; ?>
                <?php
                    if ($resource->issue_date[0]) {
                        echo '- ' . format_act_date($resource->issue_date[0], $lang);
                    }
                ?>
            <?php endif; ?>
        </a>
    </h2>
</div>

<?php if ($resource->official_ementa): ?>
    <div class="row-fluid">
        <?php echo $resource->official_ementa[0];?>
    </div>
<?php endif; ?>

<?php if ($resource->scope_region): ?>
    <div class="row-fluid">
        <?php _e('Act country/region','leisref'); ?>: <strong><?php print_lang_value($resource->scope_region, $lang) ;?></strong>
    </div>
<?php endif; ?>


<?php if ($resource->organ_issuer): ?>
    <div class="row-fluid">
        <?php _e('Organ issuer','leisref'); ?>: <strong><?php print_lang_value($resource->organ_issuer[0], $lang);?></strong>
    </div>
<?php endif; ?>

<?php if ($resource->scope && $detail_page) : ?>
    <div class="row-fluid">
        <?php _e('Act scope','leisref'); ?>:
        <strong>
        <?php print_lang_value($resource->scope, $lang);?>
        <?php echo ($resource->scope_state != ''  ? ' - ' . $resource->scope_state[0] : ''); ?>
        <?php echo ($resource->scope_city != ''  ? '- ' . $resource->scope_city[0] : ''); ?>
        </strong>
    </div>
<?php endif; ?>

<?php if ($resource->source_name && $detail_page): ?>
    <div class="row-fluid">
        <?php _e('Source','leisref'); ?>: <strong><?php print_lang_value($resource->source_name, $lang) ;?></strong>
    </div>
<?php endif; ?>

<?php if ($resource->language && $detail_page) : ?>
    <div class="row-fluid">
        <?php _e('Language','leisref'); ?>:
        <strong><?php print_lang_value($resource->language, $lang);?></strong>
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
        <?php
            $subjects = array();
            foreach ( $resource->descriptor as $index => $subject ):
                echo "<a href='" . real_site_url($leisref_plugin_slug) . "?q=descriptor:\"" . $subject . "\"'>" . $subject . "</a>";
                echo $index != count($resource->descriptor)-1 ? ', ' : '';
            endforeach;
        ?>
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
                    <?php _e('Text in','leisref') ?> <?php echo $fulltext_lang[$document_lang] ?>
                </a>
            </span>&nbsp;&nbsp;
        <?php } ?>
    </div>
<?php endif; ?>
