<?
//master2
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

function scan($dir) {
	$scan = scandir($dir);
	$scan = array_diff($scan, array('.', '..'));
	sort($scan);
	foreach($scan as $val) {
		$way = $dir;
		if(is_file($way.'/'.$val)) {
			$way .='/'.$val;
			echo "<pre>";
			echo $way."<br>";
			pars($val, $way);
		} else if(is_dir($way.'/'.$val)) {
			$way .='/'.$val;
			$mass[$val] = scan($way);
		}
	}

}

function pars($file, $way) {

	$host = 'localhost';
	$login = 'register';
	$password = 'C1bwo8SOHFZoH56T';
	$db_name = 'parser';

	logs('Начало сканирования');
	$conn = mysqli_connect($host, $login, $password);
	if(!empty($conn)) {
		logs('Подключение к PhpMyAdmin');
	}
	$create_db = mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $db_name");
	mysqli_close($conn);
	if(isset($create_bd)) {
		logs('Coздана база данных '.$db_name);
	}
	$connect = mysqli_connect($host, $login, $password, $db_name);
	if(substr($file, -4) == '.csv'){
		$create = mysqli_query($connect, "CREATE TABLE `{$file}` ( 
			ID int PRIMARY KEY AUTO_INCREMENT, 
			REG_NUMB varchar(256) NOT NULL, 
			NAME varchar(256) NOT NULL, 
			URL varchar(256) NOT NULL, 
			TEL_NUMB varchar(256) NOT NULL, 
			EMAIL varchar(256) NOT NULL 
		)");
		if(isset($create)) {
			logs('Coздана таблица '.$file);
		}
		$fopen = fopen($way, 'r');
		$count = 0;
		while($str = fgets($fopen)) {
			$col = explode(',', $str);
			if(isset($create)) {
				$insert = true;
				// $insert = mysqli_query($connect, "INSERT INTO `{$file}` VALUES(
				// 	NULL,
				// 	'$col[0]',
				// 	'$col[1]',
				// 	'$col[2]', 
				// 	'$col[3]',
				// 	'$col[4]'
				// )");	
			}
			if(isset($insert)) {
				$count++;
				logs('Добавлена запись №'.$count);
			}
		}
		fclose($fopen);
	}
	mysqli_close($connect);
	logs('Завершение сканирования');
}


$dir = "import";
scan($dir);
echo $count_file;

?>