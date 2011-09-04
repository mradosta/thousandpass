<?php  
/** 
* This is a simple component to retrieve screenshots from the webthumb's easythumb API 
* http://webthumb.bluga.net/api-easythumb 
* 
* The configuration is quite simple, and though the functions are broken up for individual access,  
* the only way I ever use this is to use the getAndSave() method 
* which requests the image from webthumb and saves it to a file you specify. 
* 
* @requires cURL // If you are using debian/ubuntu, type in: # sudo apt-get install php5-curl 
* @author alan@zeroasterisk.com 
* @version 1.0 
* tested with cake 1.1.20.7692 
*/ 
class Webthumb {

    var $easythumb_url = 'http://webthumb.bluga.net/easythumb.php';
    var $user_id = '12495';
    var $api_key = '47525112197f3482d2f483d9eac34ce3';
    var $default_size = 'medium';
    var $default_cache = '-1';

    /** 
    * executes a request from webthumbs, and saves the resulting data as a file of some sort. 
    * @param string $saveToFile full filename to save as (eg: /var/www/filepath/filename.jpg) 
    * @param string $urlToThumbnail Site to thumbnail, full URL including protocol (eg: http://google.com) 
    * @param string $size [null] Size of the thumbnail to return small, medium1, medium2, large 
    * @param string $cache [null] The # of days old a cached version of the thumbnail can be -1 to 30 
    * @return bool 
    */ 
    function getAndSave($saveToFile,$urlToThumbnail,$size=null,$cache=null) { 
        $url = $this->makeEasythumbURL($urlToThumbnail,$size,$cache); 
        $data = $this->curl_get($url); 
        //if (!class_exists('File')) { uses('file'); } 
        //$file = new File($saveToFile, true); 
        //return $file->write($data,'w'); 
		file_put_contents($saveToFile, $data);
    }

    /** 
    * executes a request from webthumbs 
    * @param string $urlToThumbnail Site to thumbnail, full URL including protocol (eg: http://google.com) 
    * @param string $size [null] Size of the thumbnail to return small, medium1, medium2, large 
    * @param string $cache [null] The # of days old a cached version of the thumbnail can be -1 to 30 
    * @return string binary file data 
    */ 
    function get($urlToThumbnail,$size=null,$cache=null) { 
        $url = $this->makeEasythumbURL($urlToThumbnail,$size,$cache); 
        return $this->curl_get($url); 
    }

    /** 
    * creates the appropriate URL format to request from webthumbs 
    * @param string $urlToThumbnail Site to thumbnail, full URL including protocol (eg: http://google.com) 
    * @param string $size [null] Size of the thumbnail to return small, medium1, medium2, large 
    * @param string $cache [null] The # of days old a cached version of the thumbnail can be -1 to 30 
    * @return string $url 
    */ 
    function makeEasythumbURL($urlToThumbnail,$size=null,$cache=null) { 
        $unEncodedUrlToThumbnail = $urlToThumbnail; 
        $urlToThumbnail = urlencode($urlToThumbnail); 
        $size = urlencode((empty($size))?$size:$this->default_size);
        $cache = urlencode((empty($cache))?$cache:$this->default_cache); 
        $hash = md5(date('Ymd').$unEncodedUrlToThumbnail.$this->api_key); 
        return "{$this->easythumb_url}?user={$this->user_id}&url={$urlToThumbnail}&size={$size}&cache={$cache}&hash={$hash}"; 
    }

    /** 
    * cURL get the requested URL (and optional POST data) 
    * @param string $url 
    * @return string $curlResult 
    */ 
    function curl_get($url) { 
        if (!function_exists('curl_init')) { 
            die('Sorry - you need CURL and php5-curl (CURL module for php5).. If you are using debian/ubuntu, type in: # sudo apt-get install php5-curl'); 
        } 
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml")); 
        //curl_setopt ($ch, CURLOPT_HEADER, 0); 
        curl_setopt ($ch, CURLOPT_DNS_CACHE_TIMEOUT, 480); // 0 = forever, 5 = 5 seconds 
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 480); // 0 = forever, 5 = 5 seconds 
        curl_setopt ($ch, CURLOPT_TIMEOUT, 480); // 0 = forever, 5 = 5 seconds 
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        $curlResult = trim(curl_exec($ch)); 
        curl_close ($ch); 
        return $curlResult; 
    } 
} 
?>