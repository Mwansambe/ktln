<?php 
echo "<h1>Exam record paper</h1>";
$title = "MidTerm";
$year = 2024;
$subject = "Bible knowelege";
$ExamBoard = "NECTA";
$fileSize = 2.4;
$downloadCount = 2;
$isPublished = false;
$markingScheme = null;
echo "<br>";
echo "_____________________________";
echo "<br>";
echo "Exam title: $title";
echo "<br>";
echo "Year:       $year";
echo "<br>";
echo "Exam board $ExamBoard";
echo "<br>";
echo "filesize $filesize";
echo "<br>";
echo "downloadCount $downloadCount";
echo "<hr>";
echo "<br>";
echo "isPublished $isPublishied";
echo "<br>";
echo "has Marking schema $markingScheme";
echo "<hr>";
var_dump($markingScheme)



?>