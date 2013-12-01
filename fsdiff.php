<?php

function listFiles($dir, &$f, &$d) {

  global $root_dir;
  
  $d[] = $dir;

  $files = glob($dir . '/*');

  foreach ($files as $file) {

    if (is_dir($file)) {

      listFiles($file, $f, $d);
    } else {

      $md5 = md5_file($file);

      $f[$md5] = str_replace($root_dir,'' ,$file);
      
      #echo "add: $file\n";
    }
  }
}

function getDirMap($dir) {

  $f = array();
  $d = array();
  
  $GLOBALS['root_dir'] = $dir;

  listFiles($dir, $f, $d);

  return array('files' => $f, 'dirs' => $d);
}

function get_diffs($dir_map1, $dir_map2) {

  $old = json_decode($dir_map1,true);

  $g = json_decode($dir_map2, true);


  //$diff_dirs = array_diff_assoc($g['dirs'], $old['dirs']);


  $files_res = array();
  $files_res['removed'] = array();
  $files_res['updated'] = array();
  $files_res['added'] = array();


  $eq_elements = array_intersect_assoc($old['files'], $g['files']);

  foreach ($eq_elements as $k => $ff) {

    unset($g['files'][$k]);
    unset($old['files'][$k]);
  }


  foreach ($g['files'] as $key => $file) {

    if (in_array($file, $old['files'])) {

      $files_res['updated'][$key] = $file;
    } else {

      $files_res['added'][$key] = $file;
    }
  }

  foreach ($old['files'] as $kk => $fff) {

    if (!in_array($fff, $files_res['added']) && !in_array($fff, $files_res['updated'])) {

      $files_res['removed'][$kk] = $fff;
    }
  }
  
  return $files_res;
}

#file_put_contents('test2.json',json_encode(getDirMap(dirname(__FILE__))));

function get_diff_report($diff_files) {
  
  echo "\nFiles removed:\n";
  
  foreach($diff_files['removed'] as $removed) {
    
    echo "- " . $removed . "\n";
    
  }
  
  echo "\nFiles added:\n";
  
  foreach($diff_files['added'] as $added) {
    
    echo "- " . $added . "\n";
    
  }
  
  echo "\nFiles updated:\n";
  
  foreach($diff_files['updated'] as $updated) {
    
    echo "- " . $updated . "\n";
    
  }
}

#get_diff_report(get_diffs('test1.json', 'test2.json'));
?>