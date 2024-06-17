<?php
include('errorMsgs.php');        // メッセージ用のPHPファイルの読み込み

$response = [
    "result" => "",         // 実行結果を格納する(success or error)
    "errCode" => null,      // エラーコードがある場合格納する
    "errMsg" => null,       //エラーメッセージがある場合格納する
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // HTTPメソッドがPOST形式で送られてきたか確認。
    $postData = json_decode(file_get_contents('php://input'), true);
}



// １．Inputパラメータの必須チェックを行う。
if (!isset($postData['userId'])) {
    $response = setError($response, "006");
} else if (!isset($postData['content'])) {
    $response = setError($response, "005");          //【エラーコード】ユーザID：006、ささやき内容：005	
}

// errCodeに何も入っていない場合(エラーではない場合) - 泉
if (is_null($response["errCode"])) {

    include('mysqlConnect.php');                   // DB接続処理を呼び出し、データベースの接続を行う。

    $userId = $postData["userId"];
    $content = $postData["content"];

    $date = date('Y-m-d H:i:s');

    //トランザクションの開始　-　泉
    $pdo->beginTransaction();

    $sql = "INSERT INTO whisper (userId,content,postDate) VALUES(:userId,:content,:date)";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // 成功の処理
        //$response = array('success' => true);
        $response['result'] = "success";        //泉
        $pdo->commit();                                // データベースのコミット命令を実行する。
    } else {
        $pdo->rollBack();                              //  データベースのロールバック命令を実行する。
        $response = setError($response, "001");         // 【エラーコード】001
    }
}

// SQL情報をクローズさせる。									
$stmt = null;

// DB切断処理を呼び出し、データベースの接続を解除する。									
include('mysqlClose.php');

// 返却値の連想配列をJSONにエンコードしてoutputパラメータを出力する。									
header('Content-Type: application/json'); // JSON形式でレスポンスを送信するよう指定
echo json_encode($response, JSON_UNESCAPED_UNICODE); // $responseのデータをJSON形式に加工して出力
