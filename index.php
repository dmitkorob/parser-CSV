<?
// > Нужно написать парсер csv файлов с данными (в качестве разделителя любой удобный символ):

// 1. папка /import/ с вложенными подпапкам /YYYY/MM/DD в конечной папке несколько csv файлов
// 2. поля в файлах такие "рег. номер, наименование, url, телефон, email"
// 3. необходимо собрать все данные из файлов в mysql таблицу
// 4. повторная обработка файла в будущем не допускается
// 5. название конечных полей на усмотрение исполнителя

// все события нужно логировать в отдельную таблицу

function logs ($string) {
	$date = date('Y.m.d H:m:s');
	$log = '========================================================'.PHP_EOL;
	$log .= $date."     ";
	$log .= $string.PHP_EOL;
	$open_log = fopen('log.txt', 'a+');
	fwrite($open_log, $log);
	fclose($open_log);
}

$host = 'localhost';
$login = 'register';
$password = 'C1bwo8SOHFZoH56T';
$db_name = 'parser';

$file = "import/YYYY/MM/DD";
// $explode = explode('/', $file);
// foreach($explode as $val) {
// 	$way .= $val.'/';
// 	mkdir($way);
// }


logs('Начало сканирования');
$scandir = scandir($file);
$scandir = array_diff($scandir, ['.', '..']);
sort($scandir);
if(!empty($scandir)) {
	$conn = mysqli_connect($host, $login, $password);
	if(!empty($conn)) {
		logs('Подключение к PhpMyAdmin');
	}
	$create_db = mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $db_name");
	mysqli_close($conn);
	if(!empty($create_bd)) {
		logs('Coздана база данных '.$db_name);
	}
	$connect = mysqli_connect($host, $login, $password, $db_name);
	foreach($scandir as $scanfile) {
		if(substr($scanfile, -4) == '.csv'){
			$create = mysqli_query($connect, "CREATE TABLE `{$scanfile}` ( 
				ID int PRIMARY KEY AUTO_INCREMENT, 
				REG_NUMB varchar(256) NOT NULL, 
				NAME varchar(256) NOT NULL, 
				URL varchar(256) NOT NULL, 
				TEL_NUMB varchar(256) NOT NULL, 
				EMAIL varchar(256) NOT NULL 
			)");
			if(!empty($create)) {
				logs('Coздана таблица '.$scanfile);
			}
			$fopen = fopen($file.'/'.$scanfile, 'r');
			$count = 0;
			while($str = fgets($fopen)) {
				$col = explode(',', $str);
				if(!empty($create)) {
					$insert = mysqli_query($connect, "INSERT INTO `{$scanfile}` VALUES(
						NULL,
						'$col[0]',
						'$col[1]',
						'$col[2]', 
						'$col[3]',
						'$col[4]'
					)");	
				}
				if(!empty($insert)) {
					$count++;
					logs('Добавлена запись №'.$count);
				}
			}
			fclose($fopen);
		}
	}
}
logs('Завершение сканирования');

?>