<?php
session_start();

if (!isset($_SESSION["Student_ID"])) {
    header("Location: login.php");
    exit();
}

require_once "config/db.php";

$studentID = $_SESSION["Student_ID"];
$success = "";
$error = "";

/* ==============================
   GET STUDENT DETAILS
============================== */

$studentSQL = "SELECT s.Student_ID,
                      s.First_Name,
                      s.Email,
                      s.Department_ID,
                      d.Department_Name
               FROM student s
               INNER JOIN department d
               ON s.Department_ID = d.Department_ID
               WHERE s.Student_ID = ?";

$stmt = mysqli_prepare($conn,$studentSQL);
mysqli_stmt_bind_param($stmt,"s",$studentID);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

/* ==============================
   FETCH COURSES
============================== */

$courseSQL = "SELECT *
              FROM course
              WHERE Department_ID = ?
              ORDER BY Course_Code";

$stmt2 = mysqli_prepare($conn,$courseSQL);
mysqli_stmt_bind_param($stmt2,"s",$student["Department_ID"]);
mysqli_stmt_execute($stmt2);

$courses = mysqli_stmt_get_result($stmt2);

/* ==============================
   REGISTER COURSE
============================== */

if(isset($_POST["register"])){

    if(empty($_POST["courses"])){

        $error="Please select at least one course.";

    }else{

        $semester=$_POST["semester"];
        $academicYear=$_POST["academic_year"];
        $registrationDate=date("Y-m-d");

        foreach($_POST["courses"] as $courseID){

            /* Check duplicate */

            $checkSQL="SELECT *
                       FROM registration
                       WHERE Student_ID=?
                       AND Course_ID=?";

            $checkStmt=mysqli_prepare($conn,$checkSQL);

            mysqli_stmt_bind_param(
                $checkStmt,
                "ss",
                $studentID,
                $courseID
            );

            mysqli_stmt_execute($checkStmt);

            $checkResult=mysqli_stmt_get_result($checkStmt);

            if(mysqli_num_rows($checkResult)>0){
                continue;
            }

            /* Generate Registration ID */

            $idSQL="SELECT Registration_ID
                    FROM registration
                    ORDER BY Registration_ID DESC
                    LIMIT 1";

            $idResult=mysqli_query($conn,$idSQL);

            $lastID=mysqli_fetch_assoc($idResult);

            if($lastID){

                $number=(int)substr(
                    $lastID["Registration_ID"],
                    2
                )+1;

            }else{

                $number=1;

            }

            $registrationID="RD".$number;

            /* Insert */

            $insertSQL="INSERT INTO registration
            (
                Registration_ID,
                Student_ID,
                Course_ID,
                Semester,
                Academic_Year,
                Registration_Date
            )
            VALUES
            (
                ?,?,?,?,?,?
            )";

            $insertStmt=mysqli_prepare(
                $conn,
                $insertSQL
            );

            mysqli_stmt_bind_param(
                $insertStmt,
                "ssssss",
                $registrationID,
                $studentID,
                $courseID,
                $semester,
                $academicYear,
                $registrationDate
            );

            mysqli_stmt_execute($insertStmt);

        }

        $success="Course registration completed successfully.";

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width,initial-scale=1.0">

<title>Register Course</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial,Helvetica,sans-serif;
}

body{
background:#eef2f7;
}

.header{
height:70px;
background:#2563eb;
display:flex;
justify-content:space-between;
align-items:center;
padding:0 30px;
color:white;
position:fixed;
top:0;
left:0;
width:100%;
z-index:1000;
}

.sidebar{
position:fixed;
top:70px;
left:0;
width:250px;
height:100vh;
background:#0f172a;
padding-top:20px;
}

.sidebar ul{
list-style:none;
}

.sidebar li{
margin:8px 15px;
}

.sidebar a{
display:flex;
align-items:center;
gap:15px;
padding:15px;
text-decoration:none;
color:white;
border-radius:12px;
transition:.3s;
}

.sidebar a:hover,
.sidebar .active{
background:#2563eb;
}

.main{
margin-left:250px;
margin-top:70px;
padding:35px;
}

.page-title{
margin-bottom:25px;
}

.page-title h1{
color:#0f172a;
}

.page-title p{
color:#666;
margin-top:8px;
}

.success{
background:#d4edda;
color:#155724;
padding:15px;
border-radius:10px;
margin-bottom:20px;
}

.error{
background:#f8d7da;
color:#721c24;
padding:15px;
border-radius:10px;
margin-bottom:20px;
}

.card{
background:white;
padding:25px;
border-radius:18px;
box-shadow:0 10px 20px rgba(0,0,0,.08);
}

/* ===== PART 2 STARTS HERE ===== */
table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

table th{
background:#2563eb;
color:white;
padding:15px;
text-align:left;
}

table td{
padding:15px;
border-bottom:1px solid #ddd;
}

table tr:hover{
background:#f8fafc;
}

select{
width:100%;
padding:12px;
margin-top:10px;
margin-bottom:20px;
border:1px solid #ccc;
border-radius:8px;
outline:none;
}

button{
background:#2563eb;
color:white;
border:none;
padding:14px 25px;
border-radius:10px;
cursor:pointer;
font-size:16px;
transition:.3s;
}

button:hover{
background:#1d4ed8;
}

.footer{
margin-top:30px;
text-align:center;
color:#666;
}

@media(max-width:900px){

.sidebar{
width:80px;
}

.sidebar a span{
display:none;
}

.main{
margin-left:80px;
}

table{
font-size:14px;
}

}

</style>

</head>

<body>

<header class="header">

<h2>
<i class="fa-solid fa-graduation-cap"></i>
Utalii University
</h2>

<div>

<strong>

<?php echo htmlspecialchars($student["First_Name"]); ?>

</strong>

</div>

</header>

<aside class="sidebar">

<ul>

<li>

<a href="dashboard.php">

<i class="fa-solid fa-house"></i>

<span>Dashboard</span>

</a>

</li>

<li>

<a href="register_course.php" class="active">

<i class="fa-solid fa-book-open"></i>

<span>Register Course</span>

</a>

</li>

<li>

<a href="view_courses.php">

<i class="fa-solid fa-book"></i>

<span>My Courses</span>

</a>

</li>

<li>

<a href="drop_course.php">

<i class="fa-solid fa-trash"></i>

<span>Drop Course</span>

</a>

</li>

<li>

<a href="logout.php">

<i class="fa-solid fa-right-from-bracket"></i>

<span>Logout</span>

</a>

</li>

</ul>

</aside>

<div class="main">

<div class="page-title">

<h1>Register Courses</h1>

<p>

Department:
<strong>

<?php echo htmlspecialchars($student["Department_Name"]); ?>

</strong>

</p>

</div>

<?php

if($success!=""){

echo "<div class='success'>$success</div>";

}

if($error!=""){

echo "<div class='error'>$error</div>";

}

?>

<div class="card">

<form method="POST">

<label><strong>Semester</strong></label>

<select name="semester" required>

<option value="">Select Semester</option>

<option>Semester 1</option>

<option>Semester 2</option>

</select>

<label><strong>Academic Year</strong></label>

<select name="academic_year" required>

<option value="">Select Academic Year</option>

<option>2025/2026</option>

<option>2026/2027</option>

<option>2027/2028</option>

</select>

<table>

<tr>

<th>Select</th>

<th>Course Code</th>

<th>Course Name</th>

</tr>

<?php while($course=mysqli_fetch_assoc($courses)){ ?>

<tr>

<td>

<input
type="checkbox"
name="courses[]"
value="<?php echo $course["Course_ID"]; ?>">

</td>

<td>

<?php echo htmlspecialchars($course["Course_Code"]); ?>

</td>

<td>

<?php echo htmlspecialchars($course["Course_Name"]); ?>

</td>

</tr>

<?php } ?>

</table>

<br>

<button
type="submit"
name="register">

<i class="fa-solid fa-floppy-disk"></i>

Register Selected Courses

</button>

</form>

</div>

<div class="footer">

<p>

© 2026 Utalii University Student Course Registration System

</p>

</div>

</div>

<script>

const checks=document.querySelectorAll(
'input[type="checkbox"]'
);

checks.forEach(box=>{

box.addEventListener("change",()=>{

const total=document.querySelectorAll(
'input[type="checkbox"]:checked'
).length;

document.title=
total+" Course(s) Selected";

});

});

</script>

</body>

</html>