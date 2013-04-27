<?php

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
    if (isset($_REQUEST[$q]) && "" != $_REQUEST[$q]){
        return $_REQUEST[$q];
    }

    global $argv;
    if ($argv[$i]){
        return $argv[$i];
    }
   
    return $def;    
}

