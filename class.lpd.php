<?php
class LPD
{
	protected $sock = null;
	protected $debg = false;
	protected $call = null;

	public function __construct($call = null, $addr = '127.0.0.1', $port = 515, $maxc = 5, $start = true, $debg = false) {
		$this->debg = $debg;
		$this->call = $call;

		if(($this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
			throw new Exception('socket_create() failed: reason: ' . socket_strerror(socket_last_error()));
		}
		if(socket_bind($this->sock, $addr, $port) === false) {
			throw new Exception('socket_bind() failed: reason: ' . socket_strerror(socket_last_error($sock)));
		}
		if(socket_listen($this->sock, $maxc) === false) {
			throw new Exception('socket_listen() failed: reason: ' . socket_strerror(socket_last_error($sock)));
		}
		if($start) {
			$this->start();
		}
	}
	public function __destruct() {
		@socket_close($this->sock);
	}
	public function start() {
		do {
			if(($msgsock = socket_accept($this->sock)) === false) {
				throw new Exception('socket_accept() failed: reason: ' . socket_strerror(socket_last_error($sock)));
			}
			$this->debug('New client');
			$this->read_command($msgsock);
		}
		while(true);
	}

	protected function read_command($msgsock, $receive_mode = false) {
		if(false === ($buff = socket_read($msgsock, 4096, PHP_NORMAL_READ))) {
			throw new Exception('socket_read() failed: reason: ' . socket_strerror(socket_last_error($msgsock)));
		}
		$command = ord($buff[0]);
		$arguments = preg_split('([\s]+)', substr($buff,1));
		$this->process_command($msgsock, $command, $arguments, $receive_mode);
	}
	protected function read_bytes($msgsock, $bytes) {
		$content = '';
		do {
			if(false === ($buff = socket_read($msgsock, 1024, PHP_BINARY_READ))) {
				throw new Exception('socket_read() failed: reason: ' . socket_strerror(socket_last_error($msgsock)));
			}
			$content .= $buff;
		} while(mb_strlen($content, '8bit') < $bytes && $buff != '');
		return mb_substr($content, 0, $bytes, '8bit');
	}
	protected function process_command($msgsock, $command, $arguments, $receive_mode) {
		$this->debug($command);
		switch($command) {
			case 1:
				socket_write($msgsock, chr(0));
				socket_close($msgsock);
				break;
			case 2:
				if(!$receive_mode) {
					$receive_mode = true;
					socket_write($msgsock, chr(0));
					$this->read_command($msgsock, $receive_mode);
				}
				else {
					socket_write($msgsock, chr(0));
					$this->read_bytes($msgsock, $arguments[0]);
					socket_write($msgsock, chr(0));
					$this->read_command($msgsock, $receive_mode);
				}
				break;
			case 3:
				if(!$receive_mode) {
					socket_write($msgsock, chr(0));
					$this->read_command($msgsock, $receive_mode);
				}
				else {
					socket_write($msgsock, chr(0));
					$data = $this->read_bytes($msgsock, $arguments[0]);
					socket_write($msgsock, chr(0));
					socket_close($msgsock);
					$this->process_data($data);
				}
				break;
			default:
				socket_write($msgsock, chr(0));
				break;
		}
	}
	protected function debug($msg) {
		if($this->debg) {
			echo $msg . "\r\n";
		}
	}
	protected function process_data($data) {
		if($this->call && is_callable($this->call)) {
			call_user_func($this->call, $data);
		}
	}
}