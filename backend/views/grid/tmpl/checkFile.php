<?php
$grid_file=JPATH_COMPONENT_SITE.'/layouts'.'/GridView.php';
$card_file=JPATH_COMPONENT_SITE.'/layouts'.'/CardView.php';
$grap_file=JPATH_COMPONENT_SITE.'/layouts'.'/GraphView.php';
$reco_file=JPATH_COMPONENT_SITE.'/layouts'.'/RecordView.php';

echo "<input type=\"radio\" name=\"typejx\" value=\"grid\" onchange=\"javascript: submitbutton('dbcheck')\" ";
if ($this->grid->typejx == 'grid') echo 'checked';
if(!file_exists($grid_file))echo ' disabled';
echo "/> Table JX";

echo "<input type=\"radio\" name=\"typejx\" value=\"card\" onchange=\"javascript: submitbutton('dbcheck')\" ";
if ($this->grid->typejx == 'card') echo 'checked';
if(!file_exists($card_file))echo ' disabled';
echo "/> Card JX";

echo "<input type=\"radio\" name=\"typejx\" value=\"grap\" onchange=\"javascript: submitbutton('dbcheck')\" ";
if ($this->grid->typejx == 'grap') echo 'checked';
if(!file_exists($grap_file))echo ' disabled';
echo "/> Graph JX";

echo "<input type=\"radio\" name=\"typejx\" value=\"reco\" onchange=\"javascript: submitbutton('dbcheck')\" ";
if ($this->grid->typejx == 'reco') echo 'checked';
if(!file_exists($reco_file))echo ' disabled';
echo "/> Record JX";
?>