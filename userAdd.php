<?php
// ユーザー登録
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
$userName = $postData["userName"];
$password = $postData["password"];

/*
１．Inputパラメータの必須チェックを行う。
パラメータが無い場合、対象エラーメッセージをセットしてエラー終了させる。									
【エラーコード】ユーザID：006、ユーザ名：011、パスワード：007
*/
if (!isset($postData['userId'])) {
	$response = setError($response, "006");

}else if (!isset($postData['userName'])) {
	$response = setError($response, "011");

}else if (!isset($postData['password'])) {
	$response = setError($response, "007");

}else{
//DB接続処理を呼び出し、データベースの接続を行う。
include('mysqlConnect.php');

}

/*
	ユーザデータを挿入するSQL文を実行する。									
	登録テーブル：ユーザ情報								
	登録データ：inputパラメータのユーザID、ユーザ名、パスワード					
*/
$sql = "INSERT INTO user (userId,userName,password) VALUES(:userId,:userName,:password)";
$stmt = $pdo->prepare($sql);

// データのバインド
$stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
$stmt->bindParam(':userName',$userName,PDO::PARAM_STR);
$stmt->bindParam(':password',$password,PDO::PARAM_STR);

/*４．SQL文の実行結果を受取り、異常終了なら以下の処理を行う。									
４－１．データベースのロールバック命令を実行する。																
４－２．対象エラーメッセージをセットしてエラー終了させる。								
【エラーコード】001								
*/
if($stmt->execute()){
	// 成功の処理
	$response = array('success' => true);
}else{
	$response['errCode'] = '001';
}
// データベースのコミット命令を実行する。
$pdo ->commit();

// 返却値の連想配列に成功パラメータをセットする。
$response = array('success' => true);

// SQL情報をクローズさせる。									
$stmt = null;

// DB切断処理を呼び出し、データベースの接続を解除する。
include('mysqlClose.php');

// 返却値の連想配列をJSONにエンコードしてoutputパラメータを出力する。								
// レスポンスの送信(API⇒APIを呼び出したシステムへ)
header('Content-Type: application/json'); // JSON形式でレスポンスを送信するよう指定
echo json_encode($response, JSON_UNESCAPED_UNICODE); // $responseのデータをJSON形式に加工して出力
?>