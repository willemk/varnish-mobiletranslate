<code>
<pre>

sub devicedetect {
	unset req.http.X-UA-Device;
	set req.http.X-UA-Device = "browser";
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
?>
	}
}




<?
function returnVarnishRules($rulesArray, $key, $else = true){
	$retString = "\t\t";
	if ($else){
		$retString .= "els";
	}
	$retString .= "if (\n";
	$count = 0;
	foreach($rulesArray as $rule){
		$retString .= "\t\t";
		$retString .= "   (req.http.User-Agent ~ \"$rule\")"; 
		if ($count < (count((array)$rulesArray) -1)){
			$retString .= " ||\n";
		}else{
			 $retString .= ") {\n";	
		} 
		$count++;
	}
	$retString .= "\t\t\tset req.http.X-UA-Device = \"$key\";\n";
	$retString .= "\t\t}\n\n";

	return $retString;

}

?>
</pre>
</code>
