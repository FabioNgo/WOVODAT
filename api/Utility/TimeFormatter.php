<?php
	class TimeFormatter {
		public static function getJavascriptTimestamp($src) {
			$date = new DateTime($src);
			return $date->format('U') . '000';
		}
	}