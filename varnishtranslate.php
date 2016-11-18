<?php
$jsonRules = file_get_contents("https://raw.github.com/serbanghita/Mobile-Detect/master/Mobile_Detect.json");

$rules = json_decode($jsonRules);

?>
# Copyright (c) 2014, Willem Kappers
# All rights reserved.
#
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions are met:
#
# 1. Redistributions of source code must retain the above copyright notice, this
#    list of conditions and the following disclaimer.
# 2. Redistributions in binary form must reproduce the above copyright notice,
#    this list of conditions and the following disclaimer in the documentation
#    and/or other materials provided with the distribution.
#
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
# ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
# WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
# DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
# ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
# (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
# LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
# ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
# (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
# SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
#
# The views and conclusions contained in the software and documentation are those
# of the authors and should not be interpreted as representing official policies,
# either expressed or implied, of the FreeBSD Project
# mobile_detect.vcl - Drop-in varnish solution to mobile user detection based on the Mobile-Detect library
#
# https://github.com/willemk/varnish-mobiletranslate
#
# Author: Willem Kappers

sub devicedetect {
	#Based on Mobile detect <?php echo $rules->version ?>

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

/**
 * Mobile Bots visit sites a lot. In order to calculate regex fast
 * MobileBots must be first. Also it has much less expressions than Phones or Tablets
 */
$mobileBots = array($rules->uaMatch->utilities->MobileBot);
echo returnVarnishRules($mobileBots, 'mobile', false);

$mobileBrowsers = $rules->uaMatch->browsers;
echo returnVarnishRules($mobileBrowsers, "mobile", false, true);

$phones = $rules->uaMatch->phones;
echo returnVarnishRules($phones, "mobile", false, true);

$tablets = $rules->uaMatch->tablets;
echo returnVarnishRules($tablets, "tablet", true, true);

$mobileOS = $rules->uaMatch->os;
echo returnVarnishRules($mobileOS, "mobile", false, true);

?>
}
}

<?php
function returnVarnishRules($rulesArray, $key, $tablet = false, $useElse = false)
{
    $retString = "\t\t";
    if ($useElse) {
        $retString .= "elsif (\n";
    } else {

        $retString .= "if (\n";
    }
    $count = 0;
    foreach ($rulesArray as $rule) {
        $retString .= "\t\t";
        $retString .= "   (req.http.User-Agent ~ \"(?i)$rule\")";
        if ($count < (count((array)$rulesArray) - 1)) {
            $retString .= " ||\n";
        } else {
            $retString .= ") {\n";
        }
        $count++;
    }
    if ($tablet) {
        $retString .= "\t\t\tset req.http.X-UA-Device = \"mobile;$key\";\n";
    } else {
        $retString .= "\t\t\tset req.http.X-UA-Device = \"$key\";\n";
    }
    $retString .= "\t\t}\n\n";

    return $retString;

}

?>
