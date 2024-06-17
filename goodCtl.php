<?php
include('errorMsgs.php');                                    // エラーメッセージ用のPHPファイルの読み込み

$response = [

    "result" => "",                                         // 実行結果を格納する(success or error)
    "errCode" => null,                                      // エラーコードがある場合格納する
    "errMsg" => null,                                       // エラーメッセージがある場合格納する

];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {                // HTTPメソッドがPOST形式で送られてきたか確認。

    $postData = json_decode(file_get_contents('php://input'), true);
}

// Inputパラメータの必須チェックを行う
if (!isset($postData["userId"]) || $postData["userId"] == "") {
    $response = setError($response, "006");                  // 【エラーコード】ユーザID：006
}

if (!isset($postData["whisperNo"]) || $postData["whisperNo"] == "") {
    $response = setError($response, "008");                  // 【エラーコード】ユーザID：008
}

if (!isset($postData["goodFlg"]) || !is_bool($postData["goodFlg"])) {
    $response = setError($response, "014");                  // 【エラーコード】ユーザID：014
}

// エラーがなければ
if ($response["errCode"] == null) {

    $userId = $postData["userId"];
    $whisperNo = $postData["whisperNo"];
    $goodFlg = $postData["goodFlg"];

    //エラー"001"を検出するためのtry-catch　-　泉
    try {
        include('mysqlConnect.php');                            // DB接続処理を呼び出し

        //トランザクション開始　-　泉
        $pdo->beginTransaction();

        // イイねフラグがtrue(イイね)の場合
        if ($goodFlg == true) {
            $sql = "INSERT INTO goodInfo(userId,whisperNo) VALUES(:userId,:whisperNo)";
        }

        // イイねフラグがfalse(イイねを外す)の場合
        if ($goodFlg == false) {
            $sql = "DELETE FROM goodInfo WHERE userId = :userId AND whisperNo = :whisperNo";
        }
        $stmt = $pdo->prepare($sql);
        // データのバインド
        $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
        $stmt->bindParam(':whisperNo', $whisperNo, PDO::PARAM_STR);

        $stmt->execute();
        // 成功の処理
        $response["result"] = "success";
        //$response = array('success' => true);
        $pdo->commit();
    } catch (Exception $e) {
        //具体的なエラー内容が知りたいとき↓　-　泉
        //var_dump($e->getMessage());
        //失敗時の処理　-　泉
        $response = setError($response, "001");
        $pdo->rollBack();
    }


    //↓ここじゃエラーでも成功したことになるよ…　泉
    //$response = array('success' => true);                  // 返却値の連想配列に成功パラメータをセット

    $stmt = null;                                          // SQL情報をクローズ

    include('mysqlClose.php');
}
header('Content-Type: application/json'); // JSON形式でレスポンスを送信するよう指定
echo json_encode($response, JSON_UNESCAPED_UNICODE); // $responseのデータをJSON形式に加工して出力
