<?php

// ユーザ情報取得API
// 対象ユーザのユーザ情報を取得する
// 作成者：泉

// エラーメッセージ用のPHPファイルの読み込み
include('errorMsgs.php');

$response = [
    "result" => "",         // 実行結果を格納する(success or error)
    "errCode" => null,      // エラーコードがある場合格納する
    "errMsg" => null,       //エラーメッセージがある場合格納する
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // HTTPメソッドがPOST形式で送られてきたか確認。
    $postData = json_decode(file_get_contents('php://input'), true);
}

/*
１．Inputパラメータの必須チェックを行う。
　　パラメータが無い場合、対象エラーメッセージをセットしてエラー終了させる。
　　【エラーコード】ユーザID：006
*/

//inputパラメータの代入
$userId = $postData["userId"];

if (!isset($postData['userId'])) {
    $response = setError($response, "006");
} else {
    // ２．DB接続処理を呼び出し、データベースの接続を行う。
    include('mysqlConnect.php');
}


/*
３．ユーザ情報を取得するSQL文を実行する。
対象列：ユーザ名、プロフィール、アイコン(userName,profile,icon)
対象テーブル：ユーザ情報(user)
取得対象：inputパラメータのユーザIDと一致するデータ
*/

// クエリ(SQL)の実行
$sql = "SELECT userName,profile,icon FROM user WHERE userID = :userId";
$stmt = $pdo->prepare($sql);

// データのバインド
$stmt->bindParam(':userId', $userId, PDO::PARAM_STR);

/*
４．データのフェッチを行う。
　　データが存在しない場合、対象エラーメッセージをセットしてエラー終了させる。
　　【エラーコード】004
*/

if ($stmt->execute()) {
    // 成功の処理
    $response["result"] = "success";
    // データの取得
    // fetch()で1行ずつデータを取得。
    while ($row = $stmt->fetch()) {
        // fetch()で取得した1行分のデータが$rowに格納されているので、対象の列を指定して処理する。
        // 大文字と小文字を区別されてたらしい。なぜ。
        //返したときに表示する$dataをここで用意する。　それぞれの箱に$rowを詰める。
        $data["userName"] = $row["userName"];
        $data["profile"] = $row["profile"];
        $data["icon"] =  $row["iconPath"];
        // //レスポンスの枠組み(箱)の中に取得したデータを詰め込む
        $response["list"][] = $data;
        //var_dump($product_no);
    }
} else {
    //失敗の処理
    $response = setError($response, "004");
}


// ５．返却値の連想配列に成功パラメータとユーザ情報のデータを格納する



// ６．SQL情報をクローズさせる。
$stmt = null;

// ７．DB切断処理を呼び出し、データベースの接続を解除する。
include('mysqlClose.php');

// ８．返却値の連想配列をJSONにエンコードしてoutputパラメータを出力する。
header('Content-Type: application/json'); // JSON形式でレスポンスを送信するよう指定
echo json_encode($response, JSON_UNESCAPED_UNICODE); // $responseのデータをJSON形式に加工して出力