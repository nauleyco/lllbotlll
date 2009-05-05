<?php
class mybot
{
    private $_data;
    private $_irc;
    private $_conn;
    
    /**
     * IRCデータをセット
     * @param $irc
     * @param $data
     * @return unknown_type
     */
    private function _setData(&$irc, &$data) {
        $this->_data = &$data;
        $this->_irc = &$irc;
    }
    
    /**
     * bot処理
     * @param $irc
     * @param $data
     * @return unknown_type
     */
    function bot (&$irc, &$data) {
        $this->_setData($irc, $data);
        // 送信されたメッセージから、botパターンとパターンへのパラメータを取得
        $str = preg_replace('/^' . BOT_NICKNAME . ':/', '', $data->message);
        $str = str_replace('　', ' ', $str);
        $str = trim($str);
        $params = explode(' ', $str);
        if (count($params) < 1) {
            $this->_message('どのパターンかわからんかった');
            return;
        }
        // bot処理パターンをセット
        $pattern = $params[0];
        array_shift($params);
        // bot処理へのパラメータをセット
        $param = implode(' ', $params);
        // DB接続
        if (!$this->_connect()) {
            return;
        }
        // 処理内容取得
        $sql = "SELECT phpcode FROM " . DB_BOT_TABLE_NAME . " " . 
                "WHERE pattern='" . mysql_real_escape_string($pattern) . "'" . 
                "  AND deleted=0 " . 
                "  AND timerflg=0";
        if (!$result = mysql_query($sql, $this->_conn)) {
            $this->_disconnect();
            $this->_message('DBエラーった:mysql_query');
            return;
        }
        if (!$count = mysql_num_rows($result)) {
            $this->_disconnect();
            $message = (strlen($pattern)) ? 
                '「' . $pattern . '」ないづら。' : 'パターン入れてちょ☆';
            $this->_message($message);
            return;
        }
        if (!$row = mysql_fetch_object($result)) {
            $this->_disconnect();
            $this->_message('DBエラーった:mysql_fetch_object');
            return;
        }
        // bot処理実行
        try {
            eval($row->phpcode);
        } catch (Exception $e) {
            $this->_disconnect();
            $this->_message('phpcodeからのエラー:' . $e->getMessage());
            return;
        }
        // DB切断
        $this->_disconnect();
    }
    
    /**
     * メッセージを送信する。
     * @param $message
     * @param $type
     * @return unknown_type
     */
    private function _message($message, $type=null) {
        if ($type === null) {
            $type = ($this->_data->type == SMARTIRC_TYPE_QUERY) ? 
                SMARTIRC_TYPE_QUERY : SMARTIRC_TYPE_NOTICE;
        }
        if ($type == SMARTIRC_TYPE_QUERY) {
            $to = $this->_data->nick;
        } else {
            $to = $this->_data->channel;
        }
        foreach (explode("\n", $message) as $value) {
            if (strlen($value) == 0) {
                continue;
            }
            $this->_irc->message($type, $to, $value);
        }
    }
    
    /**
     * DB接続
     * @return boolean
     */
    private function _connect() {
        $conn = mysql_connect(DB_SERVER, DB_USER, DB_PASS);
        if (!$conn) {
            $this->_message('DB接続できんかった');
            return false;
        }
        $this->_conn = $conn;
        if (!mysql_select_db(DB_NAME, $conn)) {
            $this->_disconnect();
            $this->_message('DBなかった');
            return false;
        }
        return true;
    }
    
    /**
     * DB切断
     * @return boolean
     */
    private function _disconnect() {
        if (!mysql_close($this->_conn)) {
            $this->_message('DBエラーった:mysql_close');
            return false;
        }
        return true;
    }

}
?>