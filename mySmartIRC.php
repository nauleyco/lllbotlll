<?php
class mySmartIRC extends Net_SmartIRC
{

    private $_irc_encoding;
    private $_source_encoding;
    private $_converter;

    function init($irc_encoding, $source_encoding) {

        $this->_irc_encoding = $irc_encoding;
        $this->_source_encoding = $source_encoding;
        if (strtolower($irc_encoding) == strtolower($source_encoding)) {
            $this->_converter = false;
        } else {
            $this->_converter = true;
        }
    }

    function _rawsend($data) {

        $data = $this->_convertEncoding($data);
        return parent::_rawsend($data);
    }

    function _handleactionhandler(&$ircdata) {
        
        $ircdata->message = $this->_convertDecoding($ircdata->message);
        parent::_handleactionhandler($ircdata);
    }

    private function _convertEncoding($string) {

        if ($this->_converter) {
            return mb_convert_encoding(
            $string, $this->_irc_encoding, $this->_source_encoding);
        }
        return $string;
    }

    private function _convertDecoding($string) {

        if ($this->_converter) {
            return mb_convert_encoding(
            $string, $this->_source_encoding, $this->_irc_encoding);
        }
        return $string;
    }
    
}
?>