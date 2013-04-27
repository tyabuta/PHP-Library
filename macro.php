<?php

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
    $val = $_REQUEST[$q];
    return empty($val)? $def : $val;
}


/*
* $_POSTに値が設定されていない場合、デフォルト値を使用する。
*
*   $q: リクエストインデックス
* $def: デフォルト値(省略時は空文字列)
*/
function valpos($q, $def=""){
    $val = $_POST[$q];
    return empty($val)? $def : $val;
}

/*
* $_GETに値が設定されていない場合、デフォルト値を使用する。
*
*   $q: リクエストインデックス
* $def: デフォルト値(省略時は空文字列)
*/
function valget($q, $def=""){
    $val = $_GET[$q];
    return empty($val)? $def : $val;
}




