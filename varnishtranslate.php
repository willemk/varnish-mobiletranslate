<h1>Varnish Translate</h1>
<pre>

sub devicedetect {
	unset req.http.X-UA-Device;
	set req.http.X-UA-Device = "browser";
<?php

$jsonRules =  file_get_contents("https://raw.github.com/serbanghita/Mobile-Detect/master/Mobile_Detect.json");

$rules = json_decode($jsonRules);



$phones = $rules->uaMatch->phones;
echo returnVarnishRules($phones,"phone", false);

$tablets = $rules->uaMatch->tablets;
echo returnVarnishRules($tablets,"tablet");

$desktop = $rules->uaMatch->browsers;
echo returnVarnishRules($desktop,"desktop");

$os = $rules->uaMatch->browsers;
echo returnVarnishRules($phones,"os");

function returnVarnishRules($rulesArray, $key, $else = true){
	$retString = "\t";
	if ($else){
		$retString .= "els";
	}
	$retString .= "if (\n";
	$count = 0;
	foreach($rulesArray as $rule){
		$retString .= "\t";
		$retString .= "   (req.http.User-Agent ~ \"$rule\")"; 
		if ($count < (count((array)$rulesArray) -1)){
			$retString .= " ||\n";
		}else{
			 $retString .= ") {\n";	
		} 
		$count++;
	}
	$retString .= "\t\tset req.http.X-UA-Device = \"$key\";\n";
	$retString .= "\t}\n\n";

	return $retString;

}

?>

