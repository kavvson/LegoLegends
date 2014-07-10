<?PHP 
/**
 * @autor		Piotr Miloszewicz
 * @www 		FiveGroup
 * @name		Cache variables
 */

//define cache path
	define( 'CACHE_PATH', './cache');
	
class class_cache {
	
	public $check_cache = false;
	
	/**
	 * save data to cache
	 *
	 * @param: mixed $name
	 * @param: mixed $value
	 * @param: (int) $timeout
	 */
	
	public function save( $name, $value, $timeout = 0) {
	
		//start cache file
		$cache_file = "<?PHP\n\n";
		
		//set description to cache file
		$cache_file .= "/*\n	CACHE FILE NAME: ". $name ."\n	GENERATE TIME: ". date('c') ."\n*/\n\n";
		
		//set data to cache file
		$timeout = $timeout + time();
		$cache_file .= "//cache timeout\n" . '$timeout = '. $timeout . ";\n\n";
		
		//set variable to cache
		$cache_file .= "//cache content\n" . '$cache_variable = '. var_export($value, true) .';';
		
		//end cache
		$cache_file .= "\n\n?>";
		
		//return cache to save
		return file_put_contents( '/'. $name .'.php', $cache_file);
	}
	
	/**
	 * load cache 
	 *
	 * @return: mixed
	 */
	
	public function load( $name) {
	
		//chech was cache exists
		if( file_exists('/'. $name .'.php')) {
		
			//load cache
			include '/'. $name .'.php';
			
			//chech was cache no expired
			if($timeout > time()) {

				$this -> check_cache = true;
			}
			
			// return cache
			return $cache_variable;
			
		} else {
		
			// return error
			return "<b>Error</b>\n\n Cache file does not exists.";
		}
	}
	
	/**
	 * check value of check_cache field
	 * 
	 */
	
	public function use_cache() {
		return $this -> check_cache;
	}
	
	/**
	 * delete cache
	 *
	 * @param: mixed $name;
	 */
	 
	public function flush( $name) {
	
		if( file_exists( '/'. $name .'.php')) {
		
			unlink( '/'. $name .'.php');
		}
	} 
}
		
?>