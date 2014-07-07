<?php	
$dir = 'images';
	
   // open specified directory
   $dirHandle = opendir($dir);
   $count = -1;
   $returnstr = "";
   while ($file = readdir($dirHandle)) {
      // if not a subdirectory and if filename contains the string '.jpg' 
//and strpos($file,'photo-strip')!==false and strpos($file,'.jpg')!==false
      if(!is_dir($file) && strpos($file, '.jpg')>0 && strpos($file,'photo-strip')!==false ) {
         // update count and string of files to be returned
         $count++;
         $returnstr[] = '<a href="'.$dir.'/'.$file.'">
         					http://www.forcremation.com/wp-content/themes/forcremation//images/'.$dir.'/'.$file.'
         					<Br /><img style="border:none;" src="'.$dir.'/'.$file.'" />
         					</a>';
      }
   } 
   closedir($dirHandle);
   foreach( $returnstr as $key=>$value){
   		echo $value.'<br /><Br /><br />';
   }
?>