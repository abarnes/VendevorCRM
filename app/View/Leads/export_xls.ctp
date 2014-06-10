<?PHP

  function cleanData(&$str)
  {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    $str = str_replace(",","",$str);
    $str = str_replace("&amp;","&",$str);
      $str = str_replace("&#x27;","'",$str);
	if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }

  $flag = false;
  foreach($rows as $r) {
    if(!$flag) {
      # display field/column names as first row
      //echo implode("\t", array_keys($r['Lead'])) . "\r\n";
      echo "Id,Name,Categories,Phone,Address,Email,Website,Search Term\r\n";
      $flag = true;
    }
    array_walk($r['Lead'], 'cleanData');
    echo implode(",", array_values($r['Lead'])) . "\r\n";
  }
?>