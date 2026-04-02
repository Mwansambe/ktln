<?php 

$StudentName = "Jacob Godwin";
$Institution = "Mbeya university of and technology";
$ExamCount = 5;

echo "<h1> Welcome $StudentName</h1>";
echo "<p>Institution: $Institution</p>";
echo "<p>You have taken $ExamCount exams so far</p>";


$value = "2023";
echo gettype($value);
echo "<br>";
var_dump($value);


//explicity type converion
$year = (int) "2023";
echo "<br>";
echo gettype($year);
$filesize = (float) "2.5";
echo "<br>";
echo gettype($filesize);
?>