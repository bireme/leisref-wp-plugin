<?php
function leisref_page_admin() {
    $config = get_option('leisref_config');

?>
    <div class="wrap">
            <div id="icon-options-general" class="icon32"></div>
            <h2><?php _e('Legislation settings', 'leisref'); ?></h2>

            <form method="post" action="options.php">

                <?php settings_fields('leisref-settings-group'); ?>

                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><?php _e('Plugin page', 'leisref'); ?>:</th>
                            <td><input type="text" name="leisref_config[plugin_slug]" value="<?php echo ($config['plugin_slug'] != '' ? $config['plugin_slug'] : 'leisref'); ?>" class="regular-text code"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Filter query', 'leisref'); ?>:</th>
                            <td><input type="text" name="leisref_config[initial_filter]" value='<?php echo $config['initial_filter'] ?>' class="regular-text code"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('AddThis profile ID', 'leisref'); ?>:</th>
                            <td><input type="text" name="leisref_config[addthis_profile_id]" value="<?php echo $config['addthis_profile_id'] ?>" class="regular-text code"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Google Analytics code', 'leisref'); ?>:</th>
                            <td><input type="text" name="leisref_config[google_analytics_code]" value="<?php echo $config['google_analytics_code'] ?>" class="regular-text code"></td>
                        </tr>

                        <?php
                        if ( function_exists( 'pll_the_languages' ) ) {
                            $available_languages = pll_languages_list();
                            $available_languages_name = pll_languages_list(array('fields' => 'name'));
                            $count = 0;
                            foreach ($available_languages as $lang) {
                                $key_name = 'plugin_title_' . $lang;
                                $home_url = 'home_url_' . $lang;

                                echo '<tr valign="top">';
                                echo '    <th scope="row"> ' . __("Home URL", "leisref") . ' (' . $available_languages_name[$count] . '):</th>';
                                echo '    <td><input type="text" name="leisref_config[' . $home_url . ']" value="' . $config[$home_url] . '" class="regular-text code"></td>';
                                echo '</tr>';

                                echo '<tr valign="top">';
                                echo '    <th scope="row"> ' . __("Page title", "leisref") . ' (' . $available_languages_name[$count] . '):</th>';
                                echo '    <td><input type="text" name="leisref_config[' . $key_name . ']" value="' . $config[$key_name] . '" class="regular-text code"></td>';
                                echo '</tr>';
                                $count++;
                            }
                        }else{
                            echo '<tr valign="top">';
                            echo '   <th scope="row">' . __("Page title", "leisref") . ':</th>';
                            echo '   <td><input type="text" name="leisref_config[plugin_title]" value="' . $config["plugin_title"] . '" class="regular-text code"></td>';
                            echo '</tr>';
                        }
                        ?>

                        <tr valign="top">
                            <th scope="row"><?php _e('Sources for similar documents', 'leisref'); ?>:</th>
                            <td>
                                <input type="text" name="leisref_config[default_filter_db]" value='<?php echo $config['default_filter_db']; ?>' class="regular-text code">
                                <small style="display: block;">* <?php _e('The database names must be separated by commas.', 'leisref'); ?></small>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Sources for related documents', 'leisref'); ?>:</th>
                            <td>
                                <input type="text" name="leisref_config[extra_filter_db]" value='<?php echo $config['extra_filter_db']; ?>' class="regular-text code">
                                <small style="display: block;">* <?php _e('The database names must be separated by commas.', 'leisref'); ?></small>
                            </td>
                        </tr>
                        <tr valign="top">
                          <th scope="row">
                            <?php _e('Page layout', 'leisref'); ?>:
                          </th>
                          <td>
                            <label for="whole_page">
                              <input type="radio" id="whole_page" value="whole_page" name="leisref_config[page_layout]"  <?php if($config['page_layout'] == 'whole_page' ){ echo 'checked'; }?>>
                              <?php _e('Show filters as whole page', 'leisref'); ?>

                            </label>
                            <br>
                            <br>
                            <label for="normal_page">
                              <input type="radio" id="normal_page" value="normal_page" name="leisref_config[page_layout]" <?php if(!isset($config['page_layout']) || $config['page_layout'] == 'normal_page' ){ echo 'checked'; }?> >
                              <?php _e('Show normal page', 'leisref'); ?>

                            </label>
                          </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><?php _e('Search filters', 'leisref');?>:</th>

                            <?php
                                $available_filters = 'Subject;Act type;Scope;Country/region;State;Collection;Language;Year;Database';
                                $available_filter_list = explode(';', $available_filters);
                                if(!isset($config['available_filter'])){
                                    $order = $available_filter_list;
                                } else {
                                    $order = array_filter(explode(';', $config['available_filter']));
                                }
                            ?>

                            <td>
                                <table border=0>
                                    <tr>
                                        <td>
                                            <p align="left"><?php _e('Available', 'leisref');?><br>
                                                <ul id="sortable1" class="droptrue">
                                                    <?php foreach ($available_filter_list as $key => $value) : ?>
                                                        <?php if ( !in_array($value, $order) ) : ?>
                                                            <?php echo '<li class="ui-state-default" id="'.$value.'">'.translate($value,'direve').'</li>'; ?>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </p>
                                        </td>
                                        <td>
                                            <p align="left"><?php _e('Selected', 'leisref');?> <br>
                                                <ul id="sortable2" class="sortable-list">
                                                    <?php
                                                        foreach ($order as $index => $item) {
                                                            $item = trim($item); // Important
                                                            echo '<li class="ui-state-default" id="'.$item.'">'.translate($item ,'leisref').'</li>';
                                                        }
                                                    ?>
                                                </ul>
                                                <input type="hidden" id="order_aux" name="leisref_config[available_filter]" value="<?php echo trim($config['available_filter']); ?> " >
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save changes') ?>" />
                </p>
            </form>
        </div>
        <script type="text/javascript">
            var $j = jQuery.noConflict();

            $j( function() {
              $j( "ul.droptrue" ).sortable({
                connectWith: "ul"
              });

              $j('.sortable-list').sortable({

                connectWith: 'ul',
                update: function(event, ui) {
                  var changedList = this.id;
                  var order = $j(this).sortable('toArray');
                  var positions = order.join(';');
                  $j('#order_aux').val(positions);

                }
              });
            } );
        </script>
<?php
}
?>
