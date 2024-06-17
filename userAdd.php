<?php
// ユーザー登録
include('errorMsgs.php');        // メッセージ用のPHPファイルの読み込み

$response = [
	"result" => "",         // 実行結果を格納する(success or error)
	"errCode" => null,      // エラーコードがある場合格納する
	"errMsg" => null,       //エラーメッセージがある場合格納する	
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // HTTPメソッドがPOST形式で送られてきたか確認。
	$postData = json_decode(file_get_contents('php://input'), true);
}


// Inputパラメータの必須チェックを行う。
if (!isset($postData['userId']) || $postData['userId'] == "") {
	$response = setError($response, "006");
}

if (!isset($postData['userName']) || $postData['userName'] == "") {
	$response = setError($response, "011");
}

if (!isset($postData['password']) || $postData['password'] == "") {
	$response = setError($response, "007");
}

// errCodeに何も入っていない場合(エラーではない場合) - 泉
if ($response["errCode"] == null) {

	$userId = $postData["userId"];
	$userName = $postData["userName"];
	$password = $postData["password"];

	//DB接続処理を呼び出し、データベースの接続を行う。
	include('mysqlConnect.php');


	//トランザクションの開始　-　泉
	$pdo->beginTransaction();

	$sql = "INSERT INTO user (userId,userName,password) VALUES(:userId,:userName,:password)";
	$stmt = $pdo->prepare($sql);

	// データのバインド
	$stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
	$stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
	$stmt->bindParam(':password', $password, PDO::PARAM_STR);

	if ($stmt->execute()) {
		// 成功の処理
		$response['result'] = "success";        //泉
		//$response = array('success' => true);

		// データベースのコミット命令を実行する。
		$pdo->commit();
	} else {
		//対象エラーメッセージをセット　-　泉
		$response = setError($response, "001");
		//$response['errCode'] = '001';

		//エラーならばロールバック　-　泉
		$pdo->rollBack();
	}

	// 返却値の連想配列に成功パラメータをセットする。
	//↓これelseを過ぎた後にやってはいけないと思う。
	//$response = array('success' => true);
}

// SQL情報をクローズさせる。									
$stmt = null;

// DB切断処理を呼び出し、データベースの接続を解除する。
include('mysqlClose.php');

// 返却値の連想配列をJSONにエンコードしてoutputパラメータを出力する。								
// レスポンスの送信(API⇒APIを呼び出したシステムへ)
header('Content-Type: application/json'); // JSON形式でレスポンスを送信するよう指定
echo json_encode($response, JSON_UNESCAPED_UNICODE); // $responseのデータをJSON形式に加工して出力
