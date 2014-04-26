<?php

//require_once ("config.php");
//require_once ("utils.php");
//require_once ("simplexml.php");


class cpRequest {
	var $_secret;
	var $_hwr;
	var $_response;
	var $_vars = Array ( );
	var $_ret;
	var $url;
	
	function cpRequest($secret) {
		$this->_secret = $secret;
		$this->url = "http://www.cincopa.com/media-platform/api/api.aspx";
		$this->_changed = 0;
	}
	
	function value($raw = false) {
		return $raw ? $this->_response : $this->_ret;
	}
	
	function BuildRequest($destination) {
		$this->_hwr = curl_init ( $destination );
		
		$headers = Array ("Accept: */*", "Accept-Encoding: gzip", "Accept-Language: en-us", "Content-Type: application/x-www-form-urlencoded" );
		
		curl_setopt ( $this->_hwr, CURLOPT_USERAGENT, "php cincopa framework" );
		curl_setopt ( $this->_hwr, CURLOPT_POST, 0 );
		curl_setopt ( $this->_hwr, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $this->_hwr, CURLOPT_FOLLOWLOCATION, 0 );
		curl_setopt ( $this->_hwr, CURLOPT_RETURNTRANSFER, 1 );
	}
	
	public function Add($key, $val) {
		if ($val !== FALSE) {
			$this->_vars [$key] = $val;
			return true;
		} else {
			return false;
		}
	}
	
	function GetResponse($xml=false, $debug=false, &$log=null) {
		$this->AddSignature ();
		$this->AddParams ();
		$this->BuildRequest ($this->url);
		$this->_response = $this->ReadResponse ( curl_exec ( $this->_hwr ) );
		
		if ($xml) {
			$this->_ret = new simplexml ( );
			$this->_ret = $this->_ret->parsexml ( $this->_response );
		} else {
			$this->_ret = $this->_response;
			//Utils::remove_extra_char ( $this->_ret, ',' );
			//Utils::remove_extra_char ( $this->_ret, '}', false );
		}
		
		if ($debug)
			$log = $this->LogRequest ( $this->_ret );
		
		curl_close ( $this->_hwr );
		
		return $this->_response;
	}
	
	function LogRequest($retVal) {
		$sb = "\r\n<!-- cpAPI Request\r\n";
		
		foreach ( $this->_vars as $key => $value ) {
			$sb .= $key . "     " . $value . "\r\n";
		}
		
		$sb .= "\r\nResponse\r\n";
		
		if (is_a ( $retVal, 'simplexml' ))
			$sb .= $retVal->asXML (); else {
			ob_start ();
			var_dump ( $retVal );
			$sb .= ob_get_contents ();
			ob_end_clean ();
		}
		
		$sb .= "\r\n-->\r\n";
		
		return $sb;
	}
	
	function AddSignature() {
		$sb = "";
		ksort ( $this->_vars );
		
		foreach ( $this->_vars as $key => $value ) {
			if (! empty ( $key ))
				$sb .= $key;
			
			if (! empty ( $value ))
				$sb .= $value;
		}
		
		$sb .= $this->_secret;
		
		// Create signature
		$md5 = pack ( 'H*', md5 ( $sb ) );
		$this->_vars ["sig"] = base64_encode ( $md5 );
	}
	
	function getSig(){
		$this->AddSignature();
		return urlencode($this->_vars ["sig"]);
	}
	
	
	function AddParams() {
		$params = $this->http_build_query ( $this->_vars );
		$this->url .= "?".$params;
		//curl_setopt ( $this->_hwr, CURLOPT_POSTFIELDS, $params );
	}
	
	function ReadResponse($res) {
		return gzinflate ( substr ( $res, 10 ) );
	}
	
	function http_build_query($a, $b = '', $c = 0) {
	
		if (! is_array ( $a ))
			return false;
			
		foreach ( ( array ) $a as $k => $v ) {
			if ($c)
				$k = $b . "[" . $k . "]"; 
			elseif (is_int ( $k ))
				$k = $b . $k;
				
			if (is_array ( $v ) || is_object ( $v )) {
				$r [] = $this->http_build_query ( $v, $k, 1 );
				continue;
			}
			$r [] = $k . "=" . urlencode ( $v );
		}
		return implode ( "&", $r );
	}
	
}


?>