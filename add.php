<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 80vh;
            background-color: #f8f9fa;
        }
        .form-editing {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            margin: auto;
        }
        .error {
            color: red;
            font-size: 12px;
            display: none; /* Initially hidden */
        }
    </style>
</head>
<body>
    <main class="form-editing">
        <h1 class="h3 mb-3 fw-normal text-center">Battery Edit</h1>
        <form id="myForm" action="database.php" method="post">
            <input type="hidden" value="edit_form" name="form_type">
                <input type="hidden" value="<?= htmlspecialchars($product_id) ?>" name="id">
                <input value="<?= htmlspecialchars($row['name']) ?>" type="text" class="form-control mb-3" id="name" name="name" placeholder="name">
                <input value="<?= htmlspecialchars($row['voltage']) ?>" type="number" class="form-control mb-3" id="voltage" name="voltage" placeholder="voltage">
                <input value="<?= htmlspecialchars($row['amperage']) ?>" type="number" class="form-control mb-3" id="amperage" name="amperage" placeholder="amperage">
                <input value="<?= htmlspecialchars($row['produced']) ?>" type="date" class="form-control mb-3" id="produced" name="produced" placeholder="produced">
                <input value="<?= htmlspecialchars($row['all_capacity']) ?>" type="number" class="form-control mb-3" id="all_capacity" name="all_capacity" placeholder="all_capacity">
                <input value="<?= htmlspecialchars($row['BMS']) ?>" type="text" class="form-control mb-3" id="BMS" name="BMS" placeholder="BMS">
            <button class="w-100 btn btn-lg btn-primary" type="submit">Submit Edit</button>
        </form>
    </main>
    <!-- Подключение Bootstrap JS и зависимости -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>
