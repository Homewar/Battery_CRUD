<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'database.php';

$conn = db_connection();
$sql = "SELECT `id`,`name`,`voltage`,`amperage`,`watt`,`produced`,`all_capacity`,`BMS` FROM `product`";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        .container_2
        {
        display: flex;
        justify-content: center;
        margin-top: 50px;
        width: 45.5%;
        }
        .container {
        display: flex;
        justify-content: center;
        margin-top: 50px;
        }
        table 
        {
        width: 80%;
        }
        .table thead {
        background-color: #4CAF50;
        color: white; 
        }
    </style>
</head>
<body>
        <div class="container">
            <table class="table table-striped-columns table-hover table-bordered">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">name</th>
                        <th scope="col">voltage</th>
                        <th scope="col">amperage</th>
                        <th scope="col">watt</th>
                        <th scope="col">produced</th>
                        <th scope="col">all_capacity</th>
                        <th scope="col">BMS</th>
                        <th scope="col">actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($result->num_rows > 0)
                    {
                        while($row = $result->fetch_assoc())
                        {
                        echo "<tr>";
                        echo "<td>". htmlspecialchars($row['id'])."</td>";
                        echo "<td>". htmlspecialchars($row['name'])."</td>";
                        echo "<td>". htmlspecialchars($row['voltage'])."</td>";
                        echo "<td>". htmlspecialchars($row['amperage'])."</td>";
                        echo "<td>". htmlspecialchars($row['watt'])."</td>";
                        echo "<td>". htmlspecialchars($row['produced'])."</td>";
                        echo "<td>". htmlspecialchars($row['all_capacity'])."</td>";
                        echo "<td>". htmlspecialchars($row['BMS'])."</td>";
                        echo "<td class='action-buttons'>
                        <form action='edit.php' method='GET' style='display:inline-block;'>
                            <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                            <button type='submit' class='btn btn-warning'>Edit</button>
                        </form>
                        <form action='database.php' method='GET' style='display:inline-block;'>
                            <input type='hidden' value='delete_form' name='form_type'>
                            <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                            <button type='submit' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</button>
                        </form>
                        </td>";
                        echo "</tr>";         
                        }
                    }
                    ?>                   
                </tbody>
            </table>
            <br>
            <br>
        </div>
        <div class="container_2">
        <a href="add.php" class="btn btn-success">Добавить</a>
        </div>
    <!-- Подключение Bootstrap JS и зависимости -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>
