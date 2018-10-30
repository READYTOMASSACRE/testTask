<?php
  header('Content-Type: text/html; charset=utf-8');
  include 'utils.php';

  justDoIt();


  /**
    * Init converting. Execute read and write to files
    * @return nothing
    */
  function justDoIt() {
    $input = 'Input/';
    $output = 'Output/';
    $path = array(
      '1/01.LAS',
      '1/02.LAS',
      '20/03.LAS',
      '20/04.LAS'
    );

  foreach($path as $f) {
    $file = file($input.$f);
    convert($file);
    if(file_exists($output.$f))
      file_put_contents($output.$f, $file);
    else {
      $dir = substr($f, 0, strpos($f, '/'));
      mkdir($output.$dir, 0777, true);
      file_put_contents($output.$f, $file);
    }
  }
}

  /**
    * convert array with needle instructions
    * @param array &$arr
    * @return nothing
    */
  function convert(&$arr) {

    foreach ($arr as $key => $value) {
      $arr[$key] = iconv('CP866', 'utf-8', $value);
    }

    deleteOnceFromArray($arr, 'comment');
    deleteOnceFromArray($arr, 'MNEM', 'MEST');
    translit($arr, array('FLD ', 'COMP', 'NAME'));
    changeDescText($arr, 'NAME', 'Компания оцифровщик каротажа');

    foreach ($arr as $key => $value) {
      $arr[$key] = iconv('utf-8', 'CP866', $value);
    }

  }

  /**
    * Delete from LAS file Data with different type, such as 'MNEM, UNIT, DATA, DESC'
    * @param array &$arr
    * @param string $type
    * @param string $data
    * @return bool
    */
  function deleteOnceFromArray(&$arr, $type, $data) {
    foreach($arr as $key => $value) {
      if($type == 'comment') {
        if($value[0] == '~' && $value[1] == 'W') break;
        else if($value[0] == '#') {
          unset($arr[$key]);
        }
      }
      else if ($data == getLASElement($type, $value)) {
          unset($arr[$key]);
          break;
        }
    }
    return true;
  }

  /**
    * Change text in type 'DESC'
    * @param array &$arr
    * @param string $field
    * @param string $data
    * @return nothing
    */
  function changeDescText(&$arr, $field, $data) {
    foreach($arr as $key => $value) {
      if ($field == getLASElement('MNEM', $value)) {
        $arr[$key] = getLASElement('MNEM', $value).'.'.getLASElement('UNIT', $value).' '.getLASElement('DATA', $value).':"'.$data."\"\n";
        break;
      }
    }
  }

  /**
    * Init translit and set result in array
    * @param array &$arr
    * @param string $fields
    * @return nothing
    */
  function translit(&$arr, $fields) {
    $i = 0;
    foreach($arr as $key => $value) {
      if($i == count($fields)) break;
      if(in_array(getLASElement('MNEM', $value), $fields)) {
        $i++;
        $data = getLASElement('DATA', $value);
        $arr[$key] = getLASElement('MNEM', $value).'.'.getLASElement('UNIT', $value).' '.parseToTranslit($data).':'.getLASElement('DESC', $value);//."\n";
      }
    }
  }

  /**
    * Parsing cyrillic by translit table
    * @param string $stroke
    * @return string
    */
 function parseToTranslit($stroke) {
   $translitArr = getTranslitTable();
   $str = '';
   for($i = 0; $i < strlen($stroke); $i++) {
     $char = mb_substr($stroke, $i, 1, 'UTF-8');
     $tmp = preg_match('~^[А-ЯЁ\W]~u', $char) ? true : false;
     $char = mb_strtoupper($char, 'UTF-8');
     if ($char != ' ')
      $str .= $tmp ? $translitArr[$char] : mb_strtolower($translitArr[$char]);
      else $str .= ' ';
    }
    return $str;
  }

?>
