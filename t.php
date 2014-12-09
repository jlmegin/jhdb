<?
//copy("/home/jazzhist/public_html/content/empty-dir-do-not-delete", "/home/jazzhist/public_html/content/test1");


echo "<br>0 file owner:".fileowner("/home/jazzhist/public_html/content/")."<br>\n";

$BioFileDir = "/home/jazzhist/public_html/content/test2222";
mkdir($BioFileDir, 0777);
echo "<br>1 file owner:".fileowner($BioFileDir)."<br>\n";
chown($BioFileDir, "jazzhist");
echo "<br>2 file owner:".fileowner($BioFileDir)."<br>\n";

echo shell_exec("chown jazzhist ".$BioFileDir);
//echo exec("dir /home/jazzhist/public_html/content/ ");
echo "<br>3 file owner:".fileowner($BioFileDir)."<br>\n";

echo exec("chown jazzhist ".$BioFileDir);

echo "<br>4 file owner:".fileowner($BioFileDir)."<br>\n";

//echo "<br>cp -av /home/jazzhist/public_html/content/empty-dir-do-not-delete /home/jazzhist/public_html/content/test22<br>";

//	$BioFile = fopen($BioFileDir."/test.txt", "w") or die("Error [test]: Bio Create File: Unable to open file: {$BioFileDir}");
//					fwrite($BioFile, "123");
//					fclose($BioFile);
					
					
					echo
					" <br> done<br>";