<?

$cd = getcwd();
chdir('out');
$d = glob('*.gif');
foreach($d as $f) unlink($f);
chdir($cd);
echo 'Deleted '.count($d).' files<br />';

$img = imagecreatefromgif("StraySheepPoe.trans.gif");

$transparent_index = ImageColorTransparent($img); /* gives the index of current transparent color or -1 */
if($transparent_index!=(-1)) $transparent_color = ImageColorsForIndex($img,$transparent_index);

if(empty($transparent_color)) {
  echo "Unable to detect transparent color<br />";
}

$x = 0;
$y = 0;

for($y=0;$y<480;$y=$y+40) {
  for($x=0;$x<640;$x=$x+40) {
    $tmpImg = imagecreate(40, 40);
    $blue = imagecolorallocate($tmpImg, 0, 0, 255);
    imagecolortransparent($tmpImg, $blue);
    imageFill($tmpImg, 0,0, $blue);
    imagecopy($tmpImg, $img, 0, 0, $x, $y, 40, 40);
    $fname = 'out/sheep_'.$x.'_'.$y.'.gif';
    $out[$y][$x] = $fname;
    imagegif($tmpImg, $fname);
    imagedestroy($tmpImg);
  }
}
imagedestroy($img);

foreach($out as $y=> $ya) {
 foreach($ya as $x => $xa) {
  echo '<img height=40 width=40 src="'.$out[$y][$x].'" />';
 }
 echo '<br />';
}



?>ok