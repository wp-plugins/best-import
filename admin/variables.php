<?php

    namespace best_import_namespace;

    // tags

    global $tags;
    $tags = array();

    // numbers

    global $max_preview, $import_number;

    $max_preview = 1024;
    $import_number = intval(gpost('import-number'));

    // xml

    global $xml, $xml_content;

    if(file_exists($xml_file)){
        $xml = simplexml_load_file($xml_file); 
        $xml_content = file_get_contents($xml_file);
    }else{
        $xml = null;
        $xml_content = null;
    }

    // suggest
    
    global $suggest;
    $suggest = '';

    // post data

    global $names, $values, $types, $fields;

    $names = gpost('names', array());
    $values = gpost('values', array());
    $types = gpost('types', array());
    $fields = count($names);

    // exists
    
    global $exists;
    $exists = gpost('exists', 'add');

    // post taxonomies

    global $taxonomies, $taxonomies_objects;

    $taxonomies = array();
    $taxonomies_objects = get_taxonomies(array(), 'objects');   

    foreach($taxonomies_objects as $taxonomy)
        if(in_array(gpost('type','post'), $taxonomy->object_type))
            $taxonomies[$taxonomy->name] = array(
                'name' => $taxonomy->name,
                'label' => $taxonomy->label,
                'value' => isset(gpost('taxonomies')[$taxonomy->name])?gpost('taxonomies')[$taxonomy->name]:''
            );

    // post mapping

    global $mapping, $from, $to, $mapping_fields;

    $mapping = gpost('mapping', array());
    $from = gpost('from', array());;
    $to = gpost('to', array());
    $mapping_fields = count($mapping);

    // input types

    global $input_types;

    $input_types = array(
        '' => '-',
        'string' => 'String',
        'int' => 'Integer',
        'float' => 'Float',
        'date' => 'Date',
    );

    // mapping types

    global $mapping_types;

    $mapping_types = array(
        '' => '-',
        'title' => 'Title',
        'content' => 'Content',
        'date' => 'Date',
        'media' => 'Media'
    );

    foreach($taxonomies as $taxonomy)
        $mapping_types[$taxonomy['name']] = $taxonomy['label'];

    for($i=0; $i<$fields; ++$i)
        if($types[$i])
            $mapping_types[$names[$i]] = $names[$i];
        
?>