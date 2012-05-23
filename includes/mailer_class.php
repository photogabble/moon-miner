<?php

class Mailer
{
    private static $module			= "Blacknova Mailing System";
    private static $version			= "0.0.10 (0062) beta";
    private static $author			= "TheMightyDude";

    private $mailServers			= NULL;
    private $debugMessage			= NULL;
    private $debugMode				= false;
    private $domain					= NULL;
    private $readTimout				= 10;

    private $mailSender				= NULL;
    private $mailRecipient			= NULL;
    private $mailSubject			= NULL;
    private $mailMessage			= NULL;

    private $errorMessage			= NULL;
    private $errorID				= NULL;

    private $handle					= NULL;

    private $authenticate			= false;

    private $authenticating			= false;

    function __construct($type = NULL)
    {
        $this->authenticate			= false;
        $this->authenticating		= false;
        $this->errorMessage			= "";
        $this->debugMessage 		= array();
        $this->domain				= $_SERVER['SERVER_NAME'];
        $this->lastCommandSent		= NULL;
    }

    function __destruct()
    {
    }

    public function setDomain($domain = NULL)
    {
        if ( is_null($domain) )
        {
            return (boolean) false;
        }
        $this->domain = $domain;

        return (boolean) true;
    }

    public function setSender($name = NULL, $email = NULL)
    {
        if ( is_null($name) || is_null($email))
        {
            return (boolean) false;
        }
        $this->mailSender = array("name"=>$name, "email"=>$email);

        return (boolean) true;
    }

    public function setRecipient($name = NULL, $email = NULL)
    {
        if ( is_null($name) || is_null($email))
        {
            return (boolean) false;
        }
        $this->mailRecipient = array("name"=>$name, "email"=>$email);

        return (boolean) true;
    }

    public function setMailHost($mailHost = NULL)
    {
        if ( is_null($mailHost))
        {
            return (boolean) false;
        }
        $this->mailServers = (array) $mailHost;

        return (boolean) true;
    }

    public function setSubject($subject = NULL)
    {
        if ( is_null($subject) )
        {
            return (boolean) false;
        }
        $this->mailSubject = (string) $subject;

        return (boolean) true;
    }

    public function setMessage($message = NULL)
    {
        if ( is_null($message) )
        {
            return (boolean) false;
        }
        $this->mailMessage = (string) str_replace("\r\n.\r\n", "\r\n. \r\n", $message);

        return (boolean) true;
    }

    public function setDebugMode($switch = false)
    {
        $this->debugMode = (boolean) $switch;
    }

    public function Authenticate($auth = NULL)
    {
        if ( is_null($auth) || !is_array($auth) || !array_key_exists('username', $auth) || !array_key_exists('password', $auth) )
        {
            return (boolean) false;
        }

        $this->authenticate = array ( "username"=>base64_encode($auth['username']), "password"=>base64_encode($auth['password']) );
        if( !(base64_decode($this->authenticate['username']) == $auth['username']) || !(base64_decode($this->authenticate['password']) == $auth['password']) )
        {
            $this->authenticate = (boolean) false;

            return (boolean) false;
        }

        return (boolean) true;
    }

    private function sendData($reqData = NULL, &$retCode = NULL, $read = true)
    {
        if ($this->authenticating == false)
        {
            array_push( $this->debugMessage, " C: ". htmlentities(trim($reqData, "\r\n")) );
        }
        $this->lastCommandSent = $reqData;
        $ret = fwrite($this->handle, $reqData);
        if ($ret != false && $ret > 0)
        {
            if($read == true)
            {
                $data = $this->getData($retCode);
            }

            return $data;
        }

        return false;
    }

    private function getData(&$retCode = NULL, &$retline = NULL)
    {
        $data = array();

        $read   = array($this->handle);
        $write  = NULL;
        $except = NULL;

        if (false === ($num_changed_streams = stream_select($read, $write, $except, $this->readTimout)))
        {
            # we need to return false to state we got an error.
            # Error handling

            return (boolean) false;
        }
        elseif ($num_changed_streams > 0){} # Do nothing.
        else
        {
            return (boolean) false;
        }
        $line = fgets($this->handle);
        $line = trim($line, "\r\n");

        array_push($data, $line);
        if ($this->authenticating == false)
        {
            array_push( $this->debugMessage, " S: {$line}" );
        }
        while(substr($line, 3, 1) == '-')
        {
            $line = fgets($this->handle);
            if ($line === false)
            {
                break;
            }
            $line = trim($line, "\r\n");
            array_push($data, $line);
            if ($this->authenticating == false)
            {
                array_push( $this->debugMessage, " S: {$line}" );
            }
        }
        $retCode = substr($line, 0, 3);

        $this->templine = $line;

        return $data;
    }

    public function sendMail()
    {
        $this->handle = fsockopen("{$this->mailServers[0]}", 25, $errno, $errstr, 30);
        if (!$this->handle)
        {
            $this->errorMessage = "Unable to connect to MailServer.<br />Connection refused.";
            $this->errorID		= 1;

            return (boolean) false;
        }

        $this->readTimout = 5;

        $retData = $this->getData($retCode);
        if($retCode != 220)
        {
            // Need to handle this better
            $this->em = $retData;
            $this->errorMessage = "Failed Sending email.";
            $this->errorID		= 2;
            fclose($this->handle);
            $this->displayDebug();
            $this->sendLog();

            return (boolean) false;
        }

        $retData = $this->sendHello($retCode);
        if ($retCode != 250)
        {
            // Need to handle this better
            $this->em = $retData;
            $this->errorMessage = "Failed Sending email.";
            $this->errorID		= 3;

            $this->sendData("RSET\r\n", $retCode);
            $this->sendData("QUIT\r\n", $retCode);
            fclose($this->handle);
            $this->displayDebug();
            $this->sendLog();

            return (boolean) false;
        }

        if ($this->sendAuthentication() == false)
        {
            // Need to handle this better
            $this->em = $retData;
            $this->errorMessage = "Failed Sending email.";
            $this->errorID		= 4;

            $this->sendData("RSET\r\n", $retCode);
            $this->sendData("QUIT\r\n", $retCode);
            $this->enableEncryption(false);
            fclose($this->handle);
            $this->displayDebug();
            $this->sendLog();

            return (boolean) false;
        }

        array_push( $this->debugMessage, " #: We are Authed at this point." );

        $retData = $this->sendData("MAIL FROM: <{$this->mailSender['email']}>\r\n", $retCode);
        if ($retCode != 250)
        {
            // Need to handle this better
            $this->em = $retData;
            $this->errorMessage = "Failed Sending email.";
            $this->errorID		= 5;

            $this->sendData("RSET\r\n", $retCode);
            $this->sendData("QUIT\r\n", $retCode);
            $this->enableEncryption(false);
            fclose($this->handle);
            $this->displayDebug();
            $this->sendLog();

            return (boolean) false;
        }

        $retData = $this->sendData("RCPT TO: <{$this->mailRecipient['email']}>\r\n", $retCode);
        if ($retCode != 250 && $retCode != 251)
        {
            // Need to handle this better
            $this->em = $retData;
            $this->errorMessage = "Failed Sending email.";
            $this->errorID		= 6;

            $this->sendData("RSET\r\n", $retCode);
            $this->sendData("QUIT\r\n", $retCode);
            $this->enableEncryption(false);
            fclose($this->handle);
            $this->displayDebug();
            $this->sendLog();

            return (boolean) false;
        }

        $retData = $this->sendData("DATA\r\n", $retCode);
        if ($retCode != 354)
        {
            // Need to handle this better
            $this->em = $retData;
            $this->errorMessage = "Failed Sending email.";
            $this->errorID		= 7;

            $this->sendData("RSET\r\n", $retCode);
            $this->sendData("QUIT\r\n", $retCode);
            $this->enableEncryption(false);
            fclose($this->handle);
            $this->displayDebug();
            $this->sendLog();

            return (boolean) false;
        }
        # MessageID We need this to be random or else it will fail to send.
        $MsgID = strtoupper(hash("md5", mt_rand(0, mt_getrandmax())) .".". base_convert(($sec *100)+ $usec, 10, 16));

        $message = NULL;
        # Email Headers
        $message .= "From: \"{$this->mailSender['name']}\" <{$this->mailSender['email']}>\r\n";
        $message .= "To: \"{$this->mailRecipient['name']}\" <{$this->mailRecipient['email']}>\r\n";
        $message .= "Subject: {$this->mailSubject}\r\n";
        $message .= "Date: ". date("D, d M Y H:i:s O") ."\r\n";
        $message .= "Message-ID: <{$MsgID}@{$this->domain}>\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: text/plain;\r\n";
        $message .= "\tcharset=\"us-ascii\"\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n";
        $message .= "X-Mailer: ". self::$module ." ". self::$version ."\r\n";
        $message .= "X-Author: ". self::$author ."\r\n";
        $message .= "\r\n";

        # Email Body
        $message .= "{$this->mailMessage}\r\n";
        # Email Body END

        $message .= "\r\n.\r\n";
        $retData = $this->sendData($message, $retCode);

        if ($retCode != 250)
        {
            // Need to handle this better
            $this->em = $retData;
            $this->errorMessage = "Failed Sending email.";
            $this->errorID		= 8;

            $this->sendData("RSET\r\n", $retCode);
            $this->sendData("QUIT\r\n", $retCode);
            $this->enableEncryption(false);
            fclose($this->handle);
            $this->displayDebug();
            $this->sendLog();

            return (boolean) false;
        }

        array_push( $this->debugMessage, " #: The Email has been sent at this point." );

        $this->sendData("QUIT\r\n", $retCode);

        $this->enableEncryption(false);
        fclose($this->handle);
        $this->displayDebug();
        $this->sendLog();

        return (boolean) true;
    }

    private function sendAuthentication()
    {
        $this->authed = (boolean) false;
        $retData = $this->sendData("STARTTLS\r\n", $retCode);
        if ($retCode != 220)
        {
            // Need to handle this better
            array_push( $this->debugMessage, "STARTTLS Error" );
            $this->sendData("RSET\r\n", $retCode);
            $this->sendData("QUIT\r\n", $retCode);

            return (boolean) false;
        }
        $this->authenticating = (boolean) true;

        # Turn on TLS Encryption.
        if ($this->enableEncryption(true) != true)
        {
            // Need to handle this better
            return (boolean) false;
        }
        array_push( $this->debugMessage, " #: Stripping all Auth info." );

        $retData = $this->sendHello($retCode);
        if ($retCode != 250)
        {
            // Need to handle this better
            array_push( $this->debugMessage, "HELO Error" );
            $this->sendData("RSET\r\n", $retCode);
            $this->sendData("QUIT\r\n", $retCode);

            return (boolean) false;
        }

        $retData = $this->sendData("AUTH LOGIN\r\n", $retCode);
        if ($retCode != 334)
        {
            // Need to handle this better
            return (boolean) false;
        }

        for ($i=0; $i<2; $i++)
        {
            $respData = base64_decode(substr($retData[0], 4), true);
            switch($respData)
            {
                case "Username:":
                {
                    $retData = $this->sendData("{$this->authenticate['username']}\r\n", $retCode);
                    break;
                }

                case "Password:":
                {
                    $retData = $this->sendData("{$this->authenticate['password']}\r\n", $retCode);
                    break;
                }
                default:
                {
                    array_push( $this->debugMessage, " #: Detected unknown respons: '{$respData}'" );
                    break;
                }
            }
        }
        $this->authenticating = (boolean) false;
        array_push( $this->debugMessage, " S: ". implode($retData) );
        if ($retCode != 235)
        {
            return (boolean) false;
        }

        return (boolean) true;
    }

    private function sendHello(&$retCode = NULL)
    {
        $retData = $this->sendData("EHLO {$this->domain}\r\n", $retCode);
        if ($retCode != 250)
        {
            array_push( $this->debugMessage, " E: EHLO failed" );
            $retData = $this->sendData("HELO {$this->domain}\r\n", $retCode);
            if ($retCode != 250)
            {
                array_push( $this->debugMessage, " E: HELO failed" );
            }
        }

        return $retData;
    }

    private function enableEncryption($switch = false)
    {
        if ($switch == (boolean) true)
        {
            array_push( $this->debugMessage, " #: TURNING ON ENCRYPTION");

            return stream_socket_enable_crypto($this->handle, true, STREAM_CRYPTO_METHOD_TLS_CLIENT );
        }
        else
        {
            array_push( $this->debugMessage, " #: TURNING OFF ENCRYPTION");

            return stream_socket_enable_crypto($this->handle, false);
        }
    }

    public function displayDebug()
    {
        if ($this->debugMode == true)
        {
            echo "[DebugMessage]<br />\n";
            for($i=0; $i<count($this->debugMessage); $i++)
            {
                echo "{$this->debugMessage[$i]}<br />\n";
            }
        }
    }

    public function sendLog()
    {
        global $db;

        $debugMsg = addslashes(serialize($this->debugMessage));
        $debugErr = addslashes(serialize($this->errorMessage));
        $time = time();
        $ret = adminlog(3001, "{$debugMsg}|{$debugErr}|{$_SERVER['REMOTE_ADDR']}|{$time}");

        if ($ret == false)
        {
            adminlog(3002, "Err: {$db->ErrorNo()}: {$db->ErrorMsg()}|".time());
        }

    }

    public function getError()
    {
        return (array) array("msg"=>$this->errorMessage, "no"=>$this->errorID, "debug"=>$this->em);
    }

    public function getInfo()
    {
        return (string) self::$module ." ". self::$version ." written by ". self::$author;
    }
}

?>
