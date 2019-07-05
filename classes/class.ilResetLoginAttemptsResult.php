<?php

/**
 * Class ilResetLoginAttemptsResult
 *
 * @author Stephan Winiker <stephan.winiker@hslu.ch>
 */
class ilResetLoginAttemptsResult extends ilCronJobResult {

	/**
	 * @param      $status  int
	 * @param      $message string
	 * @param null $code    string
	 */
	public function __construct($status, $message) {
		$this->setStatus($status);
		$this->setMessage($message);
	}
}
?>