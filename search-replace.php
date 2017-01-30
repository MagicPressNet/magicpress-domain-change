<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

set_time_limit(0); 
ini_set('memory_limit','256M');

if(ob_get_level()==0){ ob_start(); }

include(domainChangePluginPath().'libs/helpers.php');

printMsg('<div class="wrap"><h1>Domain change</h1></div>');

include(domainChangePluginPath().'libs/mysql.php');

if(!is_object($db) || !is_object($db2) ){
	printMsg('<p>Failed to connect to database...</p>');
} 
  
$tables='Tables_in_'.$dbname;

$db->query('SHOW TABLES LIKE "%options"');
$db->getData();
$tableOptions=$tables.' (%options)';
$optionsTable= $db->records->$tableOptions; 

printMsg('Changing options <b>siteurl, home.</b>');
$db->query('UPDATE '.$optionsTable.' SET option_value="'.urldecode($newDomain).'" WHERE option_name="siteurl" ');
$db->query('UPDATE '.$optionsTable.' SET option_value="'.urldecode($newDomain).'" WHERE option_name="home" ');

$db->query('SHOW TABLES LIKE "%posts"');
$tablePosts=$tables.' (%posts)';
$db->getData();
$postsTable= $db->records->$tablePosts;    

$db->query('SELECT guid FROM '.$postsTable.' WHERE guid LIKE "%'.urldecode($currentDomain).'%"');
$db->getData();

printMsg('Current domain found <b>'.$db->numRows().'</b> times on <b>'.$postsTable.'</b>');

printMsg('Changing domain on: <b>'.$postsTable.'</b>');
$db->query('UPDATE '.$postsTable.' SET guid=REPLACE(guid, "'.urldecode($currentDomain).'", "'.urldecode($newDomain).'") WHERE guid LIKE "%'.urldecode($currentDomain).'%"');

$search = array(); 
$tableKeys  = array();

$db->query('SHOW TABLES');

$inc=0;

printMsg('Searching database...');

while($db->getData()){
    

    $db2->query('SHOW COLUMNS FROM '.$db->records->$tables);
    while($db2->getData()){
        if(strstr($db2->records->Type , 'text') || strstr($db2->records->Type , 'varchar') && $db2->records->Field!='guid' && $db->records->$tables!=$postsTable){
            $search[]=array(
                            'table' => $db->records->$tables,
                            'field' => $db2->records->Field
                        );
            
            $inc++;
        } 

        if($db2->records->Key=='PRI'){
            $tableKeys[$db->records->$tables]=$db2->records->Field;
        }
    }
}

printMsg('Found <b>'.count($search).' fields</b> that current domain can be stored.');


$searchResults = array();
$ubc=0;
foreach ($search as $key => $case) {
    $db->query('SELECT '.$case['field'].','.$tableKeys[$case['table']].' FROM '.$case['table']);
    while($db->getData()){
        
        if(strstr($db->records->$case['field'],  urldecode($currentDomain) )){
            try {
                $searchResults[] = array(
                                'table' => $case['table'],
                                'field' => $case['field'],
                                'value' => $db->records->$case['field'],
                                'keyValue'=> $db->records->$tableKeys[$case['table']],
                                'newValue'=> newValue($db->records->$case['field'],$currentDomain,$newDomain),
                            );  

            } catch (Exception $e) {
                 
            }
            
        }
        
    }

}
printMsg('Current domain found <b>'.count($searchResults).'</b> times.');

printSimpleMsg('<p>Updating');
foreach ($searchResults as $key => $update) {
    printSimpleMsg('.');
    $q= 'UPDATE '.$update['table'].' SET '.$update['field']."='".str_replace("'","\'",$update['newValue'])."' WHERE  ".$tableKeys[$update['table']].'='.$update['keyValue']; 
    try {
        $db->query($q);    
    } catch (Exception $e) {
        
    }
    
}
printSimpleMsg('</p>');
printMsg('Done :) ');
ob_end_clean();

?>