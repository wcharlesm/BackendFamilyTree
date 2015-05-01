<?php
$q = explode("&",$_SERVER["QUERY_STRING"]);
foreach ($q as $qi){
	if ($qi != ""){
	  	$qa = explode("=",$qi);
		list ($key, $val) = $qa;
	
		if ($val)$$key = urldecode($val);

	}
}
//--------------------------
reset ($_POST);
while (list ($key, $val) = each ($_POST)){
      if ($val){

		$$key = $val;
	}
}	
//--------------------------
error_reporting(0);
//--------------------------

?>