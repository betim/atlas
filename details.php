<?php
  include 'f.php';
  $coord = "";
  $ch_hops = $times = "";

  $_q = qsprintf("SELECT `hosts`.`name`, `hosts`.`ip`, `hosts`.`id` as `hid`,
                  `results`.* FROM `results`
                  INNER JOIN `hosts` ON `hosts`.`id` = `results`.`hostid`
                  WHERE `results`.`id` = %s", $_REQUEST['result_id']);

  $q = mysql_query($_q);

  if (mysql_num_rows($q) == 0)
    header("Location: index.php");

  $host_result = mysql_fetch_row($q, MYSQL_ASSOC);
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
    <![endif]-->

    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#">ATLAS</a>
          <div class="nav-collapse collapse pull-right">
            <ul class="nav">
              <li class="active"><a href="#"><?= $host_result['name']. " @ ". $host_result['ip']; ?></a></li>
              <li><a href="index.php">Results</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Hosts <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="hosts.php?a=add">Add</a></li>
                  <li><a href="hosts.php?a=list">List</a></li>
                </ul>
              </li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
      <h3>Details for this Tracert: <i><?= $host_result['name']. " @ ". $host_result['ip']; ?></i> on <?= date("d.m H:i", $host_result['timestamp']); ?></h3>
      <table class="table table-condensed">
        <tbody>
        <tr>
          <th width="0">C</th>
          <th width="30%">Hostname</th>
          <th width="30%">IP</th>
          <th width="20%">Hop</th>
          <th width="20%"><abbr title="Round Trip Time">RTT</abbr></th>
        </tr>
        <?php
          $hops = $tot_rtt = 0;

          $json = json_decode($host_result['JSON']);
          foreach ($json as $measure) {
            echo '<tr><td><img src="assets/img/c/'. strtolower($measure->country_code) .'.gif" title="', $measure->country_name ,'"></td>
                  <td>', $measure->hostname ,'</td>
                  <td>', $measure->ip_address ,'</td>
                  <td>', $measure->hop_num ,'</td>
                  <td>', $measure->rtt ,'</td></tr>';

            $hops++;
            $tot_rtt += floatval($measure->rtt);

            $times .= floatval($measure->rtt). ",";
            $ch_hops .= $measure->hop_num. ",";

            if ($measure->latitude)
              $coord .= "new google.maps.LatLng({$measure->latitude}, {$measure->longitude}),\n";
          }
        ?>
        <tr>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th><abbr title="Average">AVG</abbr> <?= sprintf("%.3f ms", ($tot_rtt / $hops)); ?></th>
        </tr>
        <tr>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th><abbr title="Total">TOT</abbr> <?= sprintf("%.3f ms", $tot_rtt); ?></th>
        </tr>
        
        </tbody>
      </table>

      <div id="container" style="height: 200px; margin: 0 auto"></div>

      <hr>

      <div id="map" style="height: 400px;"></div>

      <hr>

      <h3>Last 15 Results for <i><?= $host_result['name']. " @ ". $host_result['ip']; ?></i></h3>
      <table class="table table-striped">
        <tbody>
        <tr>
          <th>#</th>
          <th>Hostname</th>
          <th>IP</th>
          <th>When</th>
          <th><abbr title="Average">AVG</abbr> <abbr title="Round Trip Time">RTT</abbr></th>
          <th>More</th>
        </tr>
        <?php
          $_q1 = qsprintf("SELECT `hosts`.`name`, `hosts`.`ip`,
                            `results`.* FROM `results`
                            INNER JOIN `hosts` ON `hosts`.`id` = `results`.`hostid`
                            WHERE `results`.`hostid` = %s
                            ORDER BY `id` DESC
                            LIMIT 15", $host_result['hid']);

          $q1 = mysql_query($_q1);

          $c = 1;
          while ($result = mysql_fetch_row($q1, MYSQL_ASSOC)) {
            echo '<tr>
              <td>', $c++ ,'</td>
              <td>', $result['name'] ,'</td>
              <td>', $result['ip'] ,'</td>
              <td>'. date("d.m G:i", $result['timestamp']). '</td>
              <td>'. calculate_avg($result['JSON']) .'</td>
              <td><a href="details.php?result_id=', $result['id'] ,'">Details</a></td>
            </tr>';
          }
        ?>
        </tbody>
      </table>

      <hr>

      <footer>
        <p>IPKO 2013</p>
      </footer>

    </div> <!-- /container -->

    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap-transition.js"></script>
    <script src="assets/js/bootstrap-alert.js"></script>
    <script src="assets/js/bootstrap-modal.js"></script>
    <script src="assets/js/bootstrap-dropdown.js"></script>
    <script src="assets/js/bootstrap-scrollspy.js"></script>
    <script src="assets/js/bootstrap-tab.js"></script>
    <script src="assets/js/bootstrap-tooltip.js"></script>
    <script src="assets/js/bootstrap-popover.js"></script>
    <script src="assets/js/bootstrap-button.js"></script>
    <script src="assets/js/bootstrap-collapse.js"></script>
    <script src="assets/js/bootstrap-carousel.js"></script>
    <script src="assets/js/bootstrap-typeahead.js"></script>

    <script src="assets/js/highcharts.js"></script>

    <script>
      var map;
      function initialize() {
        var myLatLng = new google.maps.LatLng(42, -25);
        var mapOptions = {
          zoom: 3,
          center: myLatLng,
          mapTypeId: google.maps.MapTypeId.TERRAIN,
          zoomControl: false,
          streetViewControl: false,

        };

        var map = new google.maps.Map(document.getElementById('map'), mapOptions);

        var lineCoordinates = [
          <?= $coord; ?>
        ];

        var lineSymbol = {
          path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
        };

        var line = new google.maps.Polyline({
          strokeWeight: 2, strokeColor: "#000",
          path: lineCoordinates,
          icons: [{
            icon: lineSymbol,
            offset: '100%'
          }],
          map: map
        });

      $(function () {
        $('#container').highcharts({
            chart: {
                type: 'line',
            },
            title: {
                text: 'Hop time',
                x: -20 //center
            },
            subtitle: {
                text: '',
                x: -20
            },
            xAxis: {
                categories: [<?= $ch_hops; ?>],
                title: {
                    text: 'hops'
                },
            },
            yAxis: {
                title: {
                    text: 'time / (ms)'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: ' ms'
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                borderWidth: 0
            },
            series: [{
                name: '<?= $host_result['ip']; ?>',
                data: [<?= $times; ?>]
            }]
          });
        });
      }

      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </body>
</html>


<?php
  function calculate_avg($JSON) {
    $tot_rtt = $hops = 0;

    $json = json_decode($JSON);
    foreach ($json as $measure) {
      $hops++;

      $tot_rtt += floatval($measure->rtt);
    }

    return sprintf("%.3f ms", ($tot_rtt / $hops));
  }

  mysql_close($dbs);
?>
