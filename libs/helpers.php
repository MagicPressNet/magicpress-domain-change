<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function printMsg($msg){
	echo '<p>'.$msg.'</p>';
	ob_flush();
	flush();
}
function printSimpleMsg($msg){
    echo $msg;
    ob_flush();
    flush();   
}

function newValue($value,$currentDomain,$newDomain){
    
    if(gettype($value)=='array'){
        $newValue=array();
        foreach ($value as $key=>$val) {
            $newValue[$key]=newValue($val,$currentDomain,$newDomain);
        }
        return $newValue;
    }elseif(startsWith($value,'a:') || startsWith($value,'s:') ){
        $serializedArr=unserialize($value);
        return serialize(newValue($serializedArr,$currentDomain,$newDomain));        
    }elseif(gettype($value)=='string'){
        return str_replace( urldecode($currentDomain) , urldecode($newDomain) , $value);
    }

    return $value;
}

function startsWith($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}  
?>