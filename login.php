<?php
session_start();
require_once "config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {

        $error = "Please fill in all fields.";

    } else {

        $sql = "SELECT * FROM student WHERE Email = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $email);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $student = mysqli_fetch_assoc($result);

            if ($password == $student["Password"]) {

                $_SESSION["Student_ID"] = $student["Student_ID"];
                $_SESSION["First_Name"] = $student["First_Name"];

                header("Location: dashboard.php");
                exit();

            } else {

                $error = "Incorrect password.";

            }

        } else {

            $error = "Email not found.";

        }

    }

}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Student Course Registration System</title>
    <style>

        .error{
        background:#ffdddd;
        color:#b30000;
        padding:12px;
        margin-bottom:20px;
        border-radius:8px;
        text-align:center;
        font-weight:bold;
       }

    </style>

</head>

<body>

    <div class="container">

        <div class="login-box">

            <h1>Student Course Registration System</h1>

            <p class="subtitle">
            <i class="fa fa-graduation-cap" aria-hidden="true"></i> Utalii University
            </p>
            
            <?php
                if (!empty($error)) {
                echo "<div class='error'>$error</div>";
                }  
            ?>

            <form action="" method="POST">

                <div class="input-box">

                    <label>Email Address</label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        required>

                </div>

                <div class="input-box">

                    <label>Password</label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required>

                        <span id="togglePassword">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </span>

                    </div>

                </div>

                <button type="submit">

                    Login

                </button>

            </form>

        </div>

    </div>

    <script>
        const togglePassword = document.getElementById("togglePassword");
        const password = document.getElementById("password");

        togglePassword.addEventListener("click", function(){

            if(password.type === "password"){

                password.type = "text";
                this.classList.replace("fa-eye","fa-eye-slash");

            }else{

                password.type = "password";
                this.classList.replace("fa-eye-slash","fa-eye");

            }

        });
    </script>

</body>


</html>