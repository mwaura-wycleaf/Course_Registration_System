<?php
session_start();

if(!isset($_SESSION["Student_ID"])){
    header("Location: login.php");
    exit();
}

require_once "config/db.php";

$studentID=$_SESSION["Student_ID"];

/* ==========================
   GET STUDENT DETAILS
========================== */

$studentSQL="SELECT s.Student_ID,
                    s.First_Name,
                    s.Email,
                    s.Department_ID,
                    d.Department_Name,
                    d.Faculty
             FROM student s
             INNER JOIN department d
             ON s.Department_ID=d.Department_ID
             WHERE s.Student_ID=?";

$stmt=mysqli_prepare($conn,$studentSQL);
mysqli_stmt_bind_param($stmt,"s",$studentID);
mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);
$student=mysqli_fetch_assoc($result);

/* ==========================
   REGISTERED COURSES
========================== */

$registeredSQL="SELECT
                c.Course_Code,
                c.Course_Name,
                r.Semester,
                r.Academic_Year,
                r.Registration_Date
                FROM registration r
                INNER JOIN course c
                ON r.Course_ID=c.Course_ID
                WHERE r.Student_ID=?
                ORDER BY c.Course_Code";

$stmt=mysqli_prepare($conn,$registeredSQL);
mysqli_stmt_bind_param($stmt,"s",$studentID);
mysqli_stmt_execute($stmt);

$registeredCourses=mysqli_stmt_get_result($stmt);

$registeredCount=mysqli_num_rows($registeredCourses);

/* ==========================
   AVAILABLE COURSES
========================== */

$availableSQL="SELECT *
               FROM course
               WHERE Department_ID=?
               AND Course_ID NOT IN
               (
                    SELECT Course_ID
                    FROM registration
                    WHERE Student_ID=?
               )
               ORDER BY Course_Code";

$stmt=mysqli_prepare($conn,$availableSQL);

mysqli_stmt_bind_param(
$stmt,
"ss",
$student["Department_ID"],
$studentID
);

mysqli_stmt_execute($stmt);

$availableCourses=mysqli_stmt_get_result($stmt);

$availableCount=mysqli_num_rows($availableCourses);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Courses</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial,Helvetica,sans-serif;
}
body{
background:#eef2f7;
color:#333;
}
.header{
position:fixed;
top:0;
left:0;
width:100%;
height:70px;
background:#2563eb;
display:flex;
justify-content:space-between;
align-items:center;
padding:0 30px;
color:#fff;
box-shadow:0 3px 10px rgba(0,0,0,.15);
z-index:1000;
}
.header h2{
display:flex;
align-items:center;
gap:10px;
}
.student-name{
font-weight:bold;
font-size:16px;
}
.sidebar{
position:fixed;
top:70px;
left:0;
width:250px;
height:calc(100vh - 70px);
background:#0f172a;
overflow-y:auto;
}
.sidebar ul{
list-style:none;
padding:20px 0;
}
.sidebar li{
margin:8px 15px;
}
.sidebar a{
display:flex;
align-items:center;
gap:15px;
padding:15px;
color:#fff;
text-decoration:none;
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
margin-bottom:30px;
}
.page-title h1{
font-size:30px;
color:#0f172a;
margin-bottom:8px;
}
.page-title p{
color:#666;
}
.summary{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
margin-bottom:30px;
}
.summary-card{
background:#fff;
padding:25px;
border-radius:15px;
box-shadow:0 8px 20px rgba(0,0,0,.08);
}
.summary-card h3{
color:#666;
margin-bottom:10px;
font-size:15px;
}
.summary-card h1{
font-size:36px;
color:#2563eb;
}
.card{
background:#fff;
padding:30px;
border-radius:18px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
margin-bottom:30px;
}
.card-title{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
}
.badge{
background:#2563eb;
color:#fff;
padding:8px 18px;
border-radius:30px;
font-size:14px;
font-weight:bold;
}
table{
width:100%;
border-collapse:collapse;
}
th{
background:#2563eb;
color:#fff;
padding:15px;
text-align:left;
}
td{
padding:15px;
border-bottom:1px solid #ddd;
}
tbody tr:nth-child(even){
background:#f8fafc;
}
tbody tr:hover{
background:#edf4ff;
}
.registered{
color:#16a34a;
font-weight:bold;
}
.available{
color:#2563eb;
font-weight:bold;
}
.empty{
text-align:center;
padding:30px;
color:#777;
font-style:italic;
}
.footer{
margin-top:20px;
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

<div class="student-name">
<?php echo htmlspecialchars($student["First_Name"]); ?>
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
<a href="register_course.php">
<i class="fa-solid fa-book-open"></i>
<span>Register Course</span>
</a>
</li>

<li>
<a href="view_courses.php" class="active">
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

<h1>My Courses</h1>

<p>

Student:
<strong><?php echo htmlspecialchars($student["First_Name"]); ?></strong>

&nbsp;&nbsp;|&nbsp;&nbsp;

Department:
<strong><?php echo htmlspecialchars($student["Department_Name"]); ?></strong>

</p>

</div>

<div class="summary">

<div class="summary-card">

<h3>Registered Courses</h3>

<h1><?php echo $registeredCount; ?></h1>

</div>

<div class="summary-card">

<h3>Available Courses</h3>

<h1><?php echo $availableCount; ?></h1>

</div>

</div>

<div class="card">

<div class="card-title">

<h2>Registered Courses</h2>

<span class="badge">

<?php echo $registeredCount; ?> Registered

</span>

</div>

<table>

<thead>

<tr>

<th>Course Code</th>
<th>Course Name</th>
<th>Semester</th>
<th>Academic Year</th>
<th>Registration Date</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php

if($registeredCount>0){

while($course=mysqli_fetch_assoc($registeredCourses)){

?>

<tr>

<td><?php echo htmlspecialchars($course["Course_Code"]); ?></td>

<td><?php echo htmlspecialchars($course["Course_Name"]); ?></td>

<td><?php echo htmlspecialchars($course["Semester"]); ?></td>

<td><?php echo htmlspecialchars($course["Academic_Year"]); ?></td>

<td><?php echo htmlspecialchars($course["Registration_Date"]); ?></td>

<td>

<span class="registered">

<i class="fa-solid fa-circle-check"></i>

Registered

</span>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="6" class="empty">

You have not registered any courses yet.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<div class="card">

<div class="card-title">

<h2>Available Courses</h2>

<span class="badge">

<?php echo $availableCount; ?> Available

</span>

</div>

<table>

<thead>

<tr>

<th>Course Code</th>
<th>Course Name</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php

if($availableCount>0){

while($course=mysqli_fetch_assoc($availableCourses)){

?>

<tr>

<td>

<?php echo htmlspecialchars($course["Course_Code"]); ?>

</td>

<td>

<?php echo htmlspecialchars($course["Course_Name"]); ?>

</td>

<td>

<span class="available">

<i class="fa-solid fa-book-open"></i>

Available

</span>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="3" class="empty">

No available courses found.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<div class="footer">

<p>

&copy; <?php echo date("Y"); ?> Utalii University | Student Course Registration System

</p>

</div>

</div>

</body>

</html>