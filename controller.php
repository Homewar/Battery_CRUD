<?php
include_once 'actions.php';

//Подключение к базе данных
$database = new Database();
$db = $database->getConnection();

//Список столбцов на которые надо заменить id
$reference_columns = [
    'manufacturers' => 'ManufacturerName',
    'watches' => 'Model',
    'customers' => 'FirstName',
    'orders' => 'OrderID'
];

//чтение записи ----------------------------------------------------------------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['action'] == 'read') {

    $table_name  = $_GET['table'];
    $query       = $db->query("SHOW TABLES");
    $tables      = $query->fetchAll(PDO::FETCH_COLUMN);
    $actions     = new actions($db, $table_name);
    $columns     = $actions->get_column_name(); 
    $stmt        = $actions->getAll();
    $data_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $primaryKey  = $actions->getAllPrimaryKeyColumns();
    $column_id   = $actions->getPrimaryKey();
    $foreign_key_names = $actions->getForeignKeyNames($primaryKey, $reference_columns);


    function prepareDataForView($original_data, $primaryKey, $foreign_key_names) {
        $prepared_data = []; // Новый массив для подготовленных данных
    
        foreach ($original_data as $row) {
            $prepared_row = $row; // Копируем оригинальную строку данных
            foreach ($primaryKey as $key) {
                $column_name = $key['column_name'];
                $referenced_table = $key['referenced_table_name'];
                $referenced_column = $key['referenced_column_name'];
    
                if (isset($row[$column_name]) && $referenced_table !== null) {
                    $referenced_value = $row[$column_name];
                    if (isset($foreign_key_names[$column_name][$referenced_value])) {
                        $prepared_row["{$column_name}_display"] = $foreign_key_names[$column_name][$referenced_value];
                    } else {
                        $prepared_row["{$column_name}_display"] = $referenced_value;
                    }
                    $prepared_row["{$column_name}_link"] = "controller.php?action=read&table={$referenced_table}";
                }
            }
            $prepared_data[] = $prepared_row; // Добавляем обработанную строку в массив
        }
    
        return $prepared_data;
    }

    function renderTableCell($column, $row) {
        if (isset($row["{$column}_link"])) {
            $link = htmlspecialchars($row["{$column}_link"]);
            $display = htmlspecialchars($row["{$column}_display"]);
            return "<td><a href=\"{$link}\" target=\"_blank\">{$display}</a></td>";
        } elseif ($column === 'image_path' && !empty($row[$column])) {
            $image = htmlspecialchars($row[$column]);
            return "<td><img src=\"{$image}\" alt=\"Image\" style=\"max-width: 100px; max-height: 100px;\"></td>";
        } else {
            $content = htmlspecialchars($row[$column]);
            return "<td>{$content}</td>";
        }
    }

    $prepared_data = prepareDataForView($data_result, $primaryKey, $foreign_key_names);
    include 'read.php';
}

//переход на страницу изменения -----------------------------------------------------------------------------------------------------

else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['table']) && $_POST['action'] == 'edit') {
    $table_name  = $_POST['table'];
    $id          = $_POST['id'];
    $query       = $db->query("SHOW TABLES");
    $tables      = $query->fetchAll(PDO::FETCH_COLUMN);
    
    if ($table_name == null || $id == null) {
        if (empty($table_name)) {
            $error_message = "Ошибка: Не указано имя таблицы.";
            include 'edit.php';
            exit;
        } else {
            $error_message = "Ошибка: Не указан ID.";
            include 'edit.php';
            exit;
        }
    }

    $actions     = new actions($db, $table_name);
    $column_id   = $actions->getPrimaryKey();
    $columns     = $actions->get_column_info();
    $stmt        = $actions->getById($id);
    $record      = $stmt->fetch(PDO::FETCH_ASSOC);

    function getInputTypeAndValue($column, $record) {
        $input_type = 'text'; 
        if (strpos($column['Type'], 'date') !== false) {
            $input_type = 'date';
        } elseif (strpos($column['Type'], 'int') !== false || 
                  strpos($column['Type'], 'float') !== false || 
                  strpos($column['Type'], 'double') !== false) {
            $input_type = 'number';
        } elseif (strpos($column['Field'], 'image') !== false || 
                  strpos($column['Field'], 'file') !== false) {
            $input_type = 'file'; 
        }
    
        $value = isset($record[$column['Field']]) ? htmlspecialchars($record[$column['Field']]) : '';
        
        return [
            'input_type' => $input_type,
            'value' => $value
        ];
    }

    include 'edit.php';
}

//запрос изменения записи ------------------------------------------------------------------------------------------------------------------

else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] == 'update') {
    $data        = $_POST;
    $table_name  = $_POST['table_name'];
    $id          = $_POST['id'];
    $actions     = new actions($db, $table_name);
    $column_id   = $actions->getPrimaryKey();
    

    // Проверка на пустые поля
    foreach ($data as $key => $value) {
        if (empty($value) && $key != 'id' && $key != 'action' && $key != 'table_name') {
            $error_message = "Ошибка: Поле " . $key . " не может быть пустым.";
            include 'edit.php'; // Вернуться к форме редактирования и показать ошибку
            exit; // Прервать выполнение скрипта
        }
    }

    if(isset($data['Email']))
    {
        $email = $_POST['Email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Ошибка: Некорректный email.";
            include 'create.php';
            exit;
        }
    }

    foreach ($_FILES as $key => $file) {
        if ($file['error'] == UPLOAD_ERR_OK) {
            // Определяем директорию для загрузки файлов
            $uploadDir = 'uploads/';
            // Уникальное имя файла
            $fileName = basename($file['name']);
            $targetFilePath = $uploadDir . $fileName;
    
            // Перемещаем загруженный файл в нужную директорию
            if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
                // Если загрузка успешна, сохраняем путь к файлу в массив $data
                $data[$key] = $targetFilePath;
            } else {
                $error_message = "Ошибка: Не удалось загрузить файл " . $fileName;
                include 'edit.php';
                exit;
            }
        }
    }

        // Проверка внешних ключей
        $foreignKeys = $actions->getAllPrimaryKeyColumns();
        foreach ($foreignKeys as $key) {
            $foreignTable  = $key['referenced_table_name'];
            $foreignColumn = $key['referenced_column_name'];
            $value         = $data[$key['column_name']];
            if (!$actions->checkForeignKey($foreignTable, $foreignColumn, $value)) {
                $error_message = "Ошибка: Значение для " . $key['column_name'] . " не найдено в таблице " . $foreignTable;
                include 'create.php'; // Вернуться к форме создания и показать ошибку
                exit; // Прервать выполнение скрипта
            }
        }

    unset($data['table_name']); // Убираем параметр table из POST данных
    unset($data['action']);     // Убираем параметр action из POST данных
    unset($data['id']);         // Убираем параметр id из POST данных

    // Обновление записи
    if ($actions->update($id, $data)) {
        header("Location: http://localhost/Internet_Laba_1/controller.php?action=read&table=".$table_name);
    } 
    else {
        $error_message = "Ошибка: Не удалось обновить запись.";
        include 'edit.php';
    }
}

//Удаление -----------------------------------------------------------------------------------------------------------------

else if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'delete') {
    $id         = $_POST['id'];
    $table_name = $_POST['table'];

    if ($table_name == null || $id == null) {
        if (empty($table_name)) {
            $error_message = "Ошибка: Не указано имя таблицы.";
            include 'read.php';
            exit;
        } else {
            $error_message = "Ошибка: Не указан ID.";
            include 'read.php';
            exit;
        }
    }
    
    
    $actions = new actions($db, $table_name);
    $record = $actions->getById($id)->fetch(PDO::FETCH_ASSOC);
    $file_field = 'image_path';

    if (isset($record[$file_field]) && !empty($record[$file_field])) {
        $file_path = $record[$file_field];
    
        // Удаляем файл с сервера, если он существует
        if (file_exists($file_path)) {
            if (unlink($file_path)) {
                echo "Файл был успешно удален.";
            } else {
                echo "Ошибка при удалении файла: " . $file_path;
            }
        } else {
            echo "Файл не найден: " . $file_path;
        }
    }

    try {
        // Проверка на наличие ссылок на удаляемый объект
        if (!$actions->checkReferencesBeforeDelete($id)) {
            $error_message = "Ошибка: На этот объект есть ссылки в других таблицах. Удаление невозможно.";
            include 'read.php'; // Страница ошибки (либо вывести сообщение на текущей странице)
            exit; // Прекращаем выполнение скрипта
        }

        // Если нет ссылок, удаляем запись
        if ($actions->delete($id)) {
            header("Location: http://localhost/Internet_Laba_1/controller.php?action=read&table=".$table_name);
        } else {
            $error_message = "Ошибка: Не удалось удалить запись.";
            include 'read.php';
        }
    } catch (Exception $e) {
        $error_message = "Ошибка: Запись имеет связнную таблицу";
        include 'read.php'; // Страница ошибки (либо вывести сообщение на текущей странице)
        exit;
    }
}

//переход на страницу добавления ----------------------------------------------------------------------------------------------------------------------

else if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'create')
{
    $table_name = $_POST['table'];
    $id         = $_POST['id'];
    $query       = $db->query("SHOW TABLES");
    $tables      = $query->fetchAll(PDO::FETCH_COLUMN);

    if (empty($table_name) || empty($id)) {
        if (empty($table_name)) {
            $error_message = "Ошибка: Не указано имя таблицы.";
            include 'create.php';
            exit;
        } else {
            $error_message = "Ошибка: Не указан ID.";
            include 'create.php';
            exit;
        }
    }

    $actions    = new actions($db, $table_name);
    $column_id  = $actions->getPrimaryKey();
    $columns    = $actions->get_column_info();
    include 'create.php';
}

//запрос на добавление
else if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'add') 
{
    $data       = $_POST;
    $table_name = $_POST['table_name'];
    $actions    = new actions($db, $table_name);

    if(isset($data['Email']))
    {
        $email= $_POST['Email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Ошибка: Некорректный email.";
        include 'create.php';
        exit;
        }
    }

    foreach ($_FILES as $key => $file) {
        if ($file['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            $fileName = basename($file['name']);
            $targetFilePath = $uploadDir . $fileName;
    
            if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
                $data[$key] = $targetFilePath;
            } else {
                $error_message = "Ошибка: Не удалось загрузить файл " . $fileName;
                include 'create.php';
                exit;
            }
        }
    }

    foreach ($data as $key => $value) {
        if (empty($value) && $key != 'id' && $key != 'action' && $key != 'table_name') {
            $error_message = "Ошибка: Поле " . $key . " не может быть пустым.";
            include 'create.php';
            exit;
        }
    }

    // Обработка изображения, если оно загружено
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        // Задаем путь для загрузки файла
        $upload_dir = 'uploads/';
        // Генерируем уникальное имя файла
        $image_name = uniqid() . '-' . basename($_FILES['image']['name']);
        $upload_file = $upload_dir . $image_name;

        // Перемещаем загруженный файл в папку uploads
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
            // Добавляем путь к изображению в данные для базы данных
            $data['image'] = $upload_file; // предполагается, что у вас есть колонка image
        } else {
            $error_message = "Ошибка: Не удалось загрузить изображение.";
            include 'create.php';
            exit;
        }
    }

    // Проверка внешних ключей
    $foreignKeys = $actions->getAllPrimaryKeyColumns();
    foreach ($foreignKeys as $key) {
        $foreignTable  = $key['referenced_table_name'];
        $foreignColumn = $key['referenced_column_name'];
        $value         = $data[$key['column_name']];
        if (!$actions->checkForeignKey($foreignTable, $foreignColumn, $value)) {
            $error_message = "Ошибка: Значение для " . $key['column_name'] . " не найдено в таблице " . $foreignTable;
            include 'create.php'; // Вернуться к форме создания и показать ошибку
            exit; // Прервать выполнение скрипта
        }
    }

    // Удаляем ненужные поля из данных
    unset($data['table_name']);
    unset($data['action']);
    unset($data['id']);

    // Сохраняем данные в базу
    if ($actions->create($data)) 
    {
        header("Location: http://localhost/Internet_Laba_1/controller.php?action=read&table=".$table_name);
    } 
    else 
    {
        $error_message = "Ошибка: Не удалось создать запись.";
        include 'create.php';
    }
}
else 
{
    echo "Invalid request.";
}
?>
