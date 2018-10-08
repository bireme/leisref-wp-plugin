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
                        <tr valign="top">
                            <th scope="row"><?php _e('Fulltext', 'leisref'); ?>:</th>
                            <td>
                                <label for="present_alternative_links">
                                    <input type="checkbox" name="leisref_config[alternative_links]" value="true" id="present_alternative_links" <?php echo (isset($config['alternative_links']) ?  " checked='true'" : '') ;?> ></input>
                                    <?php _e('Present alternative fulltext links', 'leisref'); ?>
                                </label>
                            </td>
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
                          <th scope="row">
                            <?php _e('Page Layout', 'leisref'); ?>:
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
                            <th scope="row"><?php _e('Sidebar order', 'leisref');?>:</th>

                            <?php
                              if(!isset($config['available_filter'])){
                                $config['available_filter'] = 'Subject;Act type;Country/region;Database;Collection;Language;Year';
                                $order = explode(';', $config['available_filter'] );

                              }else {
                                $order = explode(';', $config['available_filter'] );
                            }

                            ?>

                            <td>


                              <table border=0>
                                <tr>
                                <td >
                                    <p align="right"><?php _e('Available', 'leisref');?><br>
                                      <ul id="sortable1" class="droptrue">
                                      <?php
                                      if(!in_array('Subject', $order) && !in_array('Subject ', $order) ){
                                        echo '<li class="ui-state-default" id="Subject">'.translate('Subject','leisref').'</li>';
                                      }
                                      if(!in_array('Act type', $order) && !in_array('Act type ', $order) ){
                                        echo '<li class="ui-state-default" id="Act type">'.translate('Act type','leisref').'</li>';
                                      }
                                      if(!in_array('Country/region', $order) && !in_array('Country/region ', $order) ){
                                        echo '<li class="ui-state-default" id="Country/region">'.translate('Country/region','leisref').'</li>';
                                      }
                                      if(!in_array('Database', $order) && !in_array('Database ', $order) ){
                                        echo '<li class="ui-state-default" id="Database">'.translate('Database','leisref').'</li>';
                                      }
                                      if(!in_array('Collection', $order) && !in_array('Collection ', $order) ){
                                        echo '<li class="ui-state-default" id="Collection">'.translate('Collection','leisref').'</li>';
                                      }
                                      if(!in_array('Language', $order) && !in_array('Language ', $order) ){
                                        echo '<li class="ui-state-default" id="Language">'.translate('Language','leisref').'</li>';
                                      }
                                      if(!in_array('Year', $order) && !in_array('Year ', $order) ){
                                        echo '<li class="ui-state-default" id="Year">'.translate('Year','leisref').'</li>';
                                      }
                                      ?>
                                      </ul>

                                    </p>
                                </td>

                                <td >
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

                        <?php /*
                        <tr valign="top">
                            <th scope="row">
                                <?php _e('Display filters', 'leisref'); ?>:
                            </th>
                            <td>
                                <fieldset>
                                    <label for="available_filter_descriptor">
                                        <input type="checkbox" name="leisref_config[available_filter][]" value="descriptor" id="available_filter_descriptor" <?php echo (!isset($config['available_filter']) || in_array('descriptor', $config['available_filter']) ?  " checked='true'" : '') ;?> ></input>
                                        <?php _e('Subject', 'leisref'); ?>
                                    </label>
                                    <br/>
                                    <label for="available_filter_act_type">
                                        <input type="checkbox" name="leisref_config[available_filter][]" value="act_type" id="available_filter_act_type" <?php echo (!isset($config['available_filter']) || in_array('act_type', $config['available_filter']) ?  " checked='true'" : '') ;?> ></input>
                                        <?php _e('Act type', 'leisref'); ?>
                                    </label>
                                    <br/>
                                    <label for="available_filter_scope_region">
                                        <input type="checkbox" name="leisref_config[available_filter][]" value="scope_region" id="available_scope_region" <?php echo (!isset($config['available_filter']) || in_array('scope_region', $config['available_filter']) ?  " checked='true'" : '') ;?> ></input>
                                        <?php _e('Country/region', 'leisref'); ?>
                                    </label>
                                    <br/>
                                    <!--
                                    <label for="available_filter_database">
                                        <input type="checkbox" name="leisref_config[available_filter][]" value="database" id="available_filter_database" <?php echo (!isset($config['available_filter']) || in_array('database', $config['available_filter']) ?  " checked='true'" : '') ;?> ></input>
                                        <?php _e('Database', 'leisref'); ?>
                                    </label>
                                    <br/>

                                    <label for="available_filter_collection">
                                        <input type="checkbox" name="leisref_config[available_filter][]" value="collection" id="available_filter_collection" <?php echo (!isset($config['available_filter']) ||  in_array('collection', $config['available_filter']) ?  " checked='true'" : '') ;?> ></input>
                                        <?php _e('Collection', 'leisref'); ?>
                                    </label>
                                    <br/>
                                    <label for="available_filter_language">
                                        <input type="checkbox" name="leisref_config[available_filter][]" value="language" id="available_filter_language" <?php echo (!isset($config['available_filter']) ||  in_array('language', $config['available_filter']) ?  " checked='true'" : '') ;?> ></input>
                                        <?php _e('Language', 'leisref'); ?>
                                    </label>
                                    <br/>
                                    <label for="available_filter_year">
                                        <input type="checkbox" name="leisref_config[available_filter][]" value="year" id="available_filter_year" <?php echo (!isset($config['available_filter']) ||  in_array('year', $config['available_filter']) ?  " checked='true'" : '') ;?> ></input>
                                        <?php _e('Year', 'leisref'); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                      */ ?>

                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save changes') ?>" />
                </p>
            </form>
        </div>
        <script type="text/javascript">
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
