<?php


/**
 * Stellt Funktionen zum versenden von E-Mails zur verfügung.
 * @author Christian Spitschka
 * @package 
 */
class email {

	private $recipient;
	private $subject;
	private $text;
	private $replyTo;
	private $cc;
	private $lesebestaetigung;

	public function __construct($recipient,$subject,$message) {
		include_once('../framework/lib/email/phpmailer/class.phpmailer.php');
		include_once('../framework/lib/email/phpmailer/class.smtp.php');
		
		$this->recipient = $recipient;
		$this->subject = $subject;
		$this->text = $message;
		$this->lesebestaetigung = false;
		
	}
	
	public function setReplyTo($rt) {
		$this->replyTo = $rt;
	}
	

	public function setCC($cc) {
		$this->cc = $cc;
	}
	
	public function getCC() {
		return $this->cc;
	}

	/**
	 * Sendet die Mail (über den CRAWLER für Massenversand)
	 */
	public function send() {
		DB::getDB()->query("INSERT INTO mail_send (mailRecipient, mailSubject, mailText, mailCrawler, replyTo, mailCC, mailLesebestaetigung) values('" . DB::getDB()->escapeString($this->recipient) . "','" . addslashes($this->subject) . "','" . DB::getDB()->escapeString($this->text) . "', 1, '" . $this->replyTo . "','" . $this->cc . "','" . ($this->lesebestaetigung ? 1 : 0) . "')");
	}
	
	
	/**
	 * Sendset sofort eine Mail. (z.B. für Passwort Mails)
	 */
	public function sendInstantMail() {
		DB::getDB()->query("INSERT INTO mail_send (mailRecipient, mailSubject, mailText, mailCrawler, replyTo, mailCC, mailLesebestaetigung) values('" .DB::getDB()->escapeString( $this->recipient) . "','" . addslashes($this->subject) . "','" . DB::getDB()->escapeString($this->text) . "', 0, '" . $this->replyTo . "','" . $this->cc . "','" . ($this->lesebestaetigung ? 1 : 0) . "')");
		self::sendMailWithID(DB::getDB()->insert_id());	// direkt versenden
	}
	
	/*
	 * Versendet 20 Mails aus dem Stapel der Mails
	 */
	public static function sendBatchMails() {
		
		$mails = DB::getDB()->query("SELECT mailID FROM mail_send WHERE mailSent=0 AND mailCrawler=1 LIMIT 40");
		
		$noError = true;
		
		$count = 0;
		
		while($m = DB::getDB()->fetch_array($mails)) {
			try {
				self::sendMailWithID($m['mailID']);
				$count++;
			}
			catch(Exception $e) {
				$noError = false;
			}
			catch(phpmailerException $e) {
				$noError = false;
			}
		}
		
		if($noError) return $count;
		else {
			return -1;
		}
	}
	
	private static function sendMailWithID($id) {
		
		if(DB::isDebug()) {
			$m = DB::getDB()->query_first("SELECT * FROM mail_send WHERE mailID='" . $id . "'");
				
			DB::getDB()->query("UPDATE mail_send SET mailSent=UNIX_TIMESTAMP() WHERE mailID='" . $m['mailID'] . "'");
		}
		else {
		
				$m = DB::getDB()->query_first("SELECT * FROM mail_send WHERE mailID='" . $id . "'");
			
				$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
	
				$mail->IsSMTP(); // telling the class to use SMTP
	
			  $mail->Host       = DB::getGlobalSettings()->smtpSettings['host'];
			  $mail->SMTPAuth   = true;
			  $mail->Port       = 25;
			  $mail->Username   = DB::getGlobalSettings()->smtpSettings['username']; 
			  $mail->Password   = DB::getGlobalSettings()->smtpSettings['password'];
			  
			  $mail->CharSet = 'UTF-8';
			  
			  $mail->AddAddress($m['mailRecipient']);
			  
	
			  $mail->SetFrom(DB::getGlobalSettings()->smtpSettings['sender'], DB::getGlobalSettings()->schoolName);
			  
			  if($m['mailCC'] != "") {
			  	$adresses = explode(";",$m['mailCC']);
			  	for($i = 0; $i < sizeof($adresses); $i++) {
			  		$mail->AddCC($adresses[$i]);
			  	}
			  }
			  
			  $mail->Subject = $m['mailSubject'];
			  
			  $mail->Body = $m['mailText'];
			  
			  if($m['replyTo'] != "") {
			  	$mail->AddReplyTo($m['replyTo']);
			  }
			
			  if($m['mailLesebestaetigung'] > 0) {
			  	$mail->addCustomHeader("Disposition-Notification-To: " . DB::getGlobalSettings()->smtpSettings['sender']);
			  	$mail->AddCustomHeader("X-Confirm-Reading-To: " . DB::getGlobalSettings()->smtpSettings['sender']);
			  	$mail->AddCustomHeader("Return-receipt-to: " . DB::getGlobalSettings()->smtpSettings['sender']);
			  }
	
			  if($mail->Send()) {
			  	DB::getDB()->query("UPDATE mail_send SET mailSent=UNIX_TIMESTAMP() WHERE mailID='" . $m['mailID'] . "'");
			  }
			  
		}
		
	}
	
	public function setLesebestaetigung() {
		$this->lesebestaetigung = true;
	}
	
}

?>