<?php

    namespace best_import_namespace;

    function check_attribute($node, $name, $operator, $value){
        $attr = $node->attributes()->$name;
        return $operator=='' && $attr ||
            $operator=='=' && $attr==$value ||
            $operator=='!=' && $attr!=$value ||
            $operator=='^=' && substr($attr,0,strlen($value))==$value ||
            $operator=='$=' && substr($attr,-strlen($value))==$value ||
            $operator=='*=' && strpos($attr,$value)!==false ||
            $operator=='<' && $attr<$value ||
            $operator=='<=' && $attr<=$value ||
            $operator=='>' && $attr>$value ||
            $operator=='>=' && $attr>=$value;
    }

    function parse_tag($str, $i){
        global $tags;

        preg_match_all("/\<\w+\/\w+\>(\s*\[[^\]]+?\])*/", $str, $strtags);
        $strtags = $strtags[0];

        foreach($strtags as $strtag){
                        
            preg_match_all("/\<(\w+)\/(\w+)\>/", $strtag, $tag_subtag);
            $tag = $tag_subtag[1][0];
            $subtag = $tag_subtag[2][0];
            
            preg_match_all("/\[(\w+)\s*(?:(\=|\!\=|\^\=|\$\=|\*\=|\<|\>|\<\=|\>\=)\s*(\"[^\"]*\"|[^\]]*))?\]/", $strtag, $attributes);
            list($attributes, $name, $operator, $value) = $attributes;

            $data = '';
            $nodes = $tags[$tag][$i]->$subtag;

            foreach($nodes as $node){
                $add = true;
                foreach($attributes as $n=>$attr)
                    if(!check_attribute($node, $name[$n], $operator[$n], $value[$n])){
                        $add = false;
                        break;
                    }
                if($add)$data .= get_text($node)."\n";
            }
            
            $data = preg_replace("/\n$/", '', $data);
            $str = str_replace($strtag, $data, $str);
        }
        
        return $str;
    };

    function parse_string_tag($value, $i){
        $value = parse_tag($value, $i);
        return $value;
    }

    function parse_int_tag($value, $i){
        $value = parse_tag($value, $i);
        return intval($value);
    }

    function parse_float_tag($value, $i){
        $value = parse_tag($value, $i);
        return floatval(str_replace(',', '.', $value));
    }

    function parse_date_tag($value, $i){
        $value = parse_tag($value, $i);
        $value = strtotime(parse_tag($value, $i));
        return date('Y-m-d H:i:s', $value==false?time():$value);
    }

    function parse_array_tag($value, $i){
        $value = parse_tag($value, $i);
        return $value==''?array():explode("\n", $value);
    }

    echo '<h3>Import</h3>';
    if($xml){
        
        $type = gpost('type');
        $wp_error = '';

        if(gpost('import')=='Import' || isset($_GET['import'])){
            
            for($i=0; $i<$import_number; ++$i){
                
                $post = NULL;
                $title = apply_mapping('title', parse_string_tag(gpost('title'), $i));
                $oldpost = get_page_by_title($title, OBJECT, $type);
                if($oldpost && $oldpost->post_status=='trash')$oldpost = NULL;

                if($oldpost){
                    if($exists=='update')$post = array('ID' => $oldpost->ID, 'post_status' => 'publish');
                    else if($exists=='skip')continue;
                }
                
                if(!$post)$post = array('post_status' => 'publish');

                // type
                $post['post_type'] = $type;
                //if($type=='product')wp_set_object_terms($post_id, 'simple', 'product_type');

                // title
                $post['post_title'] = $title;

                // name
                $post['post_name'] = sanitize_title($post['post_title']);

                // content
                $post['post_content'] = apply_mapping('content', parse_string_tag(gpost('content'), $i));

                // date
                $post['post_date'] = apply_mapping('date', parse_date_tag(gpost('date'), $i));

                // insert post
                if(isset($post['ID']))$post_id = wp_update_post($post, $wp_error);
                else $post_id = wp_insert_post($post, $wp_error);

                // taxonomies
                foreach($taxonomies as $taxonomy){
                    $categories = parse_array_tag($taxonomy['value'], $i);
                    foreach($categories as $n=>$category)$categories[$n] = apply_mapping($taxonomy['name'], $category);
                    wp_set_object_terms($post_id, $categories, $taxonomy['name']);
                }
                
            }
            
            echo '<h4>'.$import_number.' posts has been successfully imported.</h4>';
            echo '<label for="bi-import-number">How many?</label> <input type="text" value="'.$import_number.'" name="import-number" id="bi-import-number"> ';
            echo '<input type="submit" name="preview" value="Update Preview"><br><br>';
            
        }else if(gpost('type')){
            
            echo '<h4>The table below contains the data to import.</h4>';
            echo '<label for="bi-import-number">How many?</label> <input type="text" value="'.$import_number.'" name="import-number" id="bi-import-number"> ';
            echo '<input type="submit" name="preview" value="Update Preview"> ';
            echo '<input type="submit" name="import" value="Import"><br><br>';
            
        }else{
                
            echo '<h4>Click on the button below to see preview.</h4>';
            echo '<label for="bi-import-number">How many?</label> <input type="text" value="'.$import_number.'" name="import-number" id="bi-import-number"> ';
            echo '<input type="submit" name="preview" value="Preview">';
                
        }

    }else{
        echo '<h4>You have not uploaded XML file yet.</h4>';
    }

?>