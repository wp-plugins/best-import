<?php

    namespace best_import_namespace;

    echo '<h3>Data</h3>';
    if($xml){ 
        echo '<h4>Fill the form below to match appropriate attributes with the data.</h4>';
        echo '<table class="form-table">';
        
        echo '<tr><td><label for="bi-main-tag">Main tag</label></td><td><select name="main-tag" value="" id="bi-main-tag">';
            foreach($tags as $tag=>$array)echo '<option name="import-number" value="'.$tag.'"'.(gpost('main-tag')==$tag?' selected="selected"':'').'>&lt;'.$tag.'&gt;</option>';
        echo '</select></td></tr>';
        
        echo '<tr><td><label for="bi-type">Type</label></td><td><select name="type" value="'.gpost('type').'" id="bi-type">';
            foreach(get_post_types() as $post_type)echo '<option value="'.$post_type.'"'.(gpost('type')==$post_type?' selected="selected"':'').'>'.$post_type.'</option>';
        echo '</select></td></tr>';

        echo '<tr><td><label for="bi-title">Title</label></td><td><input type="text" value="'.gpost('title').'" name="title" id="bi-title"  class="suggest"></td></tr>';
        echo '<tr><td><label for="bi-content">Content</label></td><td><textarea name="content" id="bi-content"  class="suggest">'.gpost('content').'</textarea></td></tr>';
        echo '<tr><td><label for="bi-date">Date</label></td><td><input type="text" value="'.gpost('date').'" name="date" id="bi-date"  class="suggest"></td></tr>';
        
        echo '<tr><td><label for="bi-exists">If post exists</label></td><td>';
        echo '<select name="exists">';
            echo '<option '.($exists=='add'?' selected="selected"':'').' value="add">add new post</option>';
            echo '<option '.($exists=='update'?' selected="selected"':'').' value="update">update existing post</option>';
            echo '<option '.($exists=='skip'?' selected="selected"':'').' value="skip">skip post</option>';
        echo '</select>';
        echo '</td></tr>';
        
        echo '</table>';
    }else{
        echo '<h4>You have not uploaded XML file yet.</h4>';
    }

?>