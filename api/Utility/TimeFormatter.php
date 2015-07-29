<?php
	class TimeFormatter {
		public static function getJavascriptTimestamp($src) {
			if($src == NULL){
				return NULL;
			}
			$date = new DateTime($src);
			return $date->format('U') . '000';
		}
	}