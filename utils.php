<?php

  /**
    * Find element by type: MNEM, UNIT, DATA, DESC
    * @param string $type
    * @param string $str
    * @param bool $trim
    * @return string
    */
function getLASElement($type, $str, $trim) {
  switch ($type) {
    case 'MNEM':
      $str = substr($str, 0, strpos($str, '.'));
      break;
    case 'UNIT':
      $str = substr($str, strpos($str, '.') + 1, ( strpos($str, ' ') - strpos($str, '.') + 1));
      break;
    case 'DATA':
      // hook for "FLD "
      if(getLASElement('MNEM', $str) == 'FLD ')
        $str = substr($str, strpos($str, ' ') + 3,  (strpos($str, ':') - strpos($str, ' ') - 2));
      $str = substr($str, strpos($str, ' ') + 1,  (strpos($str, ':') - strpos($str, ' ') - 1));

      break;
    case 'DESC':
      $str = substr($str, strpos($str, ':') + 1);
      break;
    default:
      $str = null;
      break;
  }
  return $trim ? trim($str) : $str;
}


/**
 * Translit table
 * @return array
 */
function getTranslitTable() {
    $tmp = array(
    'А' => 'A',
    'Б' => 'B',
    'В' => 'V',
    'Г' => 'G',
    'Д' => 'D',
    'Е' => 'E',
    'Ё' => 'JO',
    'Ж' => 'ZH',
    'З' => 'Z',
    'И' => 'I',
    'Й' => 'JJ',
    'К' => 'K',
    'Л' => 'L',
    'М' => 'M',
    'Н' => 'N',
    'О' => 'O',
    'П' => 'P',
    'Р' => 'R',
    'С' => 'S',
    'Т' => 'T',
    'У' => 'U',
    'Ф' => 'F',
    'Х' => 'KH',
    'Ц' => 'C',
    'Ч' => 'CH',
    'Ш' => 'SH',
    'Щ' => 'HH',
    'Ъ' => '“',
    'Ы' => 'Y',
    'Ь' => '‘',
    'Э' => 'EH',
    'Ю' => 'JU',
    'Я' => 'JA'
    );
    return $tmp;
  }
  ?>
