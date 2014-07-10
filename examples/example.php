<?PHP

require 'cache.class.php';

$cache = new class_cache;
 
// load cache if isset
$example = $cache -> load( 'test');
 
// if use_cache return false, this write cache
if( ! $cache -> use_cache()) {
        $example = 'test cache';
 
        //$cache -> save('cache_name', $variables, time_in_seconds);
        $cache -> save('test', $example, 20);
}
 
echo $example;
 
/**
$cache -> flush( 'test');
delete cache of 'test' name
*/
?>