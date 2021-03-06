<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/v1.0/engine/ctemplater.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/v1.0/engine/ctools.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/v1.0/engine/cform.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/v1.0/engine/settings.php";

	require_once $_SERVER['DOCUMENT_ROOT']."/iep/v1.0/iep/structures/user.class.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/v1.0/iep/consts/typeusers.consts.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/v1.0/iep/managers/gm.class.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/v1.0/iep/managers/um.class.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/v1.0/iep/managers/sbm.class.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/v1.0/iep/managers/sm.class.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/v1.0/iep/managers/nm.class.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/v1.0/iep/managers/shm.class.php";
	require_once $_SERVER['DOCUMENT_ROOT']."/iep/v1.0/iep/managers/sm.class.php";

  use IEP\Managers\UserManager;;
  use IEP\Managers\GroupManager;
  use IEP\Managers\SubjectManager;
  use IEP\Managers\SpecialtyManager;
  use IEP\Structures\Student;
  use IEP\Structures\Parent_;
  use IEP\Structures\Teacher;
  use IEP\Structures\Group;

	$CT = new CTemplater("templates/tpl", "templates/tpl_c", "templates/configs", "templates/cache");

	$DB = new PDO("mysql:dbname=".DATA_BASE_NAME.";host=127.0.0.1", USER_NAME, USER_PASSWORD);
  $DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$DB->exec("SET NAMES utf8");

  function RusToEng($string, $gost = false)
  {
    if($gost) {
      $replace = array("А"=>"A","а"=>"a","Б"=>"B","б"=>"b","В"=>"V","в"=>"v","Г"=>"G","г"=>"g","Д"=>"D","д"=>"d",
                  "Е"=>"E","е"=>"e","Ё"=>"E","ё"=>"e","Ж"=>"Zh","ж"=>"zh","З"=>"Z","з"=>"z","И"=>"I","и"=>"i",
                  "Й"=>"I","й"=>"i","К"=>"K","к"=>"k","Л"=>"L","л"=>"l","М"=>"M","м"=>"m","Н"=>"N","н"=>"n","О"=>"O","о"=>"o",
                  "П"=>"P","п"=>"p","Р"=>"R","р"=>"r","С"=>"S","с"=>"s","Т"=>"T","т"=>"t","У"=>"U","у"=>"u","Ф"=>"F","ф"=>"f",
                  "Х"=>"Kh","х"=>"kh","Ц"=>"Tc","ц"=>"tc","Ч"=>"Ch","ч"=>"ch","Ш"=>"Sh","ш"=>"sh","Щ"=>"Shch","щ"=>"shch",
                  "Ы"=>"Y","ы"=>"y","Э"=>"E","э"=>"e","Ю"=>"Iu","ю"=>"iu","Я"=>"Ia","я"=>"ia","ъ"=>"","ь"=>"");
    }
    else {
      $arStrES = array("ае","уе","ое","ые","ие","эе","яе","юе","ёе","ее","ье","ъе","ый","ий");
      $arStrOS = array("аё","уё","оё","ыё","иё","эё","яё","юё","ёё","её","ьё","ъё","ый","ий");
      $arStrRS = array("а$","у$","о$","ы$","и$","э$","я$","ю$","ё$","е$","ь$","ъ$","@","@");

      $replace = array("А"=>"A","а"=>"a","Б"=>"B","б"=>"b","В"=>"V","в"=>"v","Г"=>"G","г"=>"g","Д"=>"D","д"=>"d",
                  "Е"=>"Ye","е"=>"e","Ё"=>"Ye","ё"=>"e","Ж"=>"Zh","ж"=>"zh","З"=>"Z","з"=>"z","И"=>"I","и"=>"i",
                  "Й"=>"Y","й"=>"y","К"=>"K","к"=>"k","Л"=>"L","л"=>"l","М"=>"M","м"=>"m","Н"=>"N","н"=>"n",
                  "О"=>"O","о"=>"o","П"=>"P","п"=>"p","Р"=>"R","р"=>"r","С"=>"S","с"=>"s","Т"=>"T","т"=>"t",
                  "У"=>"U","у"=>"u","Ф"=>"F","ф"=>"f","Х"=>"Kh","х"=>"kh","Ц"=>"Ts","ц"=>"ts","Ч"=>"Ch","ч"=>"ch",
                  "Ш"=>"Sh","ш"=>"sh","Щ"=>"Shch","щ"=>"shch","Ъ"=>"","ъ"=>"","Ы"=>"Y","ы"=>"y","Ь"=>"","ь"=>"",
                  "Э"=>"E","э"=>"e","Ю"=>"Yu","ю"=>"yu","Я"=>"Ya","я"=>"ya","@"=>"y","$"=>"ye");

      $string = str_replace($arStrES, $arStrRS, $string);
      $string = str_replace($arStrOS, $arStrRS, $string);
    }

    return iconv("UTF-8","UTF-8//IGNORE",strtr($string,$replace));
  }

  $UM = new UserManager($DB);
  $GM = new GroupManager($DB);
  $SM = new SubjectManager($DB);
  $SPM = new SpecialtyManager($DB);

	session_start();
?>
