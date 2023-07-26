<?php

if ( !function_exists('leisref_print_lang_value') ) {
    function leisref_print_lang_value($value, $lang_code){
        $lang_code = substr($lang_code,0,2);
        if ( is_array($value) ){
            foreach($value as $current_value){
                $print_values[] = leisref_get_lang_value($current_value, $lang_code);
            }
            echo implode(', ', $print_values);
        }else{
            echo leisref_get_lang_value($value, $lang_code);
        }
        return;
    }
}

if ( !function_exists('leisref_get_lang_value') ) {
    function leisref_get_lang_value($string, $lang_code, $default_lang_code = 'en'){
        $lang_value = array();
        $occs = preg_split('/\|/', $string);

        foreach ($occs as $occ){
            $re_sep = (strpos($occ, '~') !== false ? '/\~/' : '/\^/');
            $lv = preg_split($re_sep, $occ);
            $lang = substr($lv[0],0,2);
            $value = $lv[1];
            $lang_value[$lang] = $value;
        }

        if ( isset($lang_value[$lang_code]) ){
            $translated = $lang_value[$lang_code];
        } elseif ( isset($lang_value[$default_lang_code]) ){
            $translated = $lang_value[$default_lang_code];
        } else {
            $translated = ltrim(strstr($string, '^'), '^');
        }

        return $translated;
    }
} else {
    echo "<pre>"; print_r($_SERVER); echo "</pre>"; die();
}

if ( !function_exists('format_date') ) {
    function format_date($string){
        $date_formated = '';
        if (strpos($string,'-') !== false) {
            $date_formated = substr($string,8,2)  . '/' . substr($string,5,2) . '/' . substr($string,0,4);
        }else{
            $date_formated =  substr($string,6,2)  . '/' . substr($string,4,2) . '/' . substr($string,0,4);
        }

        return $date_formated;
    }
}

if ( !function_exists('format_act_date') ) {
    function format_act_date($string, $lang){
        $months = array();
        $months['pt'] = array('Janeiro','Feveiro', 'Março', 'Abril', 'Maio', 'Junho',
                              'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

        $months['es'] = array('Enero','Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                              'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');


        $date_formated = '';
        if (strpos($string,'-') !== false) {
            if ($lang != 'en'){
                $month_val = intval(substr($string,5,2));
                $month_name = $months[$lang][$month_val-1];
            } else {
                $month_name = strftime("%B", strtotime($string));
            }
            $date_formated = substr($string,8,2) . ' ' . __('of','leisref') . ' ' . $month_name . ' ' . __('of', 'leisref') . ' ' . substr($string,0,4);
        }else{
            $date_formated =  substr($string,6,2)  . '/' . substr($string,4,2) . '/' . substr($string,0,4);
        }

        return $date_formated;
    }
}

if ( !function_exists('isUTF8') ) {
    function isUTF8($string){
        return (utf8_encode(utf8_decode($string)) == $string);
    }
}

if ( !function_exists('translate_label') ) {
    function translate_label($texts, $label, $group=NULL) {
        // labels on texts.ini must be array key without spaces
        $label_norm = preg_replace('/[&,\'\s]+/', '_', $label);
        if($group == NULL) {
            if(isset($texts[$label_norm]) and $texts[$label_norm] != "") {
                return $texts[$label_norm];
            }
        } else {
            if(isset($texts[$group][$label_norm]) and $texts[$group][$label_norm] != "") {
                return $texts[$group][$label_norm];
            }
        }
        // case translation not found return original label ucfirst
        return ucfirst($label);
    }
}

if ( !function_exists('get_site_meta_tags') ) {
    function get_site_meta_tags($url){

        $site_title = array();

        $fp = @file_get_contents($url);

        if ($fp) {
            $res = preg_match("/<title>(.*)<\/title>/siU", $fp, $title_matches);
            if ($res) {
                $site_title = preg_replace('/\s+/', ' ', $title_matches[1]);
                $site_title = trim($site_title);
            }

            $site_meta_tags = get_meta_tags($url);
            $site_meta_tags['title'] = $site_title;

            foreach ($site_meta_tags as $key => $value) {
                if (!isUTF8($value)){
                    $site_meta_tags[$key] = utf8_encode($value);
                }
            }
        }
        return $site_meta_tags;
    }
}

if ( !function_exists('real_site_url') ) {
    function real_site_url($path = ''){

        $site_url = get_site_url();

        // check for multi-language-framework plugin
        if ( function_exists('mlf_parseURL') ) {
            global $mlf_config;

            $current_language = substr( strtolower(get_bloginfo('language')),0,2 );

            if ( $mlf_config['default_language'] != $current_language ){
                $site_url .= '/' . $current_language;
            }
        }
        if ($path != ''){
            $site_url .= '/' . $path;
        }
        $site_url .= '/';


        return $site_url;
    }
}

if ( !function_exists('translate_filter_options') ) {

    function sort_by_label($a, $b){
        $a = strtolower($a['label']);
        $b = strtolower($b['label']);

        if ($a == $b) return 0;
        return ($a < $b) ? -1 : 1;
    }

    function translate_filter_options($filter, $lang_code){
        // translate and sort list
        $list_translated = array();
        foreach ($filter as $filter_option) {
            $translation = leisref_get_lang_value($filter_option[0], $lang_code);
            if ($translation != ''){
                $list_translated[] = array("label" => $translation, "original" => $filter_option);
            }
        }
        usort($list_translated, 'sort_by_label');

        return $list_translated;
    }
}

function mount_filter_link($filter_name, $filter_query, $query, $user_filter, $act_number){
    $filter_link = '?';
    if ($query != ''){
        $filter_link .= 'q=' . $query . '&';
    }
    $filter_link .= 'filter=' . $filter_name . ':"' . $filter_query . '"';
    if ($user_filter != ''){
        $filter_link .= ' AND ' . $user_filter ;
    }
    if ($act_number != ''){
        $filter_link .= '&act_number=' . $act_number ;
    }
    return $filter_link;

}


?>
