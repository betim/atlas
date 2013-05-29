<?php
  include 'f.php';

  if ($_REQUEST['s'] == 'ch') {
    mysql_query(qsprintf("UPDATE `hosts` SET `state` = !`state` WHERE `id` = %s LIMIT 1", $_REQUEST['host_id']));
    mysql_close($dbs);
    die(header("Location: hosts.php"));
  } else if ($_REQUEST['s'] == 'd') {
    mysql_query(qsprintf("DELETE FROM `hosts` WHERE `id` = %s LIMIT 1", $_REQUEST['host_id']));
    mysql_close($dbs);
    die(header("Location: hosts.php"));
  }
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
              <li><a href="index.php">Results</a></li>
              <li class="active dropdown">
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
        if ($_REQUEST['a'] == 'list' || !isset($_REQUEST['a'])) {
      ?>
        <h3>Tracked Hosts</h3>
        <table class="table table-striped">
          <tbody>
          <tr>
            <th>#</th>
            <th>Hostname</th>
            <th>IP</th>
            <th>Every</th>
            <th>Last</th>
            <th>State</th>
            <th>More</th>
          </tr>
          <?php
            $q = mysql_query("SELECT * FROM `hosts`");

            $c = 1;
            while ($result = mysql_fetch_row($q, MYSQL_ASSOC)) {
              echo '<tr>
                <td>', $c++ ,'</td>
                <td>', $result['name'] ,'</td>
                <td>', $result['ip'] ,'</td>
                <td>'. ($result['every'] / 60) .' mins</td>
                <td>'. date("d.m G:i", $result['last']). '</td>
                <td>'. ($result['state'] == '1' ? 'Active' : 'Disabled') .'</td>
                <td><a href="index.php?host_id=', $result['id'] ,'">Results</a> | 
                    <a href="hosts.php?host_id=', $result['id'] ,'&s=ch">Change state</a> | 
                    <a href="hosts.php?host_id=', $result['id'] ,'&s=d">Delete</a></td>
              </tr>';
            }
          ?>
          </tbody>
        </table>
      <?php
        } else if ($_REQUEST['a'] == 'add') {
          if (isset($_REQUEST['add'])) {
            $_q = mysql_query(qsprintf("INSERT INTO `hosts` (`name`, `ip`, `every`, `state`)
                                        VALUES (%s, %s, %s, '1');", 
                                       $_REQUEST['name'],  $_REQUEST['ip'], $_REQUEST['every']));
            if ($_q)
              echo "<script> alert('Added'); window.location = 'hosts.php'; </script>";
            else
              echo "<script> alert('Not added. Weird.'); window.location = 'hosts.php'; </script>";
          }
      ?>
        <h3>Add Host</h3>
        <form class="form-horizontal" method="POST">
          <input type="hidden" name="a" value="add">
          <div class="control-group">
            <label class="control-label" for="inputName">Name</label>
            <div class="controls">
              <input type="text" id="inputName" placeholder="Name" name="name">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputIP">IP</label>
            <div class="controls">
              <input type="text" id="inputIP" placeholder="IP" name="ip">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="inputEvery">Tracert Every</label>
            <div class="controls">
              <select id="inputEvery" name="every">
                <option value="60">1 min</option>
                <option value="300">5 mins</option>
                <option value="900">15 mins</option>
              </select>
            </div>
          </div>
          <div class="control-group">
            <div class="controls">
              <button type="submit" class="btn" name="add">Add</button>
            </div>
          </div>
        </form>
      <?php
        }
      ?>
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

  </body>
</html>
<?php
  mysql_close($dbs);
?>
