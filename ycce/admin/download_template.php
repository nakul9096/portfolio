<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="YCCE_Bulk_Template.csv"');

$output = fopen('php://output', 'w');

$branches = "Computer Science & Design, Information Technology, Computer Technology, Computer Science & Engineering, Electronics Engineering (VLSI Design and Technology), Computer Science and Engineering (IoT), Artificial Intelligence and Data Science, Computer Science and Engineering (AIML), Electronics Engineering, Mechanical Engineering, Civil Engineering, Electronics and Telecommunication Engg, Electrical Engg (Electronics and Power)";

fputcsv($output, ['Full Name', 'Email', 'Registration No', 'Department', 'Role']);
fclose($output);
exit;