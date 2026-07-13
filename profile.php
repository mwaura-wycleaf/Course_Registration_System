<?php
session_start();

if (!isset($_SESSION["Student_ID"])) {
    header("Location: login.php");
    exit();
}

require_once "config/db.php";

$studentID = $_SESSION["Student_ID"];


/* ==========================
   GET STUDENT PROFILE
========================== */

$sql = "SELECT 
            s.Student_ID,
            s.First_Name,
            s.Last_Name,
            s.Gender,
            s.Date_of_Birth,
            s.Email,
            s.Phone,
            s.Address,
            d.Department_Name,
            d.Faculty
        FROM student s
        INNER JOIN department d
        ON s.Department_ID = d.Department_ID
        WHERE s.Student_ID = ?";


$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "s", $studentID);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$student = mysqli_fetch_assoc($result);


if (!$student) {
    die("Student profile not found.");
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Profile | Utalii University</title>


<style>

/* ==========================
   GLOBAL
========================== */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: Arial, sans-serif;
}


body{

    background:#f4f7fb;
    color:#333;

}



/* ==========================
   HEADER
========================== */

.header{

    background:#003366;
    color:white;
    padding:20px 40px;

    display:flex;
    justify-content:space-between;
    align-items:center;

}


.header h1{

    font-size:22px;

}


.logout{

    text-decoration:none;
    color:white;
    background:#e74c3c;

    padding:10px 18px;
    border-radius:6px;

}



/* ==========================
   CONTAINER
========================== */


.container{

    width:90%;
    max-width:900px;

    margin:40px auto;

}



/* ==========================
   PROFILE CARD
========================== */


.profile-card{

    background:white;

    border-radius:15px;

    padding:30px;

    box-shadow:0 5px 20px rgba(0,0,0,0.1);

    animation:slideIn 0.5s ease;

}



.profile-header{

    text-align:center;

    margin-bottom:30px;

}


.profile-header img{

    width:100px;
    height:100px;

    border-radius:50%;

    background:#003366;

    padding:20px;

}



.profile-header h2{

    margin-top:15px;

    color:#003366;

}



/* ==========================
   DETAILS
========================== */


.details{

    display:grid;

    grid-template-columns:repeat(2,1fr);

    gap:20px;

}



.detail-box{

    background:#f8f9fa;

    padding:15px;

    border-radius:10px;

}


.detail-box span{

    font-weight:bold;

    color:#003366;

    display:block;

    margin-bottom:5px;

}



/* ==========================
   BUTTON
========================== */


.back-btn{

    display:inline-block;

    margin-top:30px;

    text-decoration:none;

    background:#003366;

    color:white;

    padding:12px 25px;

    border-radius:8px;

}



.back-btn:hover{

    background:#00509e;

}



/* ==========================
   FOOTER
========================== */


.footer{

    text-align:center;

    margin-top:40px;

    padding:20px;

    color:#777;

}



/* ==========================
   ANIMATION
========================== */


@keyframes slideIn{

    from{

        opacity:0;
        transform:translateY(20px);

    }


    to{

        opacity:1;
        transform:translateY(0);

    }

}



/* MOBILE */

@media(max-width:700px){

.details{

    grid-template-columns:1fr;

}


.header{

    flex-direction:column;
    gap:15px;

}


}

</style>


</head>



<body>



<header class="header">

<h1>
Utalii University
<br>
Student Portal
</h1>


<a href="logout.php" class="logout">
Logout
</a>


</header>




<div class="container">


<div class="profile-card">


<div class="profile-header">


<img src="assets/images/student.png" alt="Profile">


<h2>
<?= $student['First_Name']." ".$student['Last_Name']; ?>
</h2>


<p>
Student Profile
</p>


</div>




<div class="details">


<div class="detail-box">

<span>Student ID</span>

<?= $student['Student_ID']; ?>

</div>



<div class="detail-box">

<span>Gender</span>

<?= $student['Gender']; ?>

</div>




<div class="detail-box">

<span>Date of Birth</span>

<?= $student['Date_of_Birth']; ?>

</div>




<div class="detail-box">

<span>Email</span>

<?= $student['Email']; ?>

</div>




<div class="detail-box">

<span>Phone</span>

<?= $student['Phone']; ?>

</div>




<div class="detail-box">

<span>Address</span>

<?= $student['Address']; ?>

</div>




<div class="detail-box">

<span>Department</span>

<?= $student['Department_Name']; ?>

</div>




<div class="detail-box">

<span>Faculty</span>

<?= $student['Faculty']; ?>

</div>



</div>



<a href="dashboard.php" class="back-btn">
← Back to Dashboard
</a>



</div>


</div>




<footer class="footer">

© <?= date("Y"); ?> Utalii University. All Rights Reserved.

</footer>



<script>


// Simple page interaction

document.addEventListener("DOMContentLoaded",()=>{


const card=document.querySelector(".profile-card");


card.addEventListener("click",()=>{

card.style.transform="scale(1.01)";


setTimeout(()=>{

card.style.transform="scale(1)";

},200);


});


});


</script>



</body>

</html>