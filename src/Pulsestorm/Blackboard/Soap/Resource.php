<?php
/*
 * Copyright (C) 2015 by Pulse Storm LLC (www.pulsestorm.net)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * Copyright (C) 2015 by Pulse Storm LLC (www.pulsestorm.net)
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE. 
 */ 
namespace Pulsestorm\Blackboard\Soap;
use Pulsestorm\Blackboard\Soap\Legacy\Bbphp;
class Resource extends Bbphp
{
        private $curl_options;
	public function __construct($url = null, $use_curl = true) {
		$this->url = $url;
		$this->use_curl = $use_curl;
		// $this->session_id = $this->Context("initialize");
	}
        
        public function doCall($method = null, $service = "Context", $args = null) {
		
		$request = $this->buildRequest($method, $service, $args);
        $this->log($request);
        
		if ($this->use_curl) {
			$ch = curl_init();
				
			curl_setopt($ch, CURLOPT_URL, $this->url . '/webapps/ws/services/' . $service . '.WS');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml; charset=utf-8', 'SOAPAction: "' . $method . '"'));
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        
                        if (is_array($this->curl_options) && count($this->curl_options) > 0) {
                            curl_setopt_array($ch, $this->curl_options);
                        }
			
			$result = curl_exec($ch);
			curl_close($ch);	
		} else {
			$result = $this->doPostRequest($this->url . '/webapps/ws/services/' . $service . '.WS', $request, "Content-type: text/xml; charset=utf-8\nSOAPAction: \"" . $method . "\"");
		}

        $this->log($result);

		$result_array = $this->xmlstr_to_array($result);

		$final_result = (isset($result_array['Body'][$method . 'Response']['return'])) ? $result_array['Body'][$method . 'Response']['return'] : null;
		return $final_result;
	}
	
	public function setSessionId($session_id)
	{
	    $this->session_id = $session_id;
	    return $this;
	}
        public function setCurlOptions($options) {
            $this->curl_options = $options;
        }
}