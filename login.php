<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 80vh;
            background-color: #f8f9fa;
        }
        .form-signin {
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
    <main class="form-signin">
        <form id="myForm" action="database.php" method="post">
            <input type="hidden" value="login_form" name="form_type">
            <h1 class="h3 mb-3 fw-normal text-center">Please sign in</h1>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com">
                <span id="emailError" class="error">This field cannot be empty</span>
            </div>
            <div class="form-floating mb-3" style="margin-top: 10;">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                <span id="passwordError" class="error">This field cannot be empty</span>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
            <button style="margin-top: 10px;" class="w-100 btn btn-lg btn-outline-info" type="button" onclick="redirectToRegister()">Register</button>
        </form>
    </main>
    <!-- Подключение Bootstrap JS и зависимости -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script> 
        function redirectToRegister() {
            window.location.href = 'register.php';
        }
    </script>
    <script>
        document.getElementById('myForm').addEventListener('submit', function(event) {
            let isValid = true;

            // Check the name field
            const nameField = document.getElementById('email');
            const nameError = document.getElementById('emailError');
            if (nameField.value.trim() === '') {
                nameError.style.display = 'inline';
                isValid = false;
            } else {
                nameError.style.display = 'none';
            }

            // Check the email field
            const emailField = document.getElementById('password');
            const emailError = document.getElementById('passwordError');
            if (emailField.value.trim() === '') {
                emailError.style.display = 'inline';
                isValid = false;
            } else {
                emailError.style.display = 'none';
            }

            // If not valid, prevent form submission
            if (!isValid) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
