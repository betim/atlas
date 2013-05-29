<?php
  // ini_set("display_errors", 1);

  $dbs = mysql_connect("localhost", "", "");
  mysql_select_db("atlas");

  function qsprintf() {
    $numargs = func_num_args();
    $arg_list = func_get_args();
    $format = $arg_list[0];
    $clean_arg_list = array();

    for ($i = 1; $i < $numargs; $i++)
      array_push($clean_arg_list, qstrip($arg_list[$i]));

    return vsprintf($format, $clean_arg_list);
  }

  function qstrip($value) {
    if (get_magic_quotes_gpc())
      $value = stripslashes($value);

    if (is_numeric($value))
      return "'{$value}'";

    return "'" . mysql_real_escape_string($value) . "'";
  }
?>
