<?php
session_start();

require_once 'classes/Devotee.php';
$clist = Centres::getList();
$proglist = Programs::getList();

echo '<form id="frmExcelUpload2" enctype="multipart/form-data" action="modules/devotee/import_devotees.php" method="POST">';
echo "<p>";
echo "Allow Devotees With Same Contact: <SELECT name='IgnoreSameContact' style='width:200px;'>";
echo "<option value=0>No</option>";
echo "<option value=1>Yes</option>";
echo "</SELECT> ";

echo "</p>";
echo "<p>";
echo "Import Type: <SELECT name='ImportType' style='width:200px;'>";
echo "<option value=Attendence>Attendence</option>";
echo "<option value=Merge>Merge Devotees</option>";
echo "</SELECT>";

echo "</p>";

echo "<p>";
echo "<SELECT id=\"Centre\" name='Centre' style='width:200px;'>";
foreach( array_keys($clist) as $cname){
	echo "<option value=" . $clist[$cname]  . ">"  .  $cname  .  "</option>";
}
echo "</SELECT>";
echo "</p>";

echo "<p>";
echo "<SELECT id=\"Program\" name='Program' style='width:200px;'>";
foreach( array_keys($proglist) as $program){
	echo "<option value=" . $proglist[$program]  . ">"  .  $program  .  "</option>";
}
echo "</SELECT>";
echo "</p>";

echo "<p>";
echo '<input type="hidden" name="MAX_FILE_SIZE" value="5242880" />';
#echo '<input type="hidden" name="ImportType" value="Attendence" />';
echo "</p>";
echo "<p>";
echo '<input name="file" id="file" type="file" /><br />';
echo "</p>";
echo "<p>";
echo '<input type="submit" value="Upload File" />';
echo "</p>";
echo '</form>';


	
