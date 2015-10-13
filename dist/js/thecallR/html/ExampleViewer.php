<?php
class ExampleViewer {
	
	private $example_path;
	private $example_file_ignored = array('.','..','realtime');
	
	const DOC_URL = 'http://thecallr.com/docs';
	
	public function __construct($example_path) {
		if (!is_dir($example_path)){throw new Exception('EXAMPLE_PATH_NOT_EXISTS');}
		$this->example_path = rtrim($example_path,"/\\");
	}
	
	public function get_summary() {
		$summary = array();
		if (!is_null($this->example_path)) {
			$path_contents = scandir($this->example_path);
			foreach ($path_contents as $file) {
				$filename = str_replace('.php','',$file);
				if (is_file($this->example_path.'/'.$file) && !in_array($filename,$this->example_file_ignored)) {
					$fileContent = file_get_contents($this->example_path.'/'.$file);
					if (preg_match("#<h3>(.*)</h3>#", $fileContent, $match)) {
						$summary[$filename] = trim($match[1]);
					}
				}
			}
		}
		return $summary;
	}
	
	public function get_example($example_file) {
		$example_file = $this->example_path.'/'.$example_file.'.php';
		$html = array();
		if (!is_null($this->example_path) && file_exists($example_file)) {
			$file = file($example_file);
			$comment = false;
			$marker = null;
			$text = null;
			$code = null;
			foreach ($file as $line) {
				$line = trim($line," \n\r\0");
				if (substr($line,0,3) == '/**') {
					$comment = true;
				}else if (substr($line,0,2) == '*/') {
					$comment = false;
					$code = array();
				}else if (is_array($code) && $line != '?>') {
					$line = htmlspecialchars($line);
					$code[] = $line;
				}
				if ($comment === TRUE && is_null($marker) && preg_match("#<([a-zA-Z]{0,1}[^/])>#",$line,$match)) {
					$marker = $match[1];
					if (strpos($line,'</'.$marker.'>') !== FALSE) {
						$b = strpos($line,$match[0]);
						$e = strrpos($line,'</'.$marker.'>') + strlen('</'.$marker.'>');
						$html[] = substr($line,$b,($e-$b)+1)."\n";
						$marker = null;
					}else{
						$text = array(substr($line,strpos($line,'<')));
					}
				}else if ($comment === TRUE && !is_null($marker) && is_array($text) && strpos($line,'</'.$marker.'>') !== FALSE) {
					$line = ltrim($line,'* ');										  
					$html[] = implode(' ',$text).' '.substr($line,0,strpos($line,'</'.$marker.'>') + strlen('</'.$marker.'>'))."\n";
					$marker = null;
					$text = null;
				}else if ($comment === TRUE && !is_null($marker) && is_array($text)){
					$text[] = ltrim($line,'* ');
				}
				if (($comment === TRUE || substr($line,0,2) == '?>') && is_array($code) && count($code) > 0) {
					$html_code = trim(implode("\n",$code));
					$code = null;
					if (!empty($html_code)){
						$html[] = "<pre class=\"code prettyprint\">".$html_code."</pre>\n";
					}
				}
			}
			foreach ($html as &$line) {	
				if (preg_match_all('#href="(.*)"#',$line,$matches)) {
					foreach ($matches[1] as $match) {
						$line = str_replace($match,self::DOC_URL.'/'.ltrim($match,'/'),$line);
					}
				}
			}
		}
		return $html;
	}
	
}
?>