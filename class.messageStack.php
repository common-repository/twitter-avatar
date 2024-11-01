<?php
class messageStack {
		var $messages;

		function __construct() {
				$this->messages = array();
		}

		function addMessage($message, $type = 'error') {
				$this->messages[] = array('message' => $message,
																	'type' => $type );
		}
		
		function addSessionMessage($message, $type = 'error') {
				$_SESSION['bxpand_members_messages'][] = array('message' => $message,
																													'type' => $type );
		}
		
		function resetMessages() {
				$this->messages = array();
				if(isset($_SESSION['bxpand_members_messages'])) {
					foreach ($_SESSION['bxpand_members_messages'] as $message) {
						$this->messages[] = array('message' => $message['message'],
																			'type' => $message['type']);
					}
					unset($_SESSION['bxpand_members_messages']);
				}
		}
		
		function outputMessages() {
				$output = "";
				$error_message = "";
				$success_message = "";

				reset($this->messages);
				foreach($this->messages as $message) {
						if($message['type'] == 'error') {
								$error_message .= $message['message'] . "<br />\r\n";
						}
				}

				reset($this->messages);
				foreach($this->messages as $message) {
						if($message['type'] == 'updated') {
								$success_message .= $message['message'] . "<br />\r\n";
						}
				}
				
				if(strlen($error_message) > 0) {
						$output .= "<div id='message' class='error'><p>" . $error_message . "</p></div>\r\n";
				}
				
				if(strlen($success_message) > 0) {
						$output .= "<div id='message' class='updated'><p>" . $success_message . "</p></div>\r\n";
				}
				
				return $output;
		}

}
?>