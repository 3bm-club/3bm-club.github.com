<?php
	// Данный код написан исключительно в ознакомаительных целях.
	// Если Вы пишите в подобном стиле знайте,
	// что ВСЁ ОЧЕНЬ ПЛОХО
	header("Content-Type: text/html; charset=utf-8"); // Работа с заголовком
	$link = mysql_connect("localhost", "student", "student"); // Открыть соединение с MySQL
	mysql_set_charset('utf8'); // Выбор кодировки
	$db_selected = mysql_select_db("chat", $link); // Выбор базы данных

	if(isset($_POST['update'])) // Если существует "update", который был отправлен методом POST
	{
		$lastid = (int)$_POST['lastid']; // Получить "lastid", который был отправлен методом POST. Привести к целому типу
		if($lastid < 0)
		{
			$sql_res = mysql_query("SELECT id FROM messages ORDER BY id DESC LIMIT 1"); // Выбор идентификатора одного последнего сообщения из таблицы "messages"
			if($lastid_arr = mysql_fetch_assoc($sql_res)) // Получить ассоциативный массив одной строки результата запроса
			{
				$lastid = (int)$lastid_arr["id"];
			}
			else // Ни одной строки нет в таблице "messages"
			{
				$lastid = 0;
			}
		}
		$sql_res = mysql_query("SELECT id, username, message FROM messages WHERE id > $lastid ORDER BY id ASC", $link); // Выбор всех полей таблицы "messages", у которых номер больше чем тот, на котором остановился клиент
		$res_str = "";
		while($res_arr = mysql_fetch_assoc($sql_res)) // Получать в цикле ассоциативный массив строк результата запроса
		{
			if($res_str)
			{
				$res_str .= ","; // Для корректного форматирования
			}
			else
			{
				$lastid = $res_arr['id']; // Выбрать новый "lastid"
			}
			$res_str .= " { \"username\" : \"${res_arr[username]}\", \"message\" : \"${res_arr[message]}\" } "; // Формирование массива результата в формате JSON
		}
		echo "{ \"messages\" : [ " , $res_str , " ], \"lastid\" : \"$lastid\" }"; // Формирование конечного JSON, который получит клиент
	}

	if(isset($_POST['insert'])) // Если существует "insert", который был отправлен методом POST
	{
		$username = trim($_POST['username']); // Получить "username", который был отправлен методом POST. Удалить ненужные пробены в начале и в конце
		$message = $_POST['message']; // Получить "message", который был отправлен методом POST

		if($username && $message) // Если в переменных лежат непусные строки
		{
			mysql_query("INSERT INTO messages (username, message) VALUES (\"$username\", \"$message\")"); // Добавить информацию в базу данных
		}
	}

	mysql_close($link); // Закрыть соединение с MySQL
?>
