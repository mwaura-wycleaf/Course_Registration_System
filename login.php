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

            if ($password == $student["password"]) {

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
            background:linear-gradient(
            135deg,
            #0f172a 0%,
            #1e40af 50%,
            #3b82f6 100%
            );

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
            padding:50px 45px;
            box-shadow:0 10px 35px rgba(0,0,0,.3);
            color:white;
            animation:fadeIn .7s ease;

        }

        @keyframes fadeIn{

            from{

            opacity:0;

            transform:translateY(30px);

            }

            to{

            opacity:1;

            transform:translateY(0);

            }

        }

        .login-box h1{
            text-align:center;
            margin-bottom:10px;
            font-size:34px;
            font-weight:700;

        }

        .subtitle{
            text-align:center;
            margin-bottom:35px;
            color:#e2e8f0;
            letter-spacing:.5px;

        }

        .input-box input:focus{

         border-color:#60a5fa;

         box-shadow:0 0 10px rgba(96,165,250,.4);
    
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

            border:2px solid transparent;
            transition:.3s;

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

           font-weight:bold;

           letter-spacing:.5px;

            color:white;

            transition:.3s;

        }

        button:hover{

            background:#1d4ed8;
            transform:translateY(-2px);

    box-shadow:0 8px 18px rgba(37,99,235,.35);

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

            <h1>Student Course Registration</h1>

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
                           <i class="fa-solid fa-eye" aria-hidden="true"></i>
                        </span>

                    </div>

                </div>

                <button type="submit">

                    Sign in

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
                this.classList.replace("fa-eye-slash", "fa-eye");
                 

            }else{

                password.type = "password";
               this.classList.replace("fa-eye", "fa-eye-slash");

            }

        });
    </script>

</body>


</html>