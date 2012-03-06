<?
class CurlAuth {
	public $content;
	public function init() {
		return new self();
	}
	public function load($url, $post = false, $headers = false) {
		global $base;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16 20:23:00'); // Юзер-агент
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt'); // Cookie
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt'); // Cookie
		if (is_array($headers))
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Обработка всех Location на автомате
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Результат в переменную
		curl_setopt($ch, CURLOPT_HEADER, 1); // Получить заголовки
		if (!empty($post)) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // SSL
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // SSL
		curl_setopt($ch, CURLOPT_VERBOSE, 0); // Подробно о соединении
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Таймаут
		curl_setopt($ch, CURLOPT_ENCODING , 'gzip'); // Сжатие
		$out = curl_exec($ch);
		curl_close($ch);
		$this->content = $out;
		return $this;
	}
	public function login($url) {
		$post = 'user_name=romachu&user_pass=qwerty&login=%C2%EE%E9%F2%E8';
		$this->load($url, $post);
		return $this;
	}
	public function utf8(){
		preg_match('/Content-Type.*charset=(.*)/', $this->content, $chr);
		if (isset($chr[1]) && !preg_match('/utf-8/i', $chr[1]))
			$this->content = preg_replace('/charset=(.*)"/i', 'charset=utf-8"', $this->content);
		$this->content = iconv($chr[1], 'utf-8', $this->content);
		return $this;
	}
}