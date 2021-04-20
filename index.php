<?
//master
// > Нужно написать парсер csv файлов с данными (в качестве разделителя любой удобный символ):

// 1. папка /import/ с вложенными подпапкам /YYYY/MM/DD в конечной папке несколько csv файлов
// 2. поля в файлах такие "рег. номер, наименование, url, телефон, email"
// 3. необходимо собрать все данные из файлов в mysql таблицу
// 4. повторная обработка файла в будущем не допускается
// 5. название конечных полей на усмотрение исполнителя

// все события нужно логировать в отдельную таблицу

function logs ($string, $conn) {
	$date = date('Y.m.d H:m:s');
	$create_table = mysqli_query($conn, 
		"CREATE TABLE IF NOT EXISTS 'logs' (
		id_log int PRIMARY KEY AUTO_INCREMENT,
		date_log VARCHAR(256) NOT NULL,
		`text` TEXT NOT NULL
	)");
	// echo 1;
	$insert = mysqli_query($conn, "INSERT INTO 'logs' VALUES(NULL, `$date`, `$string`)");
}

function scan($dir) {
	$scan = scandir($dir);
	$scan = array_diff($scan, array('.', '..'));
	sort($scan);
	foreach($scan as $val) {
		$way = $dir;
		if(is_file($way.'/'.$val)) {
			$way .='/'.$val;
			// echo "<pre>";
			// echo $way."<br>";
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

	$conn = mysqli_connect($host, $login, $password);
	$create_db = mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $db_name");
	mysqli_close($conn);
	$connect = mysqli_connect($host, $login, $password, $db_name);
	if(!empty($connect)) {
		logs("Подключение к $db_name", $connect);
	}
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
			logs('Coздана таблица '.$file, $connect);
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
				logs('Добавлена запись №'.$count, $connect);
			}
		}
		fclose($fopen);
	}
	logs('Завершение сканирования', $connect);
	mysqli_close($connect);
}


$dir = "import";
scan($dir);

?>