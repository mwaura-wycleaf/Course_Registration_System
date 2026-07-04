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


       *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, Helvetica, sans-serif;
        }

        body{

            display:flex;
            justify-content:center;
            align-items:center;
            min-height:100vh;
            background:linear-gradient(135deg,#0f172a,#1e3a8a,#3b82f6);

        }

        .container{

            width:100%;
            display:flex;
            justify-content:center;
            align-items:center;

        }

        .login-box{

            width:420px;

            background:rgba(255,255,255,.15);

            backdrop-filter:blur(18px);

            border-radius:20px;

            padding:40px;

            box-shadow:0 10px 35px rgba(0,0,0,.3);

            color:white;

        }

        .login-box h1{

            text-align:center;

            margin-bottom:10px;

            font-size:30px;

        }

        .subtitle{

            text-align:center;

            margin-bottom:35px;

            color:#ddd;

        }

        .input-box{

            margin-bottom:22px;

        }

        .input-box label{

            display:block;

            margin-bottom:8px;

            font-weight:bold;

        }

        .input-box input{

            width:100%;

            padding:14px;

            border:none;

            outline:none;

            border-radius:10px;

            font-size:16px;

        }

        .password-wrapper{

            position:relative;

        }

        .password-wrapper input{

            padding-right:50px;

        }

        .password-wrapper i{

            position:absolute;

            right:18px;

            top:50%;

            transform:translateY(-50%);

            cursor:pointer;

            color:#333;

        }

        button{

            width:100%;

            padding:14px;

            border:none;

            border-radius:10px;

            font-size:18px;

            cursor:pointer;

            background:#2563eb;

            color:white;

            transition:.3s;

        }

        button:hover{

            background:#1d4ed8;

        }

        .error{

            background:#ffdddd;

            color:#b30000;

            padding:12px;

            border-radius:10px;

            margin-bottom:20px;

            text-align:center;

        }

        @media(max-width:500px){

        .login-box{

            width:90%;
            padding:30px;

        }

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
        const togglePassword = document.querySelector("#togglePassword i");
        const password = document.getElementById("password");

        togglePassword.addEventListener("click", function(){

            if(password.type === "password"){

                password.type = "text";
                this.classList.add("fa-eye");
                this.classList.remove("fa-eye-slash");

            }else{

                password.type = "password";
                this.classList.remove("fa-eye-slash");
                this.classList.add("fa-eye");

            }

        });
    </script>

</body>


</html>