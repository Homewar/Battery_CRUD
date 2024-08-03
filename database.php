<?php 

function db_connection()
{
    $host = '127.0.0.1';
    $username = 'root';
    $password = '';
    $databaseName= 'TimTech';

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = mysqli_connect($host,$username,$password,$databaseName);

    return $db;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_type'] == 'register_form') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $rPassword = $_POST['rPassword'] ?? '';

    if (empty($email) || empty($password) || empty($rPassword)) {
        echo "Email, password, or repeat password cannot be empty.";
        exit;
    }

    if ($password != $rPassword) {
        echo "Passwords do not match.";
        exit;
    }

    $hash_password = password_hash($password, PASSWORD_DEFAULT);
    $database = db_connection();
    $sql = "INSERT INTO `users` (`login`, `salt_password`) VALUES (?, ?)";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("ss", $email, $hash_password);
    $stmt->execute();

    $stmt->close();
    $database->close();

    header("Location:http://localhost/Battery_CRUD/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_type'] == 'login_form') {
    session_start();
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $database = db_connection();
    $sql = "SELECT `id`,`salt_password` FROM `users` WHERE `login` = ?";
    $stmt = $database->prepare($sql);

    if ($stmt === false) {
        die('Prepare failed: ' . $database->error);
    }

    $stmt->bind_param("s", $email);

    if ($stmt->execute() === false) {
        die('Execute failed: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        die('No user found with this login.');
    }

    $row = $result->fetch_assoc();
    $storedHash = $row['salt_password'];
    $userId = $row['id'];

    $stmt->close();
    $database->close();

    if (password_verify($password, $storedHash)) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
        header("location:http://localhost/Battery_CRUD/battery_table.php");
        exit;
    } else {
        echo "Invalid password.";
    }
}

if($_SERVER['REQUEST_METHOD']== 'POST' && $_POST['form_type'] == 'edit_form' )
{
    
    $conn = db_connection();
    $name = $_POST['name'];
    $voltage = $_POST['voltage'];
    $amperage = $_POST['amperage'];
    $produced = $_POST['produced']; // формат YYYY-MM-DD
    $all_capacity = $_POST['all_capacity'];
    $BMS = $_POST['BMS'];
    $product_id = $_POST['id'];

    $sql = "UPDATE `product` SET `name`=?,`voltage`=?,`amperage`=?,`produced`=?,`all_capacity`=?,`BMS`=? WHERE `id` = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }
    
    $stmt->bind_param("ssssssi", $name, $voltage, $amperage, $produced, $all_capacity, $BMS, $product_id);

    if ($stmt->execute()) {
        header("location:http://localhost/Battery_CRUD/battery_table.php");
    } else {
        echo "Ошибка обновления записи: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

if($_SERVER['REQUEST_METHOD']== 'GET' && $_GET['form_type'] == 'delete_form' )
{
    $conn = db_connection();

    // Используйте обратные кавычки для названия таблицы и полей
    $sql = "DELETE FROM `product` WHERE `id` = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }
    
    $id = $_GET['id'];
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "Запись успешно удалена";
    } else {
        echo "Ошибка при удалении записи: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
    
    // Перенос вызова header до любого вывода
    header("Location: http://localhost/Battery_CRUD/battery_table.php");
    exit();
}

?>
