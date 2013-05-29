<?php
  include 'f.php';

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
              <li class="active"><a href="index.php">Results</a></li>
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
      <?php
        if (isset($_REQUEST['host_id'])) {
          $q = mysql_query(qsprintf("SELECT `hosts`.`name`, `hosts`.`ip`,
                            `results`.* FROM `results`
                            INNER JOIN `hosts` ON `hosts`.`id` = `results`.`hostid`
                            WHERE `results`.`hostid` = %s
                            ORDER BY `id` DESC", $_REQUEST['host_id']));
          echo '<h3>Results for selected host</h3>';
        } else {
          $q = mysql_query("SELECT `hosts`.`name`, `hosts`.`ip`,
                            `results`.* FROM `results`
                            INNER JOIN `hosts` ON `hosts`.`id` = `results`.`hostid`
                            ORDER BY `id` DESC
                            LIMIT 30");
          echo '<h3>Last 30 Results</h3>';
        }
      ?>
      <table class="table table-striped">
        <tbody>
        <tr>
          <th>#</th>
          <th>Hostname</th>
          <th>IP</th>
          <th>When</th>
          <th><abbr title="Average">AVG</abbr> <abbr title="Round Trip Time">RTT</abbr></th>
          <th>More</th>
          <th width="0"><abbr title="Select up to 3 results">Compare</abbr></th>
        </tr>
        <?php
          $c = 1;
          while ($result = mysql_fetch_row($q, MYSQL_ASSOC)) {
            echo '<tr>
              <td>', $c++ ,'</td>
              <td>', $result['name'] ,'</td>
              <td>', $result['ip'] ,'</td>
              <td>'. date("d.m H:i:s", $result['timestamp']). '</td>
              <td>'. calculate_avg($result['JSON']) .'</td>
              <td><a href="details.php?result_id=', $result['id'] ,'">Details</a></td>
              <td><input type="checkbox" value="', $result['id'] ,'" /></td>
            </tr>';
          }
        ?>
        </tbody>
      </table>
      <input type="button" value="Compare" style="float: right; margin-top: 10px; margin-right: 8%" />
      <BR><BR>
      <hr>

      <footer>
        <p>IPKO 2013</p>
      </footer>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
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
    <script>
      var ids = {};

      $('input[type="checkbox"]').change(function() {
        if (this.checked) {
          if (Object.keys(ids).length >= 3) {
            alert("No more then 3 can be selected.");
            $(this).prop('checked', false);
          } else
            ids[this.value] = this.value;
        } else
          delete ids[this.value];
      });

      $('input[type="button"]').click(function() {
        window.location = 'compare.php?result_ids=' + Object.keys(ids);
      });
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
