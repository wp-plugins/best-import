<?php

    namespace best_import_namespace;

    if($xml && gpost('type')){       
        echo '<table class="wp-list-table widefat fixed">';
        echo '<thead><tr>';
        echo '<th style="width: 80px;">No</th>';
        echo '<th>Title</th>';
        echo '<th>Content</th>';
        echo '<th>Date</th>';
        foreach($taxonomies as $k=>$v)echo '<th>'.$taxonomies_objects[$k]->label.'</th>';
        echo '</tr></thead><tbody>';

        for($i=0; $i<$import_number; ++$i){

            $title = preview(apply_mapping('title', parse_string_tag(gpost('title'), $i)));
            $content = preview(apply_mapping('content', parse_string_tag(gpost('content'), $i)));
            $date = preview(apply_mapping('date', parse_date_tag(gpost('date'), $i)));
            
            $post = get_page_by_title($title, OBJECT, gpost('type'));
            if($post && $post->post_status=='trash')$post = NULL;
            
            // data
            echo '<tr>';
            if($exists=='add' || !$post)echo '<td>#'.($i+1).' add</td>';
            else if($exists=='update')echo '<td>#'.($i+1).' update</td>';
            else if($exists=='skip')echo '<td>#'.($i+1).' skip</td>';
            echo '<td>'.$title.'</td>';
            echo '<td>'.$content.'</td>';
            echo '<td>'.$date.'</td>';

            // taxonomies
            foreach($taxonomies as $taxonomy){
                echo '<td>';
                foreach(parse_array_tag($taxonomy['value'], $i) as $category)echo preview(apply_mapping($taxonomy['name'], $category)).' ';
                echo '</td>';
            }

            echo '</tr>';
        }

        echo '</tbody></table>';

    }

?>