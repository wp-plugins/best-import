<?php

    namespace best_import_namespace;

    function apply_mapping($type, $value){
        global $mapping, $from, $to, $mapping_fields;
        for($i=0; $i<$mapping_fields; ++$i)
            if($mapping[$i]==$type && $value==$from[$i])
                return $to[$i];
        return $value;
    }

    function print_mapping($mapping, $from, $to){
        global $mapping_types;
        echo '<tr><td>';
        echo '<select name="mapping[]">';
        foreach($mapping_types as $k=>$v)echo '<option value="'.$k.'"'.($k==$mapping?' selected="selected"':'').'>'.$v.'</option>';
        echo '</select>';
        echo '</td><td>';
        echo '<input type="text" name="from[]" value="'.$from.'"> &raquo;';
        echo '<input type="text" name="to[]" value="'.$to.'"> ';
        echo '<input type="button" name="removeField" value="&times;">';
        echo '</td></tr>';
    }

    if($xml){                
        echo '<h3>Mapping</h3>';
        echo '<h4>Add mapping to your data if necessarily.</h4>';
        echo '<table class="form-table">';
        for($i=0; $i<$mapping_fields; ++$i)if($mapping[$i])print_mapping($mapping[$i], $from[$i], $to[$i]);
        print_mapping('', 'from', 'to');
        echo '<tr><td><label for="bi-add-2">Add mapping</label></td><td><input type="button" value="Add" id="bi-add-2"></td></tr>';
        echo '</table>';
    }

?>