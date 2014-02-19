<?php
$jsonRules =  file_get_contents("https://raw.github.com/serbanghita/Mobile-Detect/master/Mobile_Detect.json");

$rules = json_decode($jsonRules);

?>
sub devicedetect {
	#Based on Mobile detect <?php echo $rules->version?>
	
	#https://github.com/serbanghita/Mobile-Detect
	unset req.http.X-UA-Device;
	set req.http.X-UA-Device = "desktop";
	# Handle that a cookie may override the detection alltogether.
	if (req.http.Cookie ~ "(?i)X-UA-Device-force") {
		/* ;?? means zero or one ;, non-greedy to match the first. */
		set req.http.X-UA-Device = regsub(req.http.Cookie, "(?i).*X-UA-Device-force=([^;]+);??.*", "\1");
		/* Clean up our mess in the cookie header */
		set req.http.Cookie = regsuball(req.http.Cookie, "(^|; ) *X-UA-Device-force=[^;]+;? *", "\1");
		/* If the cookie header is now empty, or just whitespace, unset it. */
		if (req.http.Cookie ~ "^ *$") { unset req.http.Cookie; }
	} else {

<?php


$phones = $rules->uaMatch->phones;
echo returnVarnishRules($phones,"mobile");

$mobileBrowsers = $rules->uaMatch->browsers;
echo returnVarnishRules($mobileBrowsers,"mobile",false, true);

$mobileOS = $rules->uaMatch->os;
echo returnVarnishRules($mobileOS,"mobile",false, true);

$tablets = $rules->uaMatch->tablets;
echo returnVarnishRules($tablets,"tablet",true);


?>
	}
}




<?php
function returnVarnishRules($rulesArray, $key, $tablet = false, $useElse = false){
	$retString = "\t\t";
	if ($useElse){
		$retString .= "elsif (\n";
	}else{
			
		$retString .= "if (\n";
	}
	$count = 0;
	foreach($rulesArray as $rule){
		$retString .= "\t\t";
		$retString .= "   (req.http.User-Agent ~ \"(?i)$rule\")"; 
		if ($count < (count((array)$rulesArray) -1)){
			$retString .= " ||\n";
		}else{
			 $retString .= ") {\n";	
		} 
		$count++;
	}
	if ($tablet){
		$retString .= "\t\t\tset req.http.X-UA-Device = \"mobile;$key\";\n";
	}else{
		$retString .= "\t\t\tset req.http.X-UA-Device = \"$key\";\n";
	}
	$retString .= "\t\t}\n\n";

	return $retString;

}

?>
