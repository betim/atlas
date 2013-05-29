<?php
  ini_set('display_errors', 1);

  $pid = 0;

  $dbs = mysql_connect("localhost", "root", "ipkonet");
  mysql_select_db("atlas");

  $q = mysql_query("SELECT * FROM `hosts` WHERE `state` = 1");

  while ($r = mysql_fetch_array($q, MYSQL_ASSOC)) {
    if (($r['last'] + $r['every']) <= time()) {
      if (!$pid) {
        traceroute($r['id'], $r['ip']);
      }

      $pid = pcntl_fork();
    }
  }

  mysql_close($dbs);

  function get_geo($ip) {
    $db = mysql_connect("localhost", "root", "ipkonet");
    mysql_select_db("atlas");

    $ch = curl_init();

    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, "http://api.hostip.info/country.php?ip={$ip}");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // grab URL and pass it to the browser
    $buff = curl_exec($ch);

    // close cURL resource, and free up system resources
    curl_close($ch);

    $country = mysql_fetch_array(
                mysql_query("SELECT * FROM `countries` WHERE `alpha_2` = '{$buff}' LIMIT 1"), 
                MYSQL_ASSOC);

    $ret_arr = array('lat' => $country['lat'], 'lng' => $country['lon'],
                     'country_code' => $country['alpha_2'], 'country_name' => $country['country'], 'city' => 'Capital');


    return $ret_arr;
  }

  function traceroute($id, $ip) {
    $time = time();

    $db = mysql_connect("localhost", "root", "ipkonet");
    mysql_select_db("atlas");

    $return_array = array();
    $buffer = shell_exec("traceroute {$ip} -q 1 -4");
    $rows = explode("\n", $buffer);

    array_shift($rows);

    foreach ($rows as $row) {
      $row = preg_replace(array("/^\\s|\(|\)/", "/\\s+/"), array("", " "), $row);
      $erow = explode(" ", $row);

      if ($erow[1] == '*' || $erow[1] == NULL)
        continue;

      $geo = get_geo($erow[2]);

      array_push($return_array, 
        array('hostname' => $erow[1], 'rtt' => $erow[3]. " ms", 
              'hop_num' => $erow[0], 'ip_address' => $erow[2],
              'latitude' => $geo['lat'], 'longitude' => $geo['lng'],
              'country_code' => $geo['country_code'],
              'country_name' => $geo['country_name']
        )
      );
    }

    mysql_query("
      INSERT INTO `results` (`hostid`, `timestamp`, `JSON`)
      VALUES ('{$id}',  '{$time}',  '". json_encode($return_array) ."');
    ", $db) or die(mysql_error());

    mysql_query("UPDATE `hosts` SET `last` = '{$time}' WHERE `id` = '{$id}' LIMIT 1;", $db);

    mysql_close($db);
  }
?>
