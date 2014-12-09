<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<? 

function execute_curl($url)
{
    $curl = curl_init();
 
// HEADERS FROM FIREFOX - SELECT TO BE A BROWSER REFERRED BY GOOGLE
 
    $header[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
    $header[] = "Cache-Control: max-age=0";
    $header[] = "Connection: keep-alive";
    $header[] = "Keep-Alive: 300";
    $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
    $header[] = "Accept-Language: en-us,en;q=0.5";
    $header[] = "Pragma: "; // browsers keep this blank.
 
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15');
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_REFERER, 'http://www.sherwoodhosting.com');
    curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 3); // GIVE UP AFTER THREE SECONDS
 
    if (!$html = curl_exec($curl))
    {
		echo "<\n!-- ERROR curl_exec($url)  failed in viewticket.inc.php-->\n";
        return FALSE;
    }
 
    curl_close($curl);
    return $html;
}// end function my_curl

$X=execute_curl('http://sherwoodphoto.com/API/account.php?d=wanamakerorgan.com'); 
echo "result = {$X} <br>";
print_r($X);

die();

echo "POST<br />\n";
print_r($_POST);
echo "<br />FILES<br />\n";
print_r($_FILES); 
echo"<br />--end--<br />\n";
?>
<form action=""  method="POST" enctype="multipart/form-data">
<table>
  <tr>
    <td align="right">XCaption</td><td></td>
    <td><textarea name="ImageCaption" cols="60" rows="3"></textarea>
    </td>
  </tr>
  <tr>
    <td align="right">Upload Image File</td><td></td>
    <td><input type="file" name="ImageFile" size="60" />
    </td>
  </tr>
  <tr>
    <td align="right">Submit Now --></td><td></td>
    <td><input type="submit" name="Command" value="Submit Image for Upload"/>
    </td>
  </tr>
  </table>
</form>
</body>
</html>