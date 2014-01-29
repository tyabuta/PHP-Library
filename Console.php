<?php
/********************************************************************
                             Class Console
********************************************************************/
class Console
{
    /*---------------------------------------------------------------
     * DRYRUNモードならtrueを返す。
     * ※ オプションに --run が指定されている場合falseを返す
     --------------------------------------------------------------*/
    public static function isDryRunMode(){
        static $ret = null;
        if (is_null($ret)) {
            $opt = getopt('', array('run'));
            $ret = !isset($opt['run']);
        }
        return $ret;
    }


    /*---------------------------------------------------------------
     * 標準入力から整数値を受け取る
     * 無効な入力の場合default値を返す。
     --------------------------------------------------------------*/
    public static function getNumber($default=NULL){
        fscanf(STDIN, "%d\n", $number); // STDIN から数値を読み込む

        if (is_null($number)) return $default;
        return $number;
    }

    /*---------------------------------------------------------------
     * 標準入力から一行分の文字列を受け取る ※改行コードは含まれない
     * 無効な入力の場合default値を返す。
     --------------------------------------------------------------*/
    public static function getString($default='') {
        $str = trim(fgets(STDIN,4096));
        if (empty($str)) return $default;
        return $str;
    }

    /*---------------------------------------------------------------
     * メッセージ付きで標準入力から整数値を受け取る
     * 無効な入力の場合default値を返す。
     --------------------------------------------------------------*/
    public static function getNumberWithMessage($msg, $default=NULL){
        echo $msg . PHP_EOL;
        echo ">>> ";
        return self::getNumber($default);
    }

    /*---------------------------------------------------------------
     * メッセージ付きで標準入力から一行分の文字列を受け取る ※改行コードは含まれない
     * 無効な入力の場合default値を返す。
     --------------------------------------------------------------*/
    public static function getStringWithMessage($msg, $default=''){
        echo $msg . PHP_EOL;
        echo ">>> ";
        return self::getString($default);
    }


    /*---------------------------------------------------------------
     * 標準入力からboolean値を受け取る (yes or no)
     * どちらでもない場合は$default値をかえす。指定なしの場合はNULL
     --------------------------------------------------------------*/
    public static function getBoolean($default=NULL){
        $value = self::getString();
        if (preg_match("/^(y|yes)$/i", $value)) return true;
        if (preg_match("/^(n|no)$/i", $value))  return false;
        return $default;
    }

    /*---------------------------------------------------------------
     * メッセージ付きで標準入力からboolean値を受け取る (yes or no)
     * どちらでもない場合はNULLをかえす。
     --------------------------------------------------------------*/
    public static function getBooleanWithMessage($msg, $default=NULL){
        echo $msg . " [yes/no]" . PHP_EOL;
        echo ">>> ";
        return self::getBoolean($default);
    }

}// End of Class

