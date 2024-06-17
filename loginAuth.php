<?php

include('errorMsgs.php');		                            // エラーメッセージ用のPHPファイルの読み込み

$response =[
    
    "result" => "",                                         // 実行結果を格納する(success or error)
    "errCode" => null,                                      // エラーコードがある場合格納する
    "errMsg" => null,                                       //エラーメッセージがある場合格納する
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {                // HTTPメソッドがPOST形式で送られてきたか確認。
	
    $postData = json_decode(file_get_contents('php://input'), true);

}

// Inputパラメータの必須チェックを行う。
if(!isset($postData['userId']) || $postData['userId'] == ""){
    $response = setError($response,"006");                  // 【エラーコード】ユーザID：006
}

if(!isset($postData['password'])|| $postData['password'] == ""){
    $response = setError($response,"007");                  // 【エラーコード】パスワード：007
}

if($response["errCode"] == null){
    $userId = $postData["userId"];
    $password = $postData["password"];

    include('mysqlConnect.php');                            // DB接続処理を呼び出し


    // ユーザIDとパスワードと一致する対象データの件数を取得するSQL文を実行
    $sql = "SELECT * FROM user WHERE userId = :userId AND password = :password ";

    $stmt = $pdo->prepare($sql); 
    $stmt->bindParam(":userId", $userId, PDO::PARAM_STR);
    $stmt->bindParam(":password", $password, PDO::PARAM_STR);
    $stmt->execute();
    $count = $stmt -> rowCount();                        // データ件数カウント

    if($count != 1){                                       // データ件数が１件以外の場合
        $response = setError($response,"003");           // 【エラーコード】003
    }else{
        try{
            while ($row = $stmt->fetch()) {
                $data["userId"] = $row["userId"];
                $data["password"] = $row["password"];
                $response["list"][] = $data;
            }
            $response["result"] = "success";    // successに書き換え
        }catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    $stmt = null;                                               // SQL情報をクローズさせる

    include('mysqlClose.php');                                  // DB切断処理を呼び出し
}

header('Content-Type: application/json');          // JSON形式でレスポンスを送信するよう指定
echo json_encode($response, JSON_UNESCAPED_UNICODE); // $responseのデータをJSON形式に加工して出力
?>