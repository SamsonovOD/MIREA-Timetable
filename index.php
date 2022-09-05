<!-- Этот файл, файл connect.php, а таже картинки должны располагаться в папке "C:\xampp\htdocs\site" для работы через XAMPP Control Panel. Доступ осуществляется через "http://localhost/site/". Работает только при наличии базы данных "schedule.pairs". -->
<!DOCTYPE html>
<?php
	include('connect.php'); //Подкулючаемя к Apache серверу и MySQL базе
?>
<html>
	<head>
		<title>РАСПИСАНИЕ</title>
	</head>
	<body background="bkg.png">
	<?php
		//Объявление строчных переменных
		$search_string_v = $search_type_v = $msg = $sql = "";
		$new_time = $new_week = $new_comment = $new_subject = $new_lecture = $new_teacher = $new_hall = $new_group = "";
		$result=$conn->query("SELECT * FROM pairs");
		//Отчистка входящих запросов от специальных символов,в целях безопасности
		function test_input($data) {
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			return $data;
		}
		//Обработка входящих комманд
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			if (isset($_POST['act'])) {
				if ($_POST['act'] == 'showall') { //Кнопка "Показать всё"
					$msg = "Show All";
					$sql = "SELECT * FROM pairs";
					$result=$conn->query($sql);
				}
				else if ($_POST['act'] == 'search') { //Кнопка поиска
					if (isset($_POST['search_string'])){
						$msg = "Search";
						$search_type_v = test_input($_POST['search_type']);
						$search_string_v = test_input($_POST['search_string']);
						$sql = "SELECT * FROM pairs WHERE $search_type_v LIKE '%$search_string_v%'";
						$result=$conn->query($sql);
					} else {					
						$msg = "Empty Search!";
					}
				}
				else if ($_POST['act'] == 'add') { //Кнопка добавления
					if(isset($_POST['add_time'])){
						if((trim($_POST['add_time']) != "")&&(mb_strlen($_POST['add_time']) <= 5)){
							$new_time = (string)$_POST['add_time'];
						}
					}
					if(isset($_POST['add_week'])){
						if((trim($_POST['add_week']) != "")&&(mb_strlen($_POST['add_week']) <= 1)&&(intval($_POST['add_week']) < 3)&&(is_numeric($_POST['add_week']) == true)){
							$new_week = $_POST['add_week'];
						}
					}
					if(isset($_POST['add_comment'])){
						if((trim($_POST['add_comment']) != "")&&(mb_strlen($_POST['add_comment']) <= 30)){
							$new_comment = $_POST['add_comment'];
						}
					}
					if(isset($_POST['add_subject'])){
						if((trim($_POST['add_subject']) != "")&&(mb_strlen($_POST['add_subject']) <= 100)){
							$new_subject = $_POST['add_subject'];
						}
					}
					if(isset($_POST['add_lecture'])){
						if((trim($_POST['add_lecture']) != "")&&(mb_strlen($_POST['add_lecture']) <= 5)){
							$new_lecture = $_POST['add_lecture'];
						}
					}
					if(isset($_POST['add_teacher'])){
						if((trim($_POST['add_teacher']) != "")&&(mb_strlen($_POST['add_teacher']) <= 50)){
							$new_teacher = $_POST['add_teacher'];
						}
					}
					if(isset($_POST['add_hall'])){
						if((trim($_POST['add_hall']) != "")&&(mb_strlen($_POST['add_hall']) <= 10)){
							$new_hall = $_POST['add_hall'];
						}
					}
					if(isset($_POST['add_group'])){
						if((trim($_POST['add_group']) != "")&&(mb_strlen($_POST['add_group']) <= 20)){
							$new_group = $_POST['add_group'];
						}
					}
					if (($new_time == "")OR($new_week == "")OR($new_subject == "")OR($new_lecture == "")OR($new_teacher == "")OR($new_hall == "")OR($new_group == "")){
						$msg = "Add ERROR";
						$sql = "INSERT INTO pairs (time, week, comment, subject, lecture, teacher, hall, class) VALUES ('$new_time', '$new_week', '$new_comment', '$new_subject', '$new_lecture', '$new_teacher', '$new_hall', '$new_group')";
					} else {
						$msg = "Add New (Wait 3 seconds)";
						$sql = "INSERT INTO pairs (time, week, comment, subject, lecture, teacher, hall, class) VALUES ('$new_time', '$new_week', '$new_comment', '$new_subject', '$new_lecture', '$new_teacher', '$new_hall', '$new_group')";
						$conn->query($sql);
						echo "<meta http-equiv='refresh' content='3'>";
					}
				}
				else if ($_POST['act'] == 'delete') { //Кнопка удаления
					$msg = "Delete (Wait 3 seconds)";
					if($_POST['del_id']){
						if(trim($_POST['del_id']) != ""){
							$del_var = $_POST['del_id'];
							$sql ="DELETE FROM pairs WHERE pair_id='$del_var'";
							$conn->query($sql);
							echo "<meta http-equiv='refresh' content='3'>";
						}
					}
				}
			}
		}
		?>
		<!--Отладочная информация-->
		<b>DEBUG HERE (Hello World!)</b><br/>
		<a href="http://localhost/dashboard/">Dashboard</a>
		<form action="index.php" method="post">
			<input type="submit" value="Обновить безопасно">
		</form>
		<?php
			echo "Command: $msg";
			echo "<br/>";
			echo "SQL query: $sql";
			echo "<br/>";
				$a = microtime(true);
		?>
		<p>TODO:</p>
		<li>Вставить другие группы;</li>
		<li>Добавить редактирование строк;</li>
		<li>Военная кафедра и физкультура не включены;</li>
		<li>Сортировка по возрастанию и убыванию;</li>
		<li>Вторая таблица для преподавателей;</li>
		<li>Заменить номер недели на римский вариант;</li>
		<li>Не учел что поиск по кабинету А-1 также выводит А-17, А-123 и прочие;</li>
		<li>Нужна конвертация даты в день недели через календарь</li>
		<hr>
		<!--Сама страница-->
		<h2>Расписание МИРЭА</h1>		
		<img src="logo.png" height="150" width="150"></img>
		<form action="index.php" method="post">
			<input type="hidden" name="act" value="showall">
			<input type="submit" value="Показать всё">
		</form>		
		<form action="index.php" method="post">
			<input type="hidden" name="act" value="search">
			Поиск по 
			<input type="radio" name="search_type" value="time">паре
			<input type="radio" name="search_type" value="subject">предмету
			<input type="radio" name="search_type" value="teacher" checked="checked">преподавателю
			<input type="radio" name="search_type" value="hall">аудитории
			<input type="radio" name="search_type" value="class">группe 
			<input type="text" name="search_string"><input type="submit" value="Поиск">
		</form>
		<table border="border" style="font-size:80%; text-align:center; border:1px solid blue">
			<thead> <!--Заголовок таблицы-->
				<tr>
				<th>ID</th>
				<th>ПАРА</th>
				<th>НЕДЕЛЯ</th>
				<th>КОММЕНТАРИЙ</th>
				<th>ПРЕДМЕТ</th>
				<th>ЗАНЯТИЕ</th>
				<th>ПРЕПОДАВАТЕЛЬ</th>
				<th>АУДИТОРИЯ</th>
				<th>ГРУППА</th>
				<th>ДЕЙСТВИЕ</th>
				</tr>
			</thead>
			<tbody> <!--Содержимое таблицы-->
				<?php
				while ($row = mysqli_fetch_assoc($result)) {
echo				"<tr>";	
echo				"<td>{$row['pair_id']}</td>";
echo				"<td>{$row['time']}</td>";
echo				"<td>{$row['week']}</td>";
echo				"<td>{$row['comment']}</td>";
echo				"<td>{$row['subject']}</td>";
echo				"<td>{$row['lecture']}</td>";
echo				"<td>{$row['teacher']}</td>";
echo				"<td>{$row['hall']}</td>";
echo				"<td>{$row['class']}</td>";
					?>
					<form action="index.php" method="post">
						<input type="hidden" name="act" value="delete">
						<input type="hidden" name="del_id" value="<?php echo$row['pair_id']?>">
						<td><input type="submit" value="Удалить"></td>
					</form>
					<?php
echo				"</tr>";
				}
				$b = microtime(true);
				echo $b-$a;
				?>
			<tr>
			<!--Новая запись-->
			<form action="index.php" method="post">
				<td>N<input type="hidden" name="act" value="add"></td>
				<td><input type="text" size="1" name="add_time"></td>
				<td><input type="text" size="1" name="add_week"></td>
				<td><input type="text" size="5" name="add_comment"></td>
				<td><input type="text" size="50" name="add_subject"></td>
				<td><input type="text" size="1" name="add_lecture"></td>
				<td><input type="text" size="35" name="add_teacher"></td>
				<td><input type="text" size="5" name="add_hall"></td>
				<td><input type="text" size="10" name="add_group"></td>
				<td><input type="submit" value="Добавить"></td>
			</form>
			</tr>
			</tbody>		
		</table>
		<p>Курсовой Проект по дисциплине "Управление Данных" студента группы ИСБО-06-15 Самсонова Олега</p>
	</body>
</html>
<?php $conn->close(); ?>