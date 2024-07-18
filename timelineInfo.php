<?php

// タイムライン取得API
// 対象ユーザがフォローしているささやきを取得する
// 作成者：泉

// エラーメッセージ用のPHPファイルの読み込み
include('errorMsgs.php');

$response = [
    "result" => "",         // 実行結果を格納する(success or error)
    "errCode" => null,      // エラーコードがある場合格納する
    "errMsg" => null,       //エラーメッセージがある場合格納する
    "whisperList" => []     //ささやき情報リスト
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // HTTPメソッドがPOST形式で送られてきたか確認。
    $postData = json_decode(file_get_contents('php://input'), true);
}

/*
１．Inputパラメータの必須チェックを行う。
　　パラメータが無い場合、対象エラーメッセージをセットしてエラー終了させる。
　　【エラーコード】ユーザID：006
*/


//$postDataのuserIdがnullか空文字の場合
if (!isset($postData['userId']) || $postData['userId'] == "") {
    $response = setError($response, "006");
}


// errCodeに何も入っていない場合(エラーではない場合)
if (is_null($response["errCode"])) {
    // ２．DB接続処理を呼び出し、データベースの接続を行う。
    include('mysqlConnect.php');

    //inputパラメータの代入
    $userId = $postData["userId"];

    // ささやきリストの内容を取得するSQL文を実行する
    $sql = "SELECT w.whisperNo,w.userId,u.userName,w.postDate,w.content,IFNULL(goodFlg,FALSE) AS goodFlg
    FROM   whisper AS w
    LEFT OUTER JOIN (SELECT userId,whisperNo,TRUE AS goodFlg 
                     FROM goodInfo
                     WHERE userId = :userId_1) AS g
    ON (w.whisperNo = g.whisperNo)
    INNER JOIN user AS u
    ON (w.userId = u.userId)
    WHERE w.userId = :userId_2
    UNION ALL
    SELECT w.whisperNo,w.userId,u.userName,w.postDate,w.content,IFNULL(goodFlg,FALSE) AS goodFlg
    FROM follow AS f
    INNER JOIN whisper AS w
    ON (f.followUserId = w.userId)
    INNER JOIN user AS u
    ON (f.followUserId = u.userId)
    LEFT OUTER JOIN (SELECT userId,whisperNo,TRUE AS goodFlg 
                    FROM goodInfo
                    WHERE userId = :userId_3) AS g
    ON (w.whisperNo = g.whisperNo)
    WHERE f.userId = :userId_4
    ORDER BY postDate DESC";

    // データのバインド
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId_1', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':userId_2', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':userId_3', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':userId_4', $userId, PDO::PARAM_STR);

    /*
4.データのフェッチを行い、検索結果のデータがある間以下の処理を繰り返す。									
	４－１．イイねフラグがnull以外の場合はtrueを、それ以外の場合はfalseをセットする。	
	４－２．ささやきリストの連想配列にデータを追加する。								
*/
    $stmt->execute();

    //fetchで取得
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        //レスポンスの枠組み(箱)の中に取得したデータを詰め込む
        // それぞれの箱に$rowを詰める。

        // ５．返却値の連想配列に成功パラメータとユーザ情報のデータを格納する
        $data["whisperNo"] = $row["whisperNo"];
        $data["userId"] = $row["userId"];
        $data["userName"] = $row["userName"];
        $data["postDate"] =  $row["postDate"];
        $data["content"] =  $row["content"];

        //イイねフラグが1ならtrue　それ以外はfalse
        if ($row["goodFlg"] == 1) {
            $data["goodFlg"] = "true";
        } else {
            $data["goodFlg"] = "false";
        }

        // //レスポンスの枠組み(箱)の中に取得したデータを詰め込む
        $response["whisperList"][] = $data;
    }

    // 6.返却値の連想配列に成功パラメータとささやきリスト連想配列のデータを格納する		
    // 成功の処理
    $response["result"] = "success";


    if (empty($response["whisperList"])) {
        //失敗の処理
        $response = setError($response, "004");
    }

    // 5.SQL情報をクローズさせる。									
    $stmt = null;


    // 7.DB切断処理を呼び出し、データベースの接続を解除する。									
    include('mysqlClose.php');
}
// 8.返却値の連想配列をJSONにエンコードしてoutputパラメータを出力する。
header('Content-Type: application/json'); // JSON形式でレスポンスを送信するよう指定
echo json_encode($response, JSON_UNESCAPED_UNICODE); // $responseのデータをJSON形式に加工して出力									
