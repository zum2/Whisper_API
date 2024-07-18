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

// Inputパラメータの必須チェックを行う。
if(!isset($postData['userId']) || $postData['userId'] == ""){
    $response = setError($response,"006");                  // 【エラーコード】ユーザID：006

}

if(!isset($postData['userName']) || $postData['userName'] == ""){
    $response = setError($response,"002");                  // 【エラーコード】ユーザID：002

}

if(!isset($postData['password']) || $postData['password'] == ""){
    $response = setError($response,"002");                  // 【エラーコード】ユーザID：002

}

if(!isset($postData['profile']) || $postData['profile'] == ""){
    $response = setError($response,"002");                  // 【エラーコード】ユーザID：002

}

if($response["errCode"] == null){
    $userId = $postData["userId"];
    $userName = $postData["userName"];
    $password = $postData["password"];
    $profile = $postData["profile"];

    try{
    include('mysqlConnect.php');                            // DB接続処理を呼び出し

    $pdo ->beginTransaction();
    // ユーザデータを更新するSQL文
    $sql = "UPDATE user SET userName = :userName, password = :password, profile = :profile
            WHERE userId = :userId";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userName', $userName, PDO::PARAM_STR);
    $stmt->bindParam(':password',$password,PDO::PARAM_STR);
    $stmt->bindParam(':profile',$profile,PDO::PARAM_STR);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);

    
        // 成功の処理
        $response['result'] = "success";
        $pdo->commit(); 
        // データベースのコミット命令を実行する。
    } catch(Exception $e){
        $pdo->rollBack();                              // データベースのロールバック命令を実行する。
        $response = setError($response,"001");         //【エラーコード】001
    }

    $stmt = null;                                      // SQL情報をクローズさせる。
    include('mysqlClose.php');                         // DB切断処理を呼び出し、データベースの接続を解除する。
}

header('Content-Type: application/json');              // JSON形式でレスポンスを送信するよう指定
echo json_encode($response, JSON_UNESCAPED_UNICODE);   // $responseのデータをJSON形式に加工して出力
?>