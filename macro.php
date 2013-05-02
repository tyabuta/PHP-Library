<?php


/*
* ファイルへのログ出力関数
* 省略時は$_SERVER['PHP_SELF']で取得したファイル名に
* 拡張子 ".log" を付けたものになります。
*/
function outlog($msg, $logname){
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
 * SQLインジェクションを防止する為、無効な文字列を排除する。
 * プリペアドステートメントを使う場合は必要なし。
 */
function SQLSanitizeForInt($val){
    return intval($val);
}

/*
 * SQLインジェクションを防止する為、無効な文字列を排除する。
 * プリペアドステートメントを使う場合は必要なし。
 */
function SQLSanitizeForDouble($val){
    return doubleval($val);
}

/*
 * SQLインジェクションを防止する為、特殊文字をエスケープする。
 * プリペアドステートメントを使う場合は必要なし。
 */
function SQLSanitizeForString($val){
    return addslashes($val);
}





/*
* HTML出力用のサニタイズ関数
* HTML出力する際はサニタイズしておく。
*/
function HTMLSanitize($val){
    return htmlspecialchars($val, ENT_QUOTES);
}

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




