<html>
<head>
<title>Maintenance Banner Admin</title>
<style>
   .mx_banner {
        text-align: center;
        font-family: Arial;
    }
    table.center {
       margin-left:auto; 
       margin-right:auto;
  }
</style>
</head>
<body class="mx_banner">
<h1>Maintenance Banner Admin</h1>
<?php

# Program: mx_banner.php
# Author: James Briggs, USA
# Date: 2018 02 03
# Env: PHP
# Purpose: single-page PHP web application to update banner settings in database for your web app to SELECT and display in your application.
#          This is also a minimal but non-trivial CRUD application that illustrates non-OOP but secure programming with placeholders and strip_tags()
# Dependencies: requires PHP PDO for either MySQL or Postgresql
# Installation: This is one of the simplest and smallest web applications to install. See README.md for installation and troubleshooting details.
#
# Query parameters:
#
# - 'a' is the Action to take, and is a letter in 'crud'
# - 'id' is the row id for an event

   # enable maximum errors. Note you need this in php.ini to see the errors: display_errors = on
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);

   $prog  = $_SERVER['PHP_SELF'];

###
### start of user settings
###

   $host  = '127.0.0.1';
   $db    = 'public';
   $table = 'intercom';
   $user  = 'postgres';
   $pw    = '';

   $product = 'pgsql'; # 'mysql' or 'pgsql'
   if ($product === 'mysql') {
      $port = 3306;
   }
   else {
      $port = 5432;
   }

###
### end of user settings
###

   $dsn = "$product:host=$host;port=$port;dbname=$db";

   echo <<<EOT
<p>This admin application updates the $table events table. Your user application(s) can then display the maintenance events on their login page.</p>
EOT;

   $view_events_link = <<<EOT
<p><a href="$prog">View Events</a></p>
EOT;

   try {
      $dbh = new PDO($dsn, $user, $pw);
   } catch (PDOException $e) {
      echo $e->getMessage();
      die();
   }

   if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (isset($_GET['a'])) {
         $a = clean_string($_GET['a']);
   
         if ($a === 'c' || $a === 'u') {
            if ($a === 'u') {
               if ($product === 'mysql') {
                  $sql = "select id, dt_start, dt_end, message, type from $table where id=?";
               }
               else {
                  $sql = "select id, to_char(dt_start, 'YYYY-MM-DD HH24:MI:SS') dt_start, to_char(dt_end, 'YYYY-MM-DD HH24:MI:SS') dt_end, message, type from $table where id=?";
               }

               $sth = $dbh->prepare($sql);
               $sth->execute([ clean_num($_GET['id']) ]);
               $result = $sth->fetch(PDO::FETCH_ASSOC);
   
               $id       = clean_num($result['id']);
               $dt_start = clean_time($result['dt_start']);
               $dt_end   = clean_time($result['dt_end']);
               $message  = clean($result['message']);
               $type     = clean_string($result['type']);
            }
            else {
               $id       = 0;
               $dt_start = '';
               $dt_end   = '';
               $message  = '';
               $type     = '';
            }
   
            $m = dynamic_select([ 'notice' => 'Notice', 'warning' => 'Warning', 'error' => 'Error', 'success' => 'Success' ], 'type', '', $type);
   
            echo <<<EOT
<form name="myform" method="POST" action="$prog">
<table class="center" border="0">
<tr>
<td>Start Time <super>*</super>:</td><td><input type="text" id="dt_start" name="dt_start" value="$dt_start" placeholder="YYYY-MM-DD HH:MM:SS" size="19" maxlength="19"></td>
</tr>
<tr>
<td>End Time <super>*</super>:</td><td><input type="text" id="dt_end" name="dt_end" value="$dt_end" placeholder="YYYY-MM-DD HH:MM:SS" size="19" maxlength="19"></td>
</tr>
<tr>
<td>Message <super>*</super>:</td><td><input type="text" id="message" name="message" value="$message" placeholder="Your maintenance message here." size="80" maxlength="80"></td>
</tr>
<tr>
<td>Status <super>*</super>:</td><td>$m</td>
</tr>
</table>
<br>
<br>
<input type="hidden" name="id" value="$id">
<input type="hidden" name="a" value="$a">
<input type="submit" value="Save" onclick="if (document.getElementById('dt_start').value === '' || document.getElementById('dt_end').value === '' || document.getElementById('message').value === '') { alert('Error: fill in all values.'); return false; }; this.disabled=true;this.value='Sending, please wait...';this.form.submit();">
<a href="$prog">Cancel</a>
</form>
<script>
   document.forms[0].elements[0].focus();
</script>
EOT;
         }
         else if ($a === 'd') {
            $id = clean_num($_GET['id']);
            $id += 0;
            if ($id === 0) {
               echo <<<EOT
<p>Error: invalid event id - cannot be zero.</p>
$view_events_link
EOT;
               die();
            }
            try {
               $sql = "delete from $table where id=?";
               $sth = $dbh->prepare($sql);
               $sth->execute([ $id ]);
               $ndel = $sth->rowCount();
            } catch (PDOException $e) {
               echo $e->getMessage();
               die();
            }

            echo <<<EOT
<p>Success: $ndel rows deleted.</p>
$view_events_link
EOT;
         }
      }
      else { # Read
         if ($product === 'mysql') {
            $sql = "select id, dt_start, dt_end, message, type from $table order by id";
         }
         else {
            $sql = "select id, to_char(dt_start, 'YYYY-MM-DD HH24:MI:SS'), to_char(dt_end, 'YYYY-MM-DD HH24:MI:SS'), message, type from $table order by id";
         }
   
         try {
            $rows = $dbh->query($sql);
         } catch (PDOException $e) {
            echo $e->getMessage();
            die();
         }
   
         $s = '';
         $n = 0;
         foreach($rows as $row) {
            $s .= <<<EOT
<tr>
<td><a href="$prog?a=u&id=$row[0]">Edit</a> | 
<a href="$prog?a=d&id=$row[0]" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a></td>
<td>$row[1]</td><td>$row[2]</td><td>$row[3]</td><td>$row[4]</td>
</tr>
EOT;
            $n++;
         }
         if ($n === 0) {
            echo <<<EOT
<p>No intercom events.</p>
<p><a href="$prog?a=c">Add Event</a></p>
EOT;

            die();
         }
         else {
            print <<<EOT
<table class="center" border="1">
<tr>
<th>Actions</th><th>Start Time</th><th>End Time</th><th>Message</th><th>Status</th>
</tr>
$s
</table>
<p><a href="$prog?a=c">Add Event</a></p>
EOT;
         }
      }
   }
   else {
      if (isset($_POST['a'])) {
         $a = clean_string($_POST['a']);
      
         if ($a === 'c') {
            try {
               $sql = "insert into $table (dt_start, dt_end, message, type) values (?, ?, ?, ?)";
               $sth = $dbh->prepare($sql);
               $sth->execute([ clean_time($_POST['dt_start']), clean_time($_POST['dt_end']), clean($_POST['message']), clean_string($_POST['type'] ) ]);
            } catch (PDOException $e) {
               $err_code = $e->getCode();
               if ($err_code === '22007' || $err_code === '22008') { # pgsql error codes see https://www.postgresql.org/docs/10/static/errcodes-appendix.html
                  echo "error: date format or range: ".$e->getMessage(); 
               }
               else {
                  echo $e->getMessage();
               }
               die();
            }
         }
         else if ($a === 'u') {
            try {
               $sql = "update $table set dt_start=?, dt_end=?, message=?, type=? where id=?";
               $sth = $dbh->prepare($sql);
               $sth->execute([ clean_time($_POST['dt_start']), clean_time($_POST['dt_end']), clean($_POST['message']), clean_string($_POST['type']), clean_num($_POST['id']) ]);
             } catch (PDOException $e) {
               $err_code = $e->getCode();
               if ($err_code === '22007' || $err_code === '22008') { # pgsql error codes see https://www.postgresql.org/docs/10/static/errcodes-appendix.html
                  echo "error: date format or range: ".$e->getMessage(); 
               }
               else {
                  echo $e->getMessage();
               }
               die();
            }
         }
         else {
            echo <<<EOT
<p>Error: invalid query parameter action: not 'crud'.</p>
$view_events_link
EOT;
            die();
         }

         $n = $sth->rowCount();
         echo <<<EOT
<p>Success: $n rows updated.</p>
$view_events_link
EOT;
      }
   }

function clean($s) {
   # if you want to display HTML tags or special characters in the message and are using SSL with authentication, then you can remove strip_tags() below
   return trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 _.!-]/', ' ', urldecode(html_entity_decode(strip_tags($s))))));
}

function clean_string($s) {
   return trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z]/', ' ', urldecode(html_entity_decode(strip_tags($s))))));
}

function clean_time($s) {
   return trim(preg_replace('/ +/', ' ', preg_replace('/[^0-9 :-]/', ' ', urldecode(html_entity_decode(strip_tags($s))))));
}

function clean_num($s) {
   return trim(preg_replace('/ +/', ' ', preg_replace('/[^0-9]/', ' ', urldecode(html_entity_decode(strip_tags($s))))));
}

function dynamic_select($the_array, $element_name, $label = '', $init_value = '') {
    $menu = '';
    if ($label != '') $menu .= '
    	<label for="'.$element_name.'">'.$label.'</label>';
    $menu .= '
    	<select name="'.$element_name.'" id="'.$element_name.'">';
    if (empty($_REQUEST[$element_name])) {
        $curr_val = $init_value;
    } else {
        $curr_val = $_REQUEST[$element_name];
    }
    foreach ($the_array as $key => $value) {
        $menu .= '
			<option value="'.$key.'"';
        if ($key === $curr_val) $menu .= ' selected="selected"';
        $menu .= '>'.$value.'</option>';
    }
    $menu .= '
    	</select>';
    return $menu;
}
?>
<p><a href="https://github.com/jamesbriggs/maintenance_banner">mx_banner</a> &copy; 2018</p>
</body>
</html>
