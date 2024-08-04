<?php 

function db_connection()
{
    $host = '127.0.0.1';
    $username = 'root';
    $password = '';
    $databaseName= 'TimTech';

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    return mysqli_connect($host,$username,$password,$databaseName);
}

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_type'] == 'register_form') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $rPassword = $_POST['rPassword'] ?? '';

        if (empty($email) || empty($password) || empty($rPassword)) {
            throw new Exception("Email, password, or repeat password cannot be empty.");
        }

        if ($password != $rPassword) {
            throw new Exception("Passwords do not match.");
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
            throw new Exception('Prepare failed: ' . $database->error);
        }

        $stmt->bind_param("s", $email);

        if ($stmt->execute() === false) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('No user found with this login.');
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
            throw new Exception("Invalid password.");
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
            throw new Exception("Ошибка подготовки запроса: " . $conn->error);
        }
        
        $stmt->bind_param("ssssssi", $name, $voltage, $amperage, $produced, $all_capacity, $BMS, $product_id);

        if ($stmt->execute()) {
            header("location:http://localhost/Battery_CRUD/battery_table.php");
            exit();
        } else {
            throw new Exception("Ошибка обновления записи: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();
    }

    if($_SERVER['REQUEST_METHOD']== 'GET' && $_GET['form_type'] == 'delete_form' )
    {
        $conn = db_connection();
        $sql = "DELETE FROM `product` WHERE `id` = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Ошибка подготовки запроса: " . $conn->error);
        }
        
        $id = $_GET['id'];
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            header("Location: http://localhost/Battery_CRUD/battery_table.php");
            exit();
        } else {
            throw new Exception("Ошибка при удалении записи: " . $stmt->error);
        }
        
        $stmt->close();
        $conn->close();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['form_type'] == 'add_form') {
        $conn = db_connection();
        $name = $_POST['name'];
        $voltage = $_POST['voltage'];
        $amperage = $_POST['amperage'];
        $produced = $_POST['produced']; // формат YYYY-MM-DD
        $all_capacity = $_POST['all_capacity'];
        $BMS = $_POST['BMS'];

        $sql = "INSERT INTO `product` (`name`,`voltage`,`amperage`,`produced`,`all_capacity`,`bms`) VALUE (?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $name,$voltage,$amperage,$produced,$all_capacity,$BMS);

        if ($stmt->execute()) {
            header("location:http://localhost/Battery_CRUD/battery_table.php");
            exit();
        } else {
            throw new Exception("Ошибка обновления записи: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    // Optionally log the error or take other actions
}
?>
