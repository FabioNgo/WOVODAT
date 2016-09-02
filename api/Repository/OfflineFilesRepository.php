<?php
	/**
	*	This class supports query the information from vd table
	* 	
	*/
	class OfflineFilesRepository {
		private static $ignoreList = array('api','.git','index.php','.gitignore','README.md','offline-data');
		private static $rootDir = "..";
		/**
		*
		*
		*/
		public static function getOfflineFilesList() {
			// echo(getcwd());
			
			
			$result = OfflineFilesRepository::getFilesList(OfflineFilesRepository::$rootDir);
			return $result;
		}
		private static function isIgnored($path){
			foreach (OfflineFilesRepository::$ignoreList as $item) {
				if($path == OfflineFilesRepository::$rootDir."/".$item){
					return true;
				}
			}
			return false;
		}
		private static function getFilesList($dir){

			$files1=array_diff(scandir($dir,1), array('..', '.'));
			$result = array();
			foreach ($files1 as $file ) {
				if(OfflineFilesRepository::isIgnored($dir.'/'.$file)){
					continue;
				}
				
		      	if(is_dir($dir.'/'.$file)){
		        	$temp = $dir.'/'.$file;

		        	$result1 = OfflineFilesRepository::getFilesList($temp);
		        	$result = array_merge($result,$result1);
		      	}else{
		      		$path = $dir.'/'.$file;
		      		//remove rootDir
		      		$path = substr($path, 3);
		      		array_push($result,$path);
		      	}
		    }
			return $result;
		}
	}
