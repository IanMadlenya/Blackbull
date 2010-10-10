<?php
require_once('../serverscripts/dba.php');
if($_REQUEST['filedate']){
$filedate = $_REQUEST['filedate'];
$csvdate = date("d-m-Y", strtotime($filedate));
}else{
$filedate = date("dMY");
$csvdate = date("d-m-Y");
}

$sqldate = date("Y-m-d",strtotime($filedate));

$baseurl = 'http://www.nseindia.com/content/historical/DERIVATIVES/';
$filename = 'fo'.strtoupper($filedate).'bhav.csv';
$url = $baseurl.date("Y",strtotime($filedate)).'/'.strtoupper(date("M",strtotime($filedate))).'/'.$filename.'.zip';
if(!url_exists($url)){
echo "FO data is up to date.\n";
}else{
$data = file_get_contents($url,FILE_BINARY);
if(!$data){
echo "FO data is up to date.\n";
}
$fp = fopen('test.zip', 'w');
fwrite($fp, $data);
fclose($fp);
$zip = new ZipArchive;
if ($zip->open('test.zip') === TRUE) {
    $zip->extractTo('.');
    $zip->close();
    unlink('test.zip');
    rename($filename, 'fobhav.csv');
    $filecontents = file("fobhav.csv");
    $fail = 0;
    $pass = 0;
    $deleterecords = "TRUNCATE TABLE `fobhav`";
    mysql_query($deleterecords);

    for($i=1; $i<sizeof($filecontents); $i++) {
        $data = substr($filecontents[$i], 0, -2);
        $dataarray = explode(',',$data);
        $insertrecord = "Insert Into fobhav values (NULL,'".$dataarray[0]."','".$dataarray[1]."','".$dataarray[2]."',".$dataarray[3].",'".$dataarray[4]."',".$dataarray[5].",".$dataarray[6].",".$dataarray[7].",".$dataarray[8].",".$dataarray[9].",".$dataarray[10].",".$dataarray[11].",".$dataarray[12].",".$dataarray[13].",'".$dataarray[14]."')";
        
         mysql_query($insertrecord);
         if(mysql_error()) {
            $fail += 1; # increments if there was an error importing the record
         }
          else
         {
            $pass += 1; # increments if the record was successfully imported
         }
    }
     
    if($fail == 0){
       unlink('fobhav.csv');
    }

    $updatetimefo = "UPDATE updatestatus SET timestamp= NOW() WHERE tablename='fobhav'";
    $updatetimeforesult = mysql_query($updatetimefo);

    echo "FO update successful\n";
} else {
    echo 'failed';
}
}

$baseurl = 'http://www.nseindia.com/content/indices/histdata/';
$filename = "S&P%20CNX%20NIFTY".$csvdate."-".$csvdate.".csv";

$url = $baseurl.$filename;
if(url_exists($url)){
$file = fopen($url,"r");

$stocks = fgetcsv($file);
while($stocks = fgetcsv($file)) {
   $insertrecord = "Insert Into cmbhav values ('NIFTY', NULL,".$stocks[1].",".$stocks[2].",".$stocks[3].",".$stocks[4].", NULL, NULL, NULL, NULL,'" .$sqldate."')";
   mysql_query($insertrecord);
}

$filename = "CNX%20NIFTY%20JUNIOR".$csvdate."-".$csvdate.".csv";
$url = $baseurl.$filename;
$file = fopen($url,"r");
$stocks = fgetcsv($file);
while($stocks = fgetcsv($file)) {
   $insertrecord = "Insert Into cmbhav values ('MINIFTY', NULL,".$stocks[1].",".$stocks[2].",".$stocks[3].",".$stocks[4].", NULL, NULL, NULL, NULL,'" .$sqldate."')";
   mysql_query($insertrecord);
}

$filename = "BANK%20NIFTY".$csvdate."-".$csvdate.".csv";
$url = $baseurl.$filename;
$file = fopen($url,"r");

$stocks = fgetcsv($file);
while($stocks = fgetcsv($file)) {
   $insertrecord = "Insert Into cmbhav values ('BANKNIFTY', NULL,".$stocks[1].",".$stocks[2].",".$stocks[3].",".$stocks[4].", NULL, NULL, NULL, NULL,'" .$sqldate."')";
   mysql_query($insertrecord);
}
echo "INDEX update successful.\n";
}else{
echo "INDEX data is up to date.\n";
}

$baseurl = 'http://www.nseindia.com/content/historical/EQUITIES/';
$filename = 'cm'.strtoupper($filedate).'bhav.csv';

$url = $baseurl.date("Y",strtotime($filedate)).'/'.strtoupper(date("M",strtotime($filedate))).'/'.$filename.'.zip';
if(url_exists($url)){
$data = file_get_contents($url,FILE_BINARY);
$fp = fopen('test.zip', 'w');
fwrite($fp, $data);
fclose($fp);
$zip = new ZipArchive;
if ($zip->open('test.zip') === TRUE) {
    $zip->extractTo('.');
    $zip->close();
    unlink('test.zip');
    rename($filename, 'cmbhav.csv');
    $filecontents = file("cmbhav.csv");
    $fail = 0;
    $pass = 0;
    
    for($i=1; $i<sizeof($filecontents); $i++) {
        $data = substr($filecontents[$i], 0, -2);
        $dataarray = explode(',',$data);
        $insertrecord = "Insert Into cmbhav values ('".$dataarray[0]."','".$dataarray[1]."',".$dataarray[2].",".$dataarray[3].",".$dataarray[4].",".$dataarray[5].",".$dataarray[6].",".$dataarray[7].",".$dataarray[8].",".$dataarray[9].",'".$sqldate."')";
         mysql_query($insertrecord);
         if(mysql_error()) {
            $fail += 1; # increments if there was an error importing the record
         }
          else
         {
            $pass += 1; # increments if the record was successfully imported
         }
    }
    
    if($fail == 0){
       unlink('cmbhav.csv');
    }

    $updatetime = "UPDATE updatestatus SET timestamp= NOW() WHERE tablename='cmbhav'";
    $updatetimeresult = mysql_query($updatetime);

    echo "EQ update successful.\n";
}else{
    echo "EQ update failed.\n";
}
}else{
    echo "EQ data is up to date.\n";
}

//http://www.nseindia.com/content/equities/temp_csv/eq_fiidii_23-09-201023-09-2010.csv
$baseurl = 'http://www.nseindia.com/content/equities/temp_csv/';
$filename = 'eq_fiidii_'.$csvdate.$csvdate.'.csv';
$url = $baseurl.$filename;
if(url_exists($url)){
	update_fiidii($url, $sqldate);
}else{
	$request = array("category" => "noValue","fromDate" => $csvdate,"toDate" => $csvdate,"check" => "new");
	list($header, $content) = PostRequest("http://www.nseindia.com/marketinfo/equities/eq_fiidii_archives.jsp","http://www.nseindia.com/content/equities/eq_fiidii_archives.htm",$request);
	if(url_exists($url)){
		update_fiidii($url, $sqldate);
	}
}


$deleteoldcm = "DELETE FROM cmbhav where DATEDIFF( CURRENT_DATE( ) , timestamp ) > 230";
$del_cm_result = mysql_query($deleteoldcm);

$optimize = "OPTIMIZE TABLE cmbhav";
$optimize_result = mysql_query($optimize);

$deletepending = "DELETE FROM pending where DATEDIFF( CURRENT_DATE( ) , date ) > 10";
$del_result = mysql_query($deletepending);

mysql_close($con);

function url_exists($url) {
    $hdrs = @get_headers($url);
    return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false;
};

function update_fiidii($url, $sqldate){
	$file = fopen($url,"r");
	$fiidii = fgetcsv($file);
	while($fiidii = fgetcsv($file)) {
		if(strtoupper($fiidii[0]) == "FII"){
			$fii = floatval($fiidii[4]);
		}
		if(strtoupper($fiidii[0]) == "DII"){
			$dii = floatval($fiidii[4]);
		}
	}
	$fiidiiquery = "SELECT fii, dii from fiidii WHERE 1 ORDER BY fiidii_date DESC LIMIT 1";
	$fiidiiresult = mysql_query($fiidiiquery);
	if($fiidiiresult){
		$fiidiirow = mysql_fetch_array($fiidiiresult);
		$newfii = $fii + $fiidiirow['fii'];
		$newdii = $dii + $fiidiirow['dii'];
		
		$addfiidii = "INSERT INTO fiidii VALUES('".$sqldate."',".$newfii.",".$newdii.")";
		$addfiidiiresult = mysql_query($addfiidii);
		if($addfiidiiresult){
			echo "FII update successful.\n";
			$updatetimefo = "UPDATE updatestatus SET timestamp= '".$sqldate."' WHERE tablename='fiidii'";
    			$updatetimeforesult = mysql_query($updatetimefo);
		}else{
			echo "FII update failed.\n";
		}
	}
	fclose($file);
};

function PostRequest($url, $referer, $_data) {
 
    // convert variables array to string:
    $data = array();    
    while(list($n,$v) = each($_data)){
        $data[] = "$n=$v";
    }    
    $data = implode('&', $data);
    // format --> test1=a&test2=b etc.
 
    // parse the given URL
    $url = parse_url($url);
    if ($url['scheme'] != 'http') { 
        die('Only HTTP request are supported !');
    }
 
    // extract host and path:
    $host = $url['host'];
    $path = $url['path'];
 
    // open a socket connection on port 80
    $fp = fsockopen($host, 80);
 
    // send the request headers:
    fputs($fp, "POST $path HTTP/1.1\r\n");
    fputs($fp, "Host: $host\r\n");
    fputs($fp, "Referer: $referer\r\n");
    fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
    fputs($fp, "Content-length: ". strlen($data) ."\r\n");
    fputs($fp, "Connection: close\r\n\r\n");
    fputs($fp, $data);
 
    $result = ''; 
    while(!feof($fp)) {
        // receive the results of the request
        $result .= fgets($fp, 128);
    }
 
    // close the socket connection:
    fclose($fp);
 
    // split the result header from the content
    $result = explode("\r\n\r\n", $result, 2);
 
    $header = isset($result[0]) ? $result[0] : '';
    $content = isset($result[1]) ? $result[1] : '';
 
    // return as array:
    return array($header, $content);
}
?>