<?php
// タイムライン取得API

include('errorMsgs.php');		// メッセージ用のPHPファイルの読み込み

$response =[
    "result" => "",         // 実行結果を格納する(success or error)
    "errCode" => null,      // エラーコードがある場合格納する
    "errMsg" => null,       //エラーメッセージがある場合格納する
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // HTTPメソッドがPOST形式で送られてきたか確認。
	$postData = json_decode(file_get_contents('php://input'), true);
}

$userId = $postData["userId"];
									
if(!isset($postData['userId'])){                // Inputパラメータの必須チェック
    $responce = setError($responce,006);        // パラメータが無い場合、【エラーコード】ユーザID:006をセットしてエラー終了させる。    
}else{									
    include('mysqlConnect.php');                // DB接続処理を呼び出し、データベースの接続を行う。
}

// ささやきリストの内容を取得するSQL文を実行する
$sql = "";
 
/*
4.データのフェッチを行い、検索結果のデータがある間以下の処理を繰り返す。									
	４－１．イイねフラグがnull以外の場合はtrueを、それ以外の場合はfalseをセットする。	
	４－２．ささやきリストの連想配列にデータを追加する。								
*/

// 5.SQL情報をクローズさせる。									
$stmt = null;
// 6.返却値の連想配列に成功パラメータとささやきリスト連想配列のデータを格納する									
									
// 7.DB切断処理を呼び出し、データベースの接続を解除する。									
include('mysqlClose.php');		// メッセージ用のPHPファイルの読み込み
// 8.返却値の連想配列をJSONにエンコードしてoutputパラメータを出力する。
header('Content-Type: application/json'); // JSON形式でレスポンスを送信するよう指定
echo json_encode($response, JSON_UNESCAPED_UNICODE); // $responseのデータをJSON形式に加工して出力									
?>