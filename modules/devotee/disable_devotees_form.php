<?php
session_start();

require_once 'classes/Devotee.php';
$clist = Centres::getList();
$proglist = Programs::getList();

echo '<form id="frmExcelUpload2" enctype="multipart/form-data" action="modules/devotee/disable_devotees.php" method="POST">';

echo "<p>";
echo '<input type="hidden" name="MAX_FILE_SIZE" value="5242880" />';
echo "</p>";
echo "<p>";
echo '<input name="file" id="file" type="file" /><br />';
echo "</p>";
echo "<p>";
echo '<input type="submit" value="Upload File" />';
echo "</p>";
echo '</form>';


	
