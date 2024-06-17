<?php
include('errorMsgs.php');		                            // エラーメッセージ用のPHPファイルの読み込み

$response =[
    
    "result" => "",                                         // 実行結果を格納する(success or error)
    "errCode" => null,                                      // エラーコードがある場合格納する
    "errMsg" => null,                                       // エラーメッセージがある場合格納する

];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {                // HTTPメソッドがPOST形式で送られてきたか確認。
	
    $postData = json_decode(file_get_contents('php://input'), true);

}

// Inputパラメータの必須チェックを行う
if(!isset($postData["userId"]) || $postData["userId"] == ""){
    $response = setError($response,"006");                  // 【エラーコード】ユーザID：006
}

if(!isset($postData["followUserId"]) || $postData["followUserId"] == ""){
    $response = setError($response,"012");                  // 【エラーコード】ユーザID：012
}

if(!isset($postData["followFlg"]) || !is_bool($postData["followFlg"])){
    $response = setError($response,"013");                  // 【エラーコード】ユーザID：013
}

// エラーがなければ
if($response["errCode"] == null){

    $userId = $postData["userId"];
    $followUserId = $postData["followUserId"];
    $followFlg = $postData["followFlg"];

    include('mysqlConnect.php');                            // DB接続処理を呼び出し

    // フォローフラグがtrue(フォローする)の場合
    if($followFlg == true){
        $sql = "INSERT INTO follow(userId,followUserId) VALUES(:userId,:followUserId)";
    }

    // フォローフラグがfalse(フォロー外す)の場合
    if($followFlg == false){
        $sql = "DELETE FROM follow WHERE userId = :userId AND followUserId = :followUserId";
    }

    $stmt = $pdo->prepare($sql);
    // データのバインド
    $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':followUserId',$followUserId,PDO::PARAM_STR);

    if($stmt->execute()){
		// 成功の処理
		$response = array('success' => true);
	}else{
		$response['errCode'] = '001';
	}

    $pdo ->commit();

	$response = array('success' => true);                  // 返却値の連想配列に成功パラメータをセット

    $stmt = null;                                          // SQL情報をクローズ

    include('mysqlClose.php');
}															
header('Content-Type: application/json'); // JSON形式でレスポンスを送信するよう指定
echo json_encode($response, JSON_UNESCAPED_UNICODE); // $responseのデータをJSON形式に加工して出力
?>