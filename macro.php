<?php
/********************************************************************

                             PHP-Library

                                                  (c) 2013 tyabuta.
********************************************************************/



/*-------------------------------------------------------------------
* 配列から１つの要素キーを抽選する。
*
* $entries: array($key => $weight, ...)
*           配列の値に抽選の割合(重み)を整数値で指定。
-------------------------------------------------------------------*/
function array_rand_weighted($entries){
    $sum  = array_sum($entries);
    $rand = rand(1, $sum);

    foreach($entries as $key => $weight){
        if (($sum -= $weight) < $rand) return $key;
    }
}

/*-------------------------------------------------------------------
 * 指定時刻があと何時何分か'##:##'の書式文字列で取得する。
 * $time_expr: '13:15' のような時刻文字列を指定する。
-------------------------------------------------------------------*/
function get_time_left($time_expr){
 
    $now    = time();
    $target = date("Y-m-d {$time_expr}:00", $now);
 
    // 既に過ぎた時間であれば、翌日の日付にする。
    if (strtotime($target) < $now){
        $tommorow = date('d', $now) + 1;
        $target   = date("Y-m-{$tommorow} {$time_expr}:00");
    }
 
    $diff         = strtotime($target) - $now;
    $diff_hour    = intval($diff / 3600);
    $diff_minutes = intval($diff / 60) % 60;
 
    return sprintf("%02d:%02d", $diff_hour, $diff_minutes);
}


/*-------------------------------------------------------------------
 * Path結合をおこなう関数
 -------------------------------------------------------------------*/
function URI_buildPath($arr, $sepa = '/'){

    $path = '';
    foreach ($arr as $i => $a){
        if (0 == $i) {
            $path .= rtrim($a, '\\/') . $sepa;
        }
        else {
            $path .= trim($a, '\\/') . $sepa;
        }
    }
    return rtrim($path, '\\/');
}


/*
* bool値を文字列へ変換する。
*/
function strbool($bValue){
    return ($bValue? 'true':'false');
}

/*
* ファイルへのログ出力関数
* 省略時は$_SERVER['PHP_SELF']で取得したファイル名に
* 拡張子 ".log" を付けたものになります。
*/
function outlog($msg, $logname=""){
    global $_logger_path;
    if (false == isset($_logger_path)){
        if (empty($logname)){
            $logname = basename($_SERVER['PHP_SELF']) . ".log";
        }
        $_logger_path = $logname;
    }
    $strDate = date("Y/m/d H:i:s");
    error_log("[{$strDate}] {$msg}\n", 3, $_logger_path);
}


/*
* リクエストのあったファイル自身名を取得する。
*/
function selfname(){
    return basename($_SERVER['PHP_SELF']);
}


/*
* POSTリクエストを送信し、コンテンツを取得する。
* データ取得に失敗した場合は、NULLが返る。
*
*    $url: リクエスト先のURL
* $params: リクエスト用のパラメータ値を連想配列で渡す。
*          例) array("q"=>"something")
*/
function HTTPPost($url, $params){
    $options = array('http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded; charset=UTF-8',
                'content' => http_build_query($params)));
    return @file_get_contents($url, false, stream_context_create($options));
}



/*
* JSON出力関数
* HTTPヘッダも含めて、レンダリングする。
* data: ハッシュ、または配列によるデータ表現を渡す。
*/
function JSONRenderWithData($data) {
    // JSON作成
    $json_value = json_encode($data);
    // JSONヘッダ
    header("Content-Type: application/json; charset=UTF-8");
    // JSON出力
    print $json_value . "\n";
}


/*
* 赤いボタン用のCSSを出力する。
*/
function CSSRenderRedButton(){
    print <<<"EOF"
.RedButton {
    padding: 0.5em;
    display: inline-block;
    font-size: 14px;
    color: #fff;
    font-weight: bold;
    letter-spacing: 0.1em;
    background-image: -webkit-linear-gradient(top, #be0000, #990000);
    background-image: -moz-linear-gradient(top, #be0000, #990000);
    background-image: -o-linear-gradient(top, #be0000, #990000);
    background-image: -linear-gradient(to bottom, #be0000, #990000);
    border-radius: 10px;
    box-shadow: 0px 5px 2px #333, 0px 0px 3px #e5acac inset;
    text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
    border: 2px solid #990000;
}
EOF;
}


/* ------------------------------------------------------------------
* セッション関係
-------------------------------------------------------------------*/

/*
* セッションデータをCookieも含めて全て削除する。
* セッションを再開するには session_start() をコールする必要があります。
 */
function SessionRemove(){
    if (isset($_SESSION) || session_start()){
        // スーパーグローバル変数の初期化
        $_SESSION = array();
        // Cookie削除
        if (ini_get("session.use_cookies")){
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]);
        }
    }
    // セッション登録データ削除
    $ret = session_destroy();
    return $ret;
}


/*
* セッション変数の簡易関数
* キーのみを指定した場合、ゲッターとしてセッション変数を取得する。
* もし、セッション変数が未定義の場合は空文字列を返します。
* valueを指定すると、セッション変数に値を代入し戻り値として返します。
* session_start()関数が呼ばれていない場合、
* この関数はNULLを返し何もしません。
*/
function S($key, $value=NULL){
    // スーパーグローバル変数がない場合はNULLを返す。
    if (false == isset($_SESSION)) return NULL;

    // NULLならゲッターとして動作する。
    if (NULL == $value){
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        // キーがないなら、空文字列を返す。
        return "";
    }
    else {
        return $_SESSION[$key] = $value;
    }
}




/* ------------------------------------------------------------------
* リクエスト解析
-------------------------------------------------------------------*/

// string valreq(string $q, mixed $def="")
// string valpos(string $q, mixed $def="")
// string valget(string $q, mixed $def="")
// string boolRequestWithParamName(string $q, bool $def=false)
// string paramWithCmdLine(string $q, int $i, mixed $def="")


/*
* $_REQUESTに値が設定されていない場合、デフォルト値を使用する。
*
*   $q: リクエストインデックス
* $def: デフォルト値(省略時は空文字列)
*/
function valreq($q, $def=""){
    if (!isset($_REQUEST[$q]) || empty($_REQUEST[$q])) return $def;
    return $_REQUEST[$q];
}


/*
* $_POSTに値が設定されていない場合、デフォルト値を使用する。
*
*   $q: リクエストインデックス
* $def: デフォルト値(省略時は空文字列)
*/
function valpos($q, $def=""){
    if (!isset($_POST[$q]) || empty($_POST[$q])) return $def;
    return $_POST[$q];
}

/*
* $_GETに値が設定されていない場合、デフォルト値を使用する。
*
*   $q: リクエストインデックス
* $def: デフォルト値(省略時は空文字列)
*/
function valget($q, $def=""){
    if (!isset($_GET[$q]) || empty($_GET[$q])) return $def;
    return $_GET[$q];
}


/*
* $_REQUEST からBool型の値を取得する。
* 値が設定されていない場合、デフォルト値を使用する。
* yes/no y/n true/false 1/0の指定が可能
*
*   $q: リクエストインデックス
* $def: デフォルト値(省略時はfalse)
*/
function boolRequestWithParamName($q, $def=false){
    if (!empty($_REQUEST)){
        if (isset($_REQUEST[$q]) && !empty($_REQUEST[$q])){
            $val = $_REQUEST[$q];
            if (preg_match('/^(y|yes|true)$/i', $val)){
                return true;
            }
            if (preg_match('/^(n|no|false)$/i', $val)){
                return false;
            }

            return (0 != $val);
        }
    }
    return $def;
}


/*
* $_REQUESTに値が設定されていない場合、
* コマンドライン引数から値を取得する。
* どちらも値が設定されていないなら、$defを使用する。
*
*   $q: リクエストインデックス
*   $i: コマンドライン引数インデックス
* $def: デフォルト値(省略時は空文字列)
*
*/
function paramWithCmdLine($q, $i, $def=""){
    if (!empty($_REQUEST[$q])){
        return $_REQUEST[$q];
    }

    global $argv;
    if ($argv[$i]){
        return $argv[$i];
    }

    return $def;
}



/* ------------------------------------------------------------------
* サニタイズ関係
-------------------------------------------------------------------*/
// void SanitizeShellArgument($arg)
// void SanitizeHtml($val)
// void SanitizeSQLInt($val)
// void SanitizeSQLDouble($val)
// void SanitizeSQLString($val)



// OSコマンドインジェクション対策
// ==============================

/*
* シェルコマンドに渡す引数文字列をサニタイズする。
* 文字列はシングルクォートで囲まれ、特殊文字はエスケープされます。
*/
function SanitizeShellArgument($arg){
    return escapeshellarg($arg);
}


// Scriptインジェクション対策
// ==========================

/*
* HTML出力用のサニタイズ関数
* HTML出力する際はサニタイズしておく。
*/
function SanitizeHtml($val){
    return htmlspecialchars($val, ENT_QUOTES);
}


// SQLインジェクション対策
// =======================

/*
 * SQLインジェクションを防止する為、無効な文字列を排除する。
 * プリペアドステートメントを使う場合は必要なし。
 */
function SanitizeSQLInt($val){
    return intval($val);
}

/*
 * SQLインジェクションを防止する為、無効な文字列を排除する。
 * プリペアドステートメントを使う場合は必要なし。
 */
function SanitizeSQLDouble($val){
    return doubleval($val);
}

/*
 * SQLインジェクションを防止する為、特殊文字をエスケープする。
 * プリペアドステートメントを使う場合は必要なし。
 */
function SanitizeSQLString($val){
    return addslashes($val);
}




/* ------------------------------------------------------------------
* HTMLRender
-------------------------------------------------------------------*/
// void HTMLRenderBadRequest()
// void HTMLRenderForbidden()
// void HTMLRenderMeta()
// void HTMLRenderHtml5js()
// void HTMLRenderjQuery()


/*
* 400 Bad Request 用の出力関数
* リクエストに不正があった場合に使う。
*/
function HTMLRenderBadRequest() {
    header('HTTP', true, 400);
    print "<h1>400 Bad Request</h1>\n";
}

/*
* 403 FrobiddenBad 用の出力関数
* 許可出来ない場合に使う。
*/
function HTMLRenderForbidden() {
    header('HTTP', true, 403);
    print "<h1>403 Forbidden</h1>\n";
}


/*
* HTMLファイルのメタデータを出力する。
*/
function HTMLRenderMeta(){
    print <<<"EOF"
<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="user-scalable=0,width=device-width,initial-scale=1.0,maximum-scale=1.0"/>
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="default"/>
EOF;
}

/*
* IE8以前でHTML5タグを認識させる為の記述
*/
function HTMLRenderHtml5js(){
    print <<<"EOF"
<!-- IE8以前でHTML5タグを認識させる -->
<!--[if lte IE 8]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
EOF;
}

/*
* jQuery mobileの導入
*/
function HTMLRenderjQuery(){
    print <<<"EOF"
<!-- jQuery mobile -->
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.css" />
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.js"></script>
EOF;
}



