<?php
/**
 * Simplified PHPMailer-like implementation for SMTP email sending
 * This is a lightweight custom implementation to avoid external dependencies
 */

class SimpleMailer {
    private $host = '';
    private $port = 465;
    private $username = '';
    private $password = '';
    private $encryption = 'ssl';
    private $from = '';
    private $fromName = '';
    private $to = '';
    private $toName = '';
    private $subject = '';
    private $body = '';
    private $errorInfo = '';
    public $debug = '';

    public function setHost($host) {
        $this->host = $host;
    }

    public function setPort($port) {
        $this->port = $port;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setEncryption($encryption) {
        $this->encryption = strtolower($encryption);
    }

    public function setFrom($email, $name = '') {
        $this->from = $email;
        $this->fromName = $name;
    }

    public function addAddress($email, $name = '') {
        $this->to = $email;
        $this->toName = $name;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    private function log($msg) {
        $this->debug .= $msg . "\n";
    }

    public function send() {
        $socket = null;
        try {
            // Validate required fields
            if (empty($this->host) || empty($this->from) || empty($this->to) || empty($this->subject)) {
                $this->errorInfo = "Missing required email parameters";
                return false;
            }

            // Create socket connection
            $protocol = $this->encryption === 'ssl' ? 'ssl://' : '';
            $socket = @fsockopen($protocol . $this->host, $this->port, $errno, $errstr, 30);

            if (!$socket) {
                $this->errorInfo = "Could not connect to SMTP server: $errstr ($errno)";
                return false;
            }

            // Read server greeting
            $this->readResponse($socket);

            // EHLO
            fputs($socket, "EHLO " . $this->host . "\r\n");
            $this->readResponse($socket);

            // STARTTLS for TLS encryption
            if ($this->encryption === 'tls') {
                fputs($socket, "STARTTLS\r\n");
                $this->readResponse($socket);
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                fputs($socket, "EHLO " . $this->host . "\r\n");
                $this->readResponse($socket);
            }

            // AUTH LOGIN
            if (!empty($this->username) && !empty($this->password)) {
                fputs($socket, "AUTH LOGIN\r\n");
                $this->readResponse($socket);
                fputs($socket, base64_encode($this->username) . "\r\n");
                $this->readResponse($socket);
                fputs($socket, base64_encode($this->password) . "\r\n");
                $response = $this->readResponse($socket);
                
                // Check if authentication failed
                if (strpos($response, '535') !== false || strpos($response, '535') === 0) {
                    fclose($socket);
                    $this->errorInfo = "Authentication failed. Please check your SMTP credentials.";
                    return false;
                }
            }

            // MAIL FROM
            fputs($socket, "MAIL FROM: <" . $this->from . ">\r\n");
            $this->readResponse($socket);

            // RCPT TO
            fputs($socket, "RCPT TO: <" . $this->to . ">\r\n");
            $this->readResponse($socket);

            // DATA
            fputs($socket, "DATA\r\n");
            $this->readResponse($socket);

            // Compose email headers and body
            $fromDisplay = !empty($this->fromName) ? $this->fromName . " <" . $this->from . ">" : $this->from;
            $toDisplay = !empty($this->toName) ? $this->toName . " <" . $this->to . ">" : $this->to;
            
            $message = "From: " . $fromDisplay . "\r\n";
            $message .= "To: " . $toDisplay . "\r\n";
            $message .= "Subject: " . $this->subject . "\r\n";
            $message .= "MIME-Version: 1.0\r\n";
            $message .= "Content-Type: text/html; charset=UTF-8\r\n";
            $message .= "\r\n";
            $message .= $this->body . "\r\n";
            $message .= ".\r\n";

            fputs($socket, $message);
            $this->readResponse($socket);

            // QUIT
            fputs($socket, "QUIT\r\n");
            $this->readResponse($socket);

            fclose($socket);
            return true;

        } catch (Exception $e) {
            $this->errorInfo = "Email sending failed: " . $e->getMessage();
            return false;
        }
    }

    private function readResponse($socket) {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return $response;
    }

    public function getErrorInfo() {
        return $this->errorInfo;
    }
}
?>
