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
            display: none;
        }
    </style>
</head>
<body>
    <main class="form-editing">
        <h1 class="h3 mb-3 fw-normal text-center">Battery add</h1>
        <form id="myForm" action="database.php" method="post">
            <input type="hidden" value="add_form" name="form_type">
                <input type="text" class="form-control mb-3" id="name" name="name" placeholder="name">
                <input type="number" class="form-control mb-3" id="voltage" name="voltage" placeholder="voltage">
                <input type="number" class="form-control mb-3" id="amperage" name="amperage" placeholder="amperage">
                <input type="date" class="form-control mb-3" id="produced" name="produced" placeholder="produced">
                <input type="number" class="form-control mb-3" id="all_capacity" name="all_capacity" placeholder="all_capacity">
                <input type="text" class="form-control mb-3" id="BMS" name="BMS" placeholder="BMS">
            <button class="w-100 btn btn-lg btn-primary" type="submit">Submit Add</button>
        </form>
    </main>
    <!-- Подключение Bootstrap JS и зависимости -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>
