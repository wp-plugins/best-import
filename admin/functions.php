<?php

    namespace best_import_namespace;

    ini_set('post_max_size', '1024M');
    ini_set('upload_max_filesize', '1024M');

    function xml_from_zip($file){
        $zip = zip_open($file);
        if($zip){
            while($zip_entry = zip_read($zip)){
                $name = zip_entry_name($zip_entry);
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                if($ext == 'xml' && zip_entry_open($zip, $zip_entry)){
                    $contents = zip_entry_read($zip_entry, 1024*1024*100);
                    zip_entry_close($zip_entry);
                    return $contents;
                }
            }
        }
        zip_close($zip);
        return null;
    }

    function csv_to_xml($csv_file){
        $file = fopen($csv_file, 'r');
        $xml = "<csv>\n";
        $headers = fgetcsv($file);
        
        foreach($headers as $i=>$header)
            $headers[$i] = preg_replace("/\W/", "_", $header);
        
        while(($row = fgetcsv($file)) !== false){
            $xml .= "   <row>\n";
            foreach($headers as $i=>$header)$xml .= "       <$header>".$row[$i]."</$header>\n";
            $xml .= "   </row>\n";
        }
        
        $xml .= "</csv>";
        return $xml;
    }

    function upload_xml($file){
        global $xml_file, $zip_file;
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        if($ext=='xml'){
            
            if(!move_uploaded_file($file['tmp_name'], $xml_file))echo 'Error while uploading the file.';
            
        }else if($ext=='zip'){
            
            if(!move_uploaded_file($file['tmp_name'], $zip_file))echo 'Error while uploading the file.';
            else{
                $xml_content = xml_from_zip($zip_file);   
                if(!$xml_content){
                    echo 'The ZIP archive does not contain XML file.';
                    unlink($zip_file);
                }else file_put_contents($xml_file, $xml_content);
            }
            
        }else if($ext=='csv'){
            
            $csv_file = str_replace('.xml', '.csv', $xml_file);
            if(!move_uploaded_file($file['tmp_name'], $csv_file))echo 'Error while uploading the file.';
            else file_put_contents($xml_file, csv_to_xml($csv_file));
            unlink($csv_file);
            
        }else{
            
            return 'Select a proper file (XML/CSV/ZIP).';
            
        }
        
        return '';
    }

    function get_text($xml){
        if($xml->count()==0)return $xml;
        $text = '';
        foreach($xml as $k=>$v)
            if($k!='@attributes')
                $text .= get_text($v)."\n";
        return $text;
    }

?>