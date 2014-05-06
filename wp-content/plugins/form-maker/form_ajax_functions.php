<?php
/**
 * @package Form Maker
 * @author Web-Dorado
 * @copyright (C) 2011 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

// Direct access must be allowed.
// Generete CSV.
function form_maker_generete_csv() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  if (isset($_GET['action']) && esc_html($_GET['action']) == 'formmakergeneretecsv') {
    $is_paypal_info = FALSE;
    global $wpdb;
    $form_id = $_REQUEST['form_id'];
    $paypal_info_fields = array('currency', 'ord_last_modified', 'status', 'full_name', 'fax', 'mobile_phone', 'email', 'phone', 'address', 'paypal_info',  'ipn', 'tax', 'shipping');
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "formmaker_submits where form_id= %d ORDER BY date ASC", $form_id);
    $rows = $wpdb->get_results($query);
    $n = count($rows);
    $labels = array();
    for ($i = 0; $i < $n; $i++) {
      $row = &$rows[$i];
      if (!in_array($row->element_label, $labels)) {
        array_push($labels, $row->element_label);
      }
    }
    $label_titles = array();
    $sorted_labels = array();
    $query_lable = $wpdb->prepare("SELECT label_order,title FROM " . $wpdb->prefix . "formmaker where id= %d", $form_id);
    $rows_lable = $wpdb->get_results($query_lable);
    $ptn = "/[^a-zA-Z0-9_]/";
    $rpltxt = "";
    $title = preg_replace($ptn, $rpltxt, $rows_lable[0]->title);
    $sorted_labels_id = array();
    $sorted_labels = array();
    $label_titles = array();
    if ($labels) {
      $label_id = array();
      $label_order = array();
      $label_order_original = array();
      $label_type = array();
      ///stexic
      $label_all = explode('#****#', $rows_lable[0]->label_order);
      $label_all = array_slice($label_all, 0, count($label_all) - 1);
      foreach ($label_all as $key => $label_each) {
        $label_id_each = explode('#**id**#', $label_each);
        array_push($label_id, $label_id_each[0]);
        $label_oder_each = explode('#**label**#', $label_id_each[1]);
        array_push($label_order_original, $label_oder_each[0]);
        $ptn = "/[^a-zA-Z0-9_]/";
        $rpltxt = "";
        $label_temp = preg_replace($ptn, $rpltxt, $label_oder_each[0]);
        array_push($label_order, $label_temp);
        array_push($label_type, $label_oder_each[1]);
      }
      foreach ($label_id as $key => $label) {
        if (in_array($label, $labels)) {
          array_push($sorted_labels, $label_order[$key]);
          array_push($sorted_labels_id, $label);
          array_push($label_titles, $label_order_original[$key]);
        }
      }
    }
    $m = count($sorted_labels);
    $group_id_s = array();
    $l = 0;
    if (count($rows) > 0 and $m)
      for ($i = 0; $i < count($rows); $i++) {
        $row = &$rows[$i];
        if (!in_array($row->group_id, $group_id_s)) {
          array_push($group_id_s, $row->group_id);
        }
      }
    $data = array();
    $temp_all = array();
    for ($j = 0; $j < $n; $j++) {
      $row = &$rows[$j];
      $key = $row->group_id;
      if (!isset($temp_all[$key])) {
        $temp_all[$key] = array();
      }
      array_push($temp_all[$key], $row);
    }
    for ($www = 0; $www < count($group_id_s); $www++) {
      $i = $group_id_s[$www];
      $temp = array();
      $temp = $temp_all[$i];
      $f = $temp[0];
      $date = $f->date;
      $ip = $f->ip;
      $data_temp['Submit date'] = $date;
      $data_temp['Ip'] = $ip;
      $ttt = count($temp);
      for ($h = 0; $h < $m; $h++) {
        $data_temp[$label_titles[$h]] = '';
        for ($g = 0; $g < $ttt; $g++) {
          $t = $temp[$g];
          if ($t->element_label == $sorted_labels_id[$h]) {
            if (strpos($t->element_value, "*@@url@@*")) {
              $new_file = str_replace("*@@url@@*", '', $t->element_value);
              $new_filename = explode('/', $new_file);
              $data_temp[$label_titles[$h]] = $new_file;
            }
            elseif (strpos($t->element_value, "***br***")) {
              $data_temp[$label_titles[$h]] = substr(str_replace("***br***", ', ', $t->element_value), 0, -2);
            }
            elseif (strpos($t->element_value, "***map***")) {
              $data_temp[$label_titles[$h]] = 'Longitude:' . substr(str_replace("***map***", ', Latitude:', $t->element_value), 0, -2);
            }
            elseif (strpos($t->element_value,"***star_rating***")) {
              $element = str_replace("***star_rating***", '', $t->element_value);
							$element = explode("***", $element);
							$data_temp[stripslashes($label_titles[$h])] = ' ' . $element[1] . '/' . $element[0];
						}
            elseif (strpos($t->element_value, "***grading***")) {
              $element = str_replace("***grading***", '', $t->element_value);
              $grading = explode(":", $element);
							$items_count = sizeof($grading) - 1;
							$items = "";
							$total = "";
              for ($k = 0; $k < $items_count / 2; $k++) {
                $items .= $grading[$items_count / 2 + $k] . ": " . $grading[$k] . ", ";
                $total += $grading[$k];
              }
              $items .= "Total: " . $total;
							$data_temp[$label_titles[$h]] = $items;
						}
            elseif (strpos($t->element_value, "***matrix***")) {
              $element = str_replace("***matrix***", '', $t->element_value);
              $matrix_value = explode('***', $element);
              $matrix_value = array_slice($matrix_value, 0, count($matrix_value) - 1);
							$mat_rows = $matrix_value[0];
							$mat_columns = $matrix_value[$mat_rows + 1];
							$matrix = "";
							$aaa = Array();
              $var_checkbox = 1;
							$selected_value = "";
              $selected_value_yes = "";
              $selected_value_no = "";
							for ($k = 1; $k <= $mat_rows; $k++) {
							  if ($matrix_value[$mat_rows + $mat_columns + 2] == "radio") {
                  if ($matrix_value[$mat_rows + $mat_columns + 2 + $k] == 0) {
                    $checked = "0";
                    $aaa[1] = "";
									}
                  else {
                    $aaa = explode("_", $matrix_value[$mat_rows + $mat_columns + 2 + $k]);
                  }
                  for ($l = 1; $l <= $mat_columns; $l++) {
										if ($aaa[1] == $l) {
									    $checked = '1';
                    }
                    else {
									    $checked = '0';
                    }
						        $matrix .= '['.$matrix_value[$k].','.$matrix_value[$mat_rows+1+$l].']='.$checked."; ";
					        }
						    }
								else {
                  if ($matrix_value[$mat_rows+$mat_columns + 2] == "checkbox") {
                    for ($l = 1; $l <= $mat_columns; $l++) {
                      if ($matrix_value[$mat_rows+$mat_columns + 2 + $var_checkbox] == 1) {
                        $checked = '1';
                      }
                      else {
                        $checked = '0';
                      }
							        $matrix .= '['.$matrix_value[$k].','.$matrix_value[$mat_rows+1+$l].']='.$checked."; ";
                      $var_checkbox++;
                    }
                  }
                  else {
                    if ($matrix_value[$mat_rows+$mat_columns + 2] == "text") {
							        for ($l = 1; $l <= $mat_columns; $l++) {
                        $text_value = $matrix_value[$mat_rows+$mat_columns+2+$var_checkbox];
                        $matrix .='['.$matrix_value[$k].','.$matrix_value[$mat_rows+1+$l].']='.$text_value."; ";
                        $var_checkbox++;
                      }
                    }
                    else {
                      for ($l = 1; $l <= $mat_columns; $l++) {
                        $selected_text = $matrix_value[$mat_rows+$mat_columns + 2 + $var_checkbox];
                        $matrix .= '['.$matrix_value[$k].','.$matrix_value[$mat_rows + 1 + $l].']='.$selected_text."; ";
                        $var_checkbox++;
                      }
                    }
                  }
								}
							}
							$data_temp[stripslashes($label_titles[$h])] = $matrix;
						}
            else {
              $val = htmlspecialchars_decode($t->element_value);
              $val = stripslashes(str_replace('&#039;', "'", $val));
              $data_temp[stripslashes($label_titles[$h])] = ($t->element_value ? $val : '');
            }
          }
        }
      }
      $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "formmaker_sessions where group_id= %d", $f->group_id);
      $paypal_info = $wpdb->get_results($query);
      if ($paypal_info) {
        $is_paypal_info = TRUE;
      }
      if ($is_paypal_info) {
        foreach ($paypal_info_fields as $paypal_info_field)	{
          if ($paypal_info) {
            $data_temp['PAYPAL_' . $paypal_info_field] = $paypal_info[0]->$paypal_info_field;
          }
          else {
            $data_temp['PAYPAL_' . $paypal_info_field] = '';
          }
        }
      }
      $data[] = $data_temp;
    }
    function cleanData(&$str) {
      $str = preg_replace("/\t/", "\\t", $str);
      $str = preg_replace("/\r?\n/", "\\n", $str);
      if (strstr($str, '"'))
        $str = '"' . str_replace('"', '""', $str) . '"';
    }
    // File name for download.
    $filename = $title . "_" . date('Ymd') . ".csv";
    header('Content-Encoding: Windows-1252');
    header('Content-type: text/csv; charset=Windows-1252');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $flag = FALSE;
    foreach ($data as $row) {
      if (!$flag) {
        # display field/column names as first row
        echo "sep=,\r\n";
        echo '"' . implode('","', array_keys($row));
        if ($is_paypal_info) {
          echo '","Currency","Last modified","Status","Full Name","Fax","Mobile phone","Email","Phone","Address","Paypal info","IPN","Tax","Shipping';
        }  
        echo "\"\r\n";
        $flag = TRUE;
      }
      array_walk($row, 'cleanData');
      echo '"' . implode('","', array_values($row)) . "\"\r\n";
    }
    die('');
  }
}

// Generete XML.
function form_maker_generete_xml() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  if (isset($_GET['action']) && esc_html($_GET['action']) == 'formmakergeneretexml') {
    $is_paypal_info = FALSE;
    global $wpdb;
    $form_id = $_REQUEST['form_id'];
    $paypal_info_fields = array('ip', 'ord_date', 'ord_last_modified', 'status', 'full_name', 'fax', 'mobile_phone', 'email', 'phone', 'address', 'paypal_info', 'without_paypal_info', 'ipn', 'checkout_method', 'tax', 'shipping', 'shipping_type', 'read');
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "formmaker_submits where form_id= %d ORDER BY date ASC", $form_id);
    $rows = $wpdb->get_results($query);
    $n = count($rows);
    $labels = array();
    for ($i = 0; $i < $n; $i++) {
      $row = &$rows[$i];
      if (!in_array($row->element_label, $labels)) {
        array_push($labels, $row->element_label);
      }
    }
    $label_titles = array();
    $sorted_labels = array();
    $query_lable = "SELECT label_order,title FROM " . $wpdb->prefix . "formmaker where id=$form_id ";
    $rows_lable = $wpdb->get_results($query_lable);
    $ptn = "/[^a-zA-Z0-9_]/";
    $rpltxt = "";
    $title = preg_replace($ptn, $rpltxt, $rows_lable[0]->title);
    $sorted_labels_id = array();
    $sorted_labels = array();
    $label_titles = array();
    if ($labels) {
      $label_id = array();
      $label_order = array();
      $label_order_original = array();
      $label_type = array();
      $label_all = explode('#****#', $rows_lable[0]->label_order);
      $label_all = array_slice($label_all, 0, count($label_all) - 1);
      foreach ($label_all as $key => $label_each) {
        $label_id_each = explode('#**id**#', $label_each);
        array_push($label_id, $label_id_each[0]);
        $label_oder_each = explode('#**label**#', $label_id_each[1]);
        array_push($label_order_original, $label_oder_each[0]);
        $ptn = "/[^a-zA-Z0-9_]/";
        $rpltxt = "";
        $label_temp = preg_replace($ptn, $rpltxt, $label_oder_each[0]);
        array_push($label_order, $label_temp);
        array_push($label_type, $label_oder_each[1]);
      }
      foreach ($label_id as $key => $label) {
        if (in_array($label, $labels)) {
          array_push($sorted_labels, $label_order[$key]);
          array_push($sorted_labels_id, $label);
          array_push($label_titles, $label_order_original[$key]);
        }
      }
    }
    $m = count($sorted_labels);
    $group_id_s = array();
    $l = 0;
    if (count($rows) > 0 and $m) {
      for ($i = 0; $i < count($rows); $i++) {
        $row = &$rows[$i];
        if (!in_array($row->group_id, $group_id_s)) {
          array_push($group_id_s, $row->group_id);
        }
      }
    }
    $data = array();
    $temp_all = array();
    for ($j = 0; $j < $n; $j++) {
      $row = &$rows[$j];
      $key = $row->group_id;
      if (!isset($temp_all[$key])) {
        $temp_all[$key] = array();
      }
      array_push($temp_all[$key], $row);
    }
    for ($www = 0; $www < count($group_id_s); $www++) {
      $i = $group_id_s[$www];
      $temp = array();
      $temp = $temp_all[$i];
      $f = $temp[0];
      $date = $f->date;
      $ip = $f->ip;
      $data_temp['Submit date'] = $date;
      $data_temp['Ip'] = $ip;
      $ttt = count($temp);
      for ($h = 0; $h < $m; $h++) {
        for ($g = 0; $g < $ttt; $g++) {
          $t = $temp[$g];
          if ($t->element_label == $sorted_labels_id[$h]) {
            if (strpos($t->element_value, "*@@url@@*")) {
              $new_file = str_replace("*@@url@@*", '', $t->element_value);
              $new_filename = explode('/', $new_file);
              $data_temp[$label_titles[$h]] = $new_file;
            }
            elseif (strpos($t->element_value, "***br***")) {
              $data_temp[$label_titles[$h]] = substr(str_replace("***br***", ', ', $t->element_value), 0, -2);
            }
            elseif (strpos($t->element_value, "***map***")) {
              $data_temp[$label_titles[$h]] = 'Longitude:' . substr(str_replace("***map***", ', Latitude:', $t->element_value), 0, -2);
            }
            elseif (strpos($t->element_value,"***star_rating***")) {
              $element = str_replace("***star_rating***", '', $t->element_value);
							$element = explode("***", $element);
							$data_temp[stripslashes($label_titles[$h])] = ' ' . $element[1] . '/' . $element[0];
						}
            elseif (strpos($t->element_value, "***grading***")) {
              $element = str_replace("***grading***", '', $t->element_value);
              $grading = explode(":", $element);
							$items_count = sizeof($grading) - 1;
							$items = "";
							$total = "";
              for ($k = 0; $k < $items_count / 2; $k++) {
                $items .= $grading[$items_count / 2 + $k] . ": " . $grading[$k] . ", ";
                $total += $grading[$k];
              }
              $items .= "Total: " . $total;
							$data_temp[$label_titles[$h]] = $items;
						}
            elseif (strpos($t->element_value, "***matrix***")) {
              $element = str_replace("***matrix***", '', $t->element_value);
              $matrix_value = explode('***', $element);
              $matrix_value = array_slice($matrix_value, 0, count($matrix_value) - 1);
							$mat_rows = $matrix_value[0];
							$mat_columns = $matrix_value[$mat_rows + 1];
							$matrix = "";
							$aaa = Array();
              $var_checkbox = 1;
							$selected_value = "";
              $selected_value_yes = "";
              $selected_value_no = "";
							for ($k = 1; $k <= $mat_rows; $k++) {
							  if ($matrix_value[$mat_rows + $mat_columns + 2] == "radio") {
                  if ($matrix_value[$mat_rows + $mat_columns + 2 + $k] == 0) {
                    $checked = "0";
                    $aaa[1] = "";
									}
                  else {
                    $aaa = explode("_", $matrix_value[$mat_rows + $mat_columns + 2 + $k]);
                  }
                  for ($l = 1; $l <= $mat_columns; $l++) {
										if ($aaa[1] == $l) {
									    $checked = '1';
                    }
                    else {
									    $checked = '0';
                    }
						        $matrix .= '['.$matrix_value[$k].','.$matrix_value[$mat_rows+1+$l].']='.$checked."; ";
					        }
						    }
								else {
                  if ($matrix_value[$mat_rows+$mat_columns + 2] == "checkbox") {
                    for ($l = 1; $l <= $mat_columns; $l++) {
                      if ($matrix_value[$mat_rows+$mat_columns + 2 + $var_checkbox] == 1) {
                        $checked = '1';
                      }
                      else {
                        $checked = '0';
                      }
							        $matrix .= '['.$matrix_value[$k].','.$matrix_value[$mat_rows+1+$l].']='.$checked."; ";
                      $var_checkbox++;
                    }
                  }
                  else {
                    if ($matrix_value[$mat_rows+$mat_columns + 2] == "text") {
							        for ($l = 1; $l <= $mat_columns; $l++) {
                        $text_value = $matrix_value[$mat_rows+$mat_columns+2+$var_checkbox];
                        $matrix .='['.$matrix_value[$k].','.$matrix_value[$mat_rows+1+$l].']='.$text_value."; ";
                        $var_checkbox++;
                      }
                    }
                    else {
                      for ($l = 1; $l <= $mat_columns; $l++) {
                        $selected_text = $matrix_value[$mat_rows+$mat_columns + 2 + $var_checkbox];
                        $matrix .= '['.$matrix_value[$k].','.$matrix_value[$mat_rows + 1 + $l].']='.$selected_text."; ";
                        $var_checkbox++;
                      }
                    }
                  }
								}
							}
							$data_temp[stripslashes($label_titles[$h])] = $matrix;
						}
            else {
              $val = str_replace('&amp;', "&", $t->element_value);
              $val = stripslashes(str_replace('&#039;', "'", $t->element_value));
              $data_temp[stripslashes($label_titles[$h])] = ($t->element_value ? $val : '');
            }
          }
        }
      }
      $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "formmaker_sessions where group_id= %d", $f->group_id);
      $paypal_info = $wpdb->get_results($query);
      if ($paypal_info) {
        $is_paypal_info = TRUE;
      }
      if ($is_paypal_info) {
        foreach ($paypal_info_fields as $paypal_info_field)	{
          if ($paypal_info) {
            $data_temp['PAYPAL_' . $paypal_info_field] = $paypal_info[0]->$paypal_info_field;
          }
          else {
            $data_temp['PAYPAL_' . $paypal_info_field] = '';
          }
        }
      }
      $data[] = $data_temp;
    }
    function cleanData(&$str) {
      $str = preg_replace("/\t/", "\\t", $str);
      $str = preg_replace("/\r?\n/", "\\n", $str);
      if (strstr($str, '"'))
        $str = '"' . str_replace('"', '""', $str) . '"';
    }

    $filename = $title . "_" . date('Ymd') . ".xml";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type:text/xml,  charset=utf-8");
    $flag = FALSE;
    echo '<?xml version="1.0" encoding="utf-8" ?> 
  <form title="' . $title . '">';
    foreach ($data as $key1 => $value1) {
      echo  '<submission>';
      foreach ($value1 as $key => $value) {
        echo  '<field title="' . $key . '">';
        echo   '<![CDATA[' . $value . "]]>";
        echo  '</field>';
      }
      echo  '</submission>';
    }
    echo ' </form>';
    die('');
  }
}

// Captcha.
function form_maker_wd_captcha() {
  if (isset($_GET['action']) && esc_html($_GET['action']) == 'formmakerwdcaptcha') {
    if (isset($_GET["i"])) {
      $i = (int) $_GET["i"];
    }
    else {
      $i = '';
    }
    if (isset($_GET['r2'])) {
      $r2 = (int) $_GET['r2'];
    }
    else {
      $r2 = 0;
    }
    if (isset($_GET['r'])) {
      $rrr = (int) $_GET['r'];
    }
    else {
      $rrr = 0;
    }
    $randNum = 0 + $r2 + $rrr;
    if (isset($_GET["digit"])) {
      $digit = (int) $_GET["digit"];
    }
    else {
      $digit = 6;
    }
    $cap_width = $digit * 10 + 15;
    $cap_height = 30;
    $cap_quality = 100;
    $cap_length_min = $digit;
    $cap_length_max = $digit;
    $cap_digital = 1;
    $cap_latin_char = 1;
    function code_generic($_length, $_digital = 1, $_latin_char = 1) {
      $dig = array(
        0,
        1,
        2,
        3,
        4,
        5,
        6,
        7,
        8,
        9
      );
      $lat = array(
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z'
      );
      $main = array();
      if ($_digital) {
        $main = array_merge($main, $dig);
      }
      if ($_latin_char) {
        $main = array_merge($main, $lat);
      }
      shuffle($main);
      $pass = substr(implode('', $main), 0, $_length);
      return $pass;
    }
    $l = rand($cap_length_min, $cap_length_max);
    $code = code_generic($l, $cap_digital, $cap_latin_char);
    @session_start();
    $_SESSION[$i . '_wd_captcha_code'] = $code;
    $canvas = imagecreatetruecolor($cap_width, $cap_height);
    $c = imagecolorallocate($canvas, rand(150, 255), rand(150, 255), rand(150, 255));
    imagefilledrectangle($canvas, 0, 0, $cap_width, $cap_height, $c);
    $count = strlen($code);
    $color_text = imagecolorallocate($canvas, 0, 0, 0);
    for ($it = 0; $it < $count; $it++) {
      $letter = $code[$it];
      imagestring($canvas, 6, (10 * $it + 10), $cap_height / 4, $letter, $color_text);
    }
    for ($c = 0; $c < 150; $c++) {
      $x = rand(0, $cap_width - 1);
      $y = rand(0, 29);
      $col = '0x' . rand(0, 9) . '0' . rand(0, 9) . '0' . rand(0, 9) . '0';
      imagesetpixel($canvas, $x, $y, $col);
    }
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', FALSE);
    header('Pragma: no-cache');
    header('Content-Type: image/jpeg');
    imagejpeg($canvas, NULL, $cap_quality);
    die('');
  }
}

// Function post or page window php.
function form_maker_window_php() {
  if (isset($_GET['action']) && esc_html($_GET['action']) == 'formmakerwindow') {
    global $wpdb;
    ?>
  <html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Form Maker</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script language="javascript" type="text/javascript"
            src="<?php echo get_option("siteurl"); ?>/wp-includes/js/jquery/jquery.js"></script>
    <script language="javascript" type="text/javascript"
            src="<?php echo get_option("siteurl"); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
    <script language="javascript" type="text/javascript"
            src="<?php echo get_option("siteurl"); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
    <script language="javascript" type="text/javascript"
            src="<?php echo get_option("siteurl"); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
    <base target="_self">
  </head>
  <body id="link" style="" dir="ltr" class="forceColors">
  <div class="tabs" role="tablist" tabindex="-1">
    <ul>
      <li id="form_maker_tab" class="current" role="tab" tabindex="0"><span><a
        href="javascript:mcTabs.displayTab('Single_product_tab','Single_product_panel');" onMouseDown="return false;"
        tabindex="-1">Form Maker</a></span></li>
    </ul>
  </div>
  <style>
    .panel_wrapper {
      height: 170px !important;
    }
  </style>
  <div class="panel_wrapper">
    <div id="Single_product_panel" class="panel current">
      <table>
        <tr>
          <td style="vertical-align: middle;">
            Select a Form
          </td>
          <td style="vertical-align: middle;">
            <select name="Form_Makername" id="Form_Makername" style="width:230px; text-align:center">
              <option style="text-align:center" value="- Select Form -" selected="selected">- Select a Form -</option>
              <?php    $ids_Form_Maker = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "formmaker order by `id` DESC");
              foreach ($ids_Form_Maker as $arr_Form_Maker) {
                ?>
                <option value="<?php echo $arr_Form_Maker->id; ?>"><?php echo $arr_Form_Maker->title; ?></option>
                <?php }?>
            </select>
          </td>
        </tr>
      </table>
    </div>
  </div>
  <div class="mceActionPanel">
    <div style="float: left">
      <input type="button" id="cancel" name="cancel" value="Cancel" onClick="tinyMCEPopup.close();"/>
    </div>

    <div style="float: right">
      <input type="submit" id="insert" name="insert" value="Insert" onClick="insert_Form_Maker();"/>
    </div>
  </div>
  <script type="text/javascript">
    function insert_Form_Maker() {
      if (document.getElementById('Form_Makername').value == '- Select Form -') {
        tinyMCEPopup.close();
      }
      else {
        var tagtext;
        tagtext = '[Form id="' + document.getElementById('Form_Makername').value + '"]';
        window.tinyMCE.execCommand('mceInsertContent', false, tagtext);
        tinyMCEPopup.close();
      }

    }

  </script>
  </body>
  </html>
  <?php
    die('');
  }
}

// Paypal info.
function paypal_info() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  global $wpdb;
  if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
  }
	$query = "SELECT * FROM " . $wpdb->prefix . "formmaker_sessions where group_id=" . $id;
	$row = $wpdb->get_row($query);
	if (!$row) {
		echo "<div style='width:100%; text-align:center; height: 70%; vertical-align:middle'>
            <h1 style='vertical-align:middle; margin:auto; color:#000'><p>No information yet</p></h1>
          </div>";
    die();
	}
	html_paypal_info($row);
}

function html_paypal_info($row) {
  if (!isset($row->ipn)) {
    echo "<div style='width:100%; text-align:center; height: 70%; vertical-align:middle;'>
            <h1 style='vertical-align:middle; margin:auto; color:#000'><p>No information yet</p></h1>
          </div>";
    die();
  }
  ?>
  <style>
    table.admintable td.key, table.admintable td.paramlist_key {
      background-color: #F6F6F6;
      border-bottom: 1px solid #E9E9E9;
      border-right: 1px solid #E9E9E9;
      color: #666666;
      font-weight: bold;
      margin-right: 10px;
      text-align: right;
      width: 140px;
    }
  </style>
  <h2>Payment Info</h2>
  <table class="admintable">
    <tr>
      <td class="key">Currency</td>
      <td><?php echo $row->currency; ?></td>
    </tr>
    <tr>
      <td class="key">Last modified</td>
      <td><?php echo $row->ord_last_modified; ?></td>
    </tr>
    <tr>
      <td class="key">Status</td>
      <td><?php echo $row->status; ?></td>
    </tr>
    <tr>
      <td class="key">Full name</td>
      <td><?php echo $row->full_name; ?></td>
    </tr>
    <tr>
      <td class="key">Email</td>
      <td><?php echo $row->email; ?></td>
    </tr>
    <tr>
      <td class="key">Phone</td>
      <td><?php echo $row->phone; ?></td>
    </tr>
    <tr>
      <td class="key">Mobile phone</td>
      <td><?php echo $row->mobile_phone; ?></td>
    </tr>
    <tr>
      <td class="key">Fax</td>
      <td><?php echo $row->fax; ?></td>
    </tr>
    <tr>
      <td class="key">Address</td>
      <td><?php echo $row->address; ?></td>
    </tr>
    <tr>
      <td class="key">Paypal info</td>
      <td><?php echo $row->paypal_info; ?></td>
    </tr>
    <tr>
      <td class="key">IPN</td>
      <td><?php echo $row->ipn; ?></td>
    </tr>
    <tr>
      <td class="key">Tax</td>
      <td><?php echo $row->tax; ?>%</td>
    </tr>
    <tr>
      <td class="key">Shipping</td>
      <td><?php echo $row->shipping; ?></td>
    </tr>
    <tr>
      <td class="key">Read</td>
      <td><?php echo $row->read; ?></td>
    </tr>
    <tr>
      <td class="key">Total</td>
      <td><b><?php echo $row->total; ?></b></td>
    </tr>
  </table>
  <?php
  die();
}

// Form preview from product options page.
function form_maker_form_preview_product_option() {
  global $wpdb;
  if (isset($_GET['id'])) {
    $getparams = (int) $_GET['id'];
  }
  if (isset($_GET['form_id'])) {
    $form_id = (int) $_GET['form_id'];
  }
  $query = "SELECT css FROM " . $wpdb->prefix . "formmaker_themes WHERE id=" . $getparams;
  $css = $wpdb->get_var($query);
  $query = "SELECT form_front FROM " . $wpdb->prefix . "formmaker WHERE id=" . $form_id;
  $form = $wpdb->get_var($query);
  html_form_maker_form_preview_product_option($css, $form);
}

function html_form_maker_form_preview_product_option($css, $form) {
  $cmpnt_js_path = plugins_url('js', __FILE__);
  $id = 'form_id_temp';
  echo "<input type='hidden' value='" . plugins_url("", __FILE__) . "' id='form_plugins_url' />";
  echo '<script type="text/javascript">
          if (document.getElementById("form_plugins_url")) {
            var plugin_url = document.getElementById("form_plugins_url").value;
          }
          else {
            var plugin_url = "";
          }
        </script>';
  ?>
  <script src="<?php echo $cmpnt_js_path . "/if_gmap_back_end.js"; ?>"></script>
  <script src="<?php echo $cmpnt_js_path . "/main.js"; ?>"></script>
  <script src="<?php echo $cmpnt_js_path . "/jquery-1.9.1.js"; ?>"></script>
  <script src="<?php echo $cmpnt_js_path . "/jquery-ui.js"; ?>"></script>
  <script src="<?php echo $cmpnt_js_path . "/jquery.ui.slider.js"; ?>"></script>
  <script src="<?php echo $cmpnt_js_path . "/main_front_end.js"; ?>"></script>
  <link media="all" type="text/css" href="<?php echo plugins_url('', __FILE__) . "/css/jquery-ui-spinner.css"; ?>" rel="stylesheet">
  <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
  <style>
      <?php
      $cmpnt_js_path = plugins_url('', __FILE__);
      echo str_replace('[SITE_ROOT]', $cmpnt_js_path, $css);
      ?>
  </style>
  <form id="form_preview"><?php echo $form ?></form>
  <?php
  die();
}

// Product options.
function form_maker_product_option() {
  if (isset($_GET['field_id'])) {
    $id = (int) $_GET['field_id'];
  }
  if (isset($_GET['property_id'])) {
    $property_id = (int) $_GET['property_id'];
  }
  if (isset($_GET['url_for_ajax'])) {
    $url_for_ajax = esc_html($_GET['url_for_ajax']);
  }
  else {
    $url_for_ajax = "aaaaaaa";
  }
  html_form_maker_product_option($id, $property_id, $url_for_ajax);
}

function html_form_maker_product_option($id, $property_id, $url_for_ajax) {
  wp_print_scripts('jquery');
  wp_print_scripts('jquery-ui-core');
  wp_print_scripts('jquery-ui-widget');
  wp_print_scripts('jquery-ui-mouse');
  wp_print_scripts('jquery-ui-slider');
  wp_print_scripts('jquery-ui-sortable');
  ?>
  <span style="position:fixed;right:10px">
    <img alt="ADD" title="add" style="cursor:pointer; vertical-align:middle; margin:5px;" src="<?php echo  plugins_url('images/save.png', __FILE__); ?>" onclick="save_options();">
    <img alt="CANCEL" title="cancel" style=" cursor:pointer; vertical-align:middle; margin:5px;" src="<?php echo  plugins_url('images/cancel_but.png', __FILE__); ?>" onclick="window.parent.tb_remove();">
  </span><br />
  <div style="margin-left:10px">
    <br />
    <fieldset>
      <legend>
        <label style="color: rgb(0, 174, 239); font-weight: bold; font-size: 13px;">Properties</label>
      </legend>
      <br />
      <div style="margin-left:10px">
        <label style="color: rgb(0, 174, 239); font-weight: bold; font-size: 13px; margin-right:20px">Type </label>
        <select id="option_type" style="width: 200px; border-width: 1px;" onchange="type_add_predefined(this.value)">
          <option value="Custom" selected="selected">Custom</option>
          <option value="Color">Color</option>
          <option value="T-Shirt Size">T-Shirt Size</option>
          <option value="Print Size">Print Size</option>
          <option value="Screen Resolution">Screen Resolution</option>
          <option value="Shoe Size">Shoe Size</option>
        </select>
        <br /><br />
        <label style="color: rgb(0, 174, 239); font-weight: bold; font-size: 13px; margin-right:20px">Title </label>
        <input type="text" style="width:200px" id="option_name"/>
        <br />
        <br />
        <label style="color: rgb(0, 174, 239); font-weight: bold; font-size: 13px;">Properties</label> &nbsp;
        <img id="el_choices_add" src="<?php echo  plugins_url('images/add.png', __FILE__); ?>" style="cursor: pointer;" title="add" onclick="add_choise_option()">
        <br />
        <div style="margin-left:0px" id="options"></div>
      </div>
    </fieldset>
  </div>
  <script>
  var j = 0;
  function save_options() {
    if (document.getElementById('option_name').value == '') {
      alert('The option must have a title')
      return;
    }
    <?php
    if (!isset($property_id)) {
    ?>
      for (i = 30; i >= 0; i--) {
        if (window.parent.document.getElementById(<?php echo $id ?>+"_propertyform_id_temp" + i)) {
          i = i + 1;
          select_ = document.createElement('select');
          select_.setAttribute("id", <?php echo $id ?>+"_propertyform_id_temp" + i);
          select_.setAttribute("name", <?php echo $id ?>+"_propertyform_id_temp" + i);
          select_.style.cssText = "width:auto; margin:2px 0px";
          break;
        }
      }
      if (i == -1) {
        i = 0;
        select_ = document.createElement('select');
        select_.setAttribute("id", <?php echo $id ?>+"_propertyform_id_temp" + i);
        select_.setAttribute("name", <?php echo $id ?>+"_propertyform_id_temp" + i);
        select_.style.cssText = "width:auto; margin:2px 0px";
      }
      for (k = 0; k <= 50; k++) {
        if (document.getElementById('el_option' + k)) {
          var option = document.createElement('option');
          option.setAttribute("id", "<?php echo $id ?>_" + i + "_option" + k);
          option.setAttribute("value", document.getElementById('el_option' + k).value);
          option.innerHTML = document.getElementById('el_option' + k).value;
          select_.appendChild(option);
        }
      }
      var select_label = document.createElement('label');
      select_label.innerHTML = document.getElementById('option_name').value;
      select_label.style.cssText = "margin-right:5px";
      select_label.setAttribute("class", 'mini_label');
      select_label.setAttribute("id", '<?php echo $id ?>_property_label_form_id_temp' + i);
      var span_ = document.createElement('span');
      span_.style.cssText = "margin-right:15px";
      span_.setAttribute("id", '<?php echo $id ?>_property_' + i);
      div_ = window.parent.document.getElementById("<?php echo $id ?>_divform_id_temp");
      span_.appendChild(select_label);
      span_.appendChild(select_);
      div_.appendChild(span_);
      var li_ = document.createElement('li');
      li_.setAttribute("id", 'property_li_' + i);
      var li_label = document.createElement('label');
      li_label.innerHTML = document.getElementById('option_name').value;
      li_label.setAttribute("id", 'label_property_' + i);
      li_label.style.cssText = "font-weight:bold; font-size: 13px";
      var li_edit = document.createElement('a');
      li_edit.setAttribute("href", "<?php echo $url_for_ajax ?>?action=product_option&field_id=<?php echo $id ?>&property_id=" + i + "&TB_iframe=1&url_for_ajax=<?php echo $url_for_ajax ?>");
      li_edit.setAttribute("class", "thickbox-preview20");
      var li_edit_img = document.createElement('img');
      li_edit_img.setAttribute("src", "<?php echo  plugins_url('images/edit.png', __FILE__); ?>");
      li_edit_img.style.cssText = "margin-left:13px;";
      li_edit.appendChild(li_edit_img);
      var li_x = document.createElement('img');
      li_x.setAttribute("src", "<?php echo  plugins_url('images/delete.png', __FILE__); ?>");
      li_x.setAttribute("onClick", 'remove_property(<?php echo $id ?>,' + i + ')');
      li_x.style.cssText = "margin-left:3px; cursor:pointer";
      ul_ = window.parent.document.getElementById("option_ul");
      li_.appendChild(li_label);
      li_.appendChild(li_edit);
      li_.appendChild(li_x);
      ul_.appendChild(li_);
      // Edit paypal properties page open in popup.
      window.parent.form_maker_edit_in_popup(20);
      <?php
    }
    else {
      ?>
      i =<?php echo $property_id ?>;
      var select_ = window.parent.document.getElementById('<?php echo $id ?>_propertyform_id_temp<?php echo $property_id ?>');
      select_.innerHTML = "";
      for (k = 0; k <= j; k++) {
        if (document.getElementById('el_option' + k)) {
          var option = document.createElement('option');
          option.setAttribute("id", "<?php echo $id ?>_" + i + "_option" + k);
          option.setAttribute("value", document.getElementById('el_option' + k).value);
          option.innerHTML = document.getElementById('el_option' + k).value;
          select_.appendChild(option);
        }
      }
      var select_label = document.createElement('label');
      select_label.innerHTML = document.getElementById('option_name').value;
      select_label.style.cssText = "margin-right:5px";
      select_label.setAttribute("class", 'mini_label');
      select_label.setAttribute("id", '<?php echo $id ?>_property_label_form_id_temp' + i);
      var span_ = window.parent.document.getElementById('<?php echo $id ?>_property_<?php echo $property_id ?>');
      span_.innerHTML = '';
      span_.appendChild(select_label);
      span_.appendChild(select_);
      window.parent.document.getElementById('label_property_<?php echo $property_id ?>').innerHTML = document.getElementById('option_name').value;
      <?php
    }
    ?>
    window.parent.tb_remove();
  }
  function type_add_predefined(type) {
    document.getElementById('options').innerHTML = '';
    switch (type) {
      case 'Custom': {
        w_choices = [];
        break;
      }

      case 'Color': {
        w_choices = ["Red", "Blue", "Green", "Yellow", "Black"];
        break;
      }

      case 'T-Shirt Size': {
        w_choices = ["XS", "S", "M", "L", "XL", "XXL", "XXXL"];
        break;
      }

      case 'Print Size': {
        w_choices = ["A4", "A3", "A2", "A1"];
        break;
      }

      case 'Screen Resolution': {
        w_choices = ["1024x768", "1152x864", "1280x768", "1280x800", "1280x960", "1280x1024", "1366x768", "1440x900", "1600x1200", "1680x1050", "1920x1080", "1920x1200"];
        break;
      }

      case 'Shoe Size': {
        w_choices = ["8", "8.5", "9", "9.5", "10", "10.5", "11", "11.5", "12", "13", "14"];
        break;
      }
    }
    type_add_options(w_choices);
  }
  function delete_options() {
    document.getElementById('options').innerHTML = '';
  }
  function type_add_options(w_choices) {
    i = 0;
    edit_main_td3 = document.getElementById('options');
    n = w_choices.length;
    for (j = 0; j < n; j++) {
      var br = document.createElement('br');
      br.setAttribute("id", "br" + j);
      var el_choices = document.createElement('input');
      el_choices.setAttribute("id", "el_option" + j);
      el_choices.setAttribute("type", "text");
      el_choices.setAttribute("value", w_choices[j]);
      el_choices.style.cssText = "width:100px; margin:0; padding:0; border-width: 1px";
      var el_choices_remove = document.createElement('img');
      el_choices_remove.setAttribute("id", "el_option" + j + "_remove");
      el_choices_remove.setAttribute("src", "<?php echo  plugins_url('images/delete.png', __FILE__); ?>");
      el_choices_remove.style.cssText = 'cursor:pointer; vertical-align:middle; margin:3px';
      el_choices_remove.setAttribute("align", 'top');
      el_choices_remove.setAttribute("onClick", "remove_option(" + j + "," + i + ")");
      edit_main_td3.appendChild(br);
      edit_main_td3.appendChild(el_choices);
      edit_main_td3.appendChild(el_choices_remove);
    }
  }
  function add_choise_option() {
    num = 0;
    j++;
    var choices_td = document.getElementById('options');
    var br = document.createElement('br');
    br.setAttribute("id", "br" + j);
    var el_choices = document.createElement('input');
    el_choices.setAttribute("id", "el_option" + j);
    el_choices.setAttribute("type", "text");
    el_choices.setAttribute("value", "");
    el_choices.style.cssText = "width:100px; margin:0; padding:0; border-width: 1px";
    var el_choices_remove = document.createElement('img');
    el_choices_remove.setAttribute("id", "el_option" + j + "_remove");
    el_choices_remove.setAttribute("src", "<?php echo  plugins_url('images/delete.png', __FILE__); ?>");
    el_choices_remove.style.cssText = 'cursor:pointer; vertical-align:middle; margin:3px';
    el_choices_remove.setAttribute("align", 'top');
    el_choices_remove.setAttribute("onClick", "remove_option('" + j + "','" + num + "')");
    choices_td.appendChild(br);
    choices_td.appendChild(el_choices);
    choices_td.appendChild(el_choices_remove);
  }
  function remove_option(id, num) {
    var choices_td = document.getElementById('options');
    var el_choices = document.getElementById('el_option' + id);
    var el_choices_remove = document.getElementById('el_option' + id + '_remove');
    var br = document.getElementById('br' + id);
    choices_td.removeChild(el_choices);
    choices_td.removeChild(el_choices_remove);
    choices_td.removeChild(br);
  }
  <?php
  if (isset($property_id)) {
  ?>
  label_ = window.parent.document.getElementById('<?php echo $id ?>_property_label_form_id_temp<?php echo $property_id ?>').innerHTML;
  select_ = window.parent.document.getElementById('<?php echo $id ?>_propertyform_id_temp<?php echo $property_id ?>');
  n = select_.childNodes.length;
  delete_options();
  w_choices = [ ];
  document.getElementById('option_name').value = label_;
  for (k = 0; k < n; k++) {
    w_choices.push(select_.childNodes[k].value);
  }
  type_add_options(w_choices);
    <?php
  }
  ?>
  </script>
  <?php
  die();
}

// Edit country in popup.
function spider_form_country_edit() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  if (isset($_GET['field_id'])) {
    $id = (int) $_GET['field_id'];
  }
  else {
    echo "<h2>Error. Cannot get field id.</h2>";
    return;
  }
  html_spider_form_country_edit($id);
}

function html_spider_form_country_edit($id) {
  wp_print_scripts('jquery');
  wp_print_scripts('jquery-ui-core');
  wp_print_scripts('jquery-ui-widget');
  wp_print_scripts('jquery-ui-mouse');
  wp_print_scripts('jquery-ui-slider');
  wp_print_scripts('jquery-ui-sortable');
  ?>

<span style=" position: absolute;right: 29px;">
<img alt="ADD" title="add" style="cursor:pointer; vertical-align:middle; margin:5px; "
     src="<?php echo  plugins_url('images/save.png', __FILE__); ?>" onClick="save_list()">
<img alt="CANCEL" title="cancel" style=" cursor:pointer; vertical-align:middle; margin:5px; "
     src="<?php echo  plugins_url('images/cancel_but.png', __FILE__); ?>" onClick="window.parent.tb_remove();">
</span>
<button onClick="select_all()">Select all</button>
<button onClick="remove_all()">Remove all</button>
<ul id="countries_list" style="list-style:none; padding:0px">
</ul>

<script>
  selec_coutries = [];
  coutries = ["", "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Central African Republic", "Chad", "Chile", "China", "Colombi", "Comoros", "Congo (Brazzaville)", "Congo", "Costa Rica", "Cote d'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor (Timor Timur)", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia, The", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, North", "Korea, South", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepa", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia and Montenegro", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "Spain", "Sri Lanka", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"];
  select_ = window.parent.document.getElementById('<?php echo $id ?>_elementform_id_temp');
  n = select_.childNodes.length;
  for (i = 0; i < n; i++) {
    selec_coutries.push(select_.childNodes[i].value);
    var ch = document.createElement('input');
    ch.setAttribute("type", "checkbox");
    ch.setAttribute("checked", "checked");
    ch.value = select_.childNodes[i].value;
    ch.id = i + "ch";
    //ch.setAttribute("id",i);
    var p = document.createElement('span');
    p.style.cssText = "color:#000000; font-size: 13px; cursor:move";
    p.innerHTML = select_.childNodes[i].value;
    var li = document.createElement('li');
    li.style.cssText = "margin:3px; vertical-align:middle";
    li.id = i;

    li.appendChild(ch);
    li.appendChild(p);

    document.getElementById('countries_list').appendChild(li);
  }
  cur = i;
  m = coutries.length;
  for (i = 0; i < m; i++) {
    isin = isValueInArray(selec_coutries, coutries[i]);

    if (!isin) {
      var ch = document.createElement('input');
      ch.setAttribute("type", "checkbox");
      ch.value = coutries[i];
      ch.id = cur + "ch";


      var p = document.createElement('span');
      p.style.cssText = "color:#000000; font-size: 13px; cursor:move";
      p.innerHTML = coutries[i];

      var li = document.createElement('li');
      li.style.cssText = "margin:3px; vertical-align:middle";
      li.id = cur;

      li.appendChild(ch);
      li.appendChild(p);

      document.getElementById('countries_list').appendChild(li);
      cur++;
    }
  }
  jQuery(function () {
    jQuery("#countries_list").sortable();
    jQuery("#countries_list").disableSelection();
  });

  function isValueInArray(arr, val) {
    inArray = false;
    for (x = 0; x < arr.length; x++)
      if (val == arr[x])
        inArray = true;
    return inArray;
  }
  function save_list() {
    select_.innerHTML = ""
    ul = document.getElementById('countries_list');
    n = ul.childNodes.length;
    for (i = 0; i < n; i++) {
      if (ul.childNodes[i].tagName == "LI") {
        id = ul.childNodes[i].id;
        if (document.getElementById(id + 'ch').checked) {
          var option_ = document.createElement('option');
          option_.setAttribute("value", document.getElementById(id + 'ch').value);
          option_.innerHTML = document.getElementById(id + 'ch').value;

          select_.appendChild(option_);
        }
      }
    }
    window.parent.tb_remove();
  }
  function select_all() {
    for (i = 0; i < 194; i++) {
      document.getElementById(i + 'ch').checked = true;
      ;
    }
  }
  function remove_all() {
    for (i = 0; i < 194; i++) {
      document.getElementById(i + 'ch').checked = false;
      ;
    }
  }
</script>
<?php
  die();
}
