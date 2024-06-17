<?php

include('errorMsgs.php');		                            // エラーメッセージ用のPHPファイルの読み込み

$response =[
    
    "result" => "",                                         // 実行結果を格納する(success or error)
    "errCode" => null,                                      // エラーコードがある場合格納する
    "errMsg" => null,                                       // エラーメッセージがある場合格納する
    "whisperList" =>[],                                     // ささやき情報の配列
    "goodList" =>[],                                        // イイね情報の配列

];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {                // HTTPメソッドがPOST形式で送られてきたか確認。
	
    $postData = json_decode(file_get_contents('php://input'), true);

}

// Inputパラメータの必須チェックを行う。
if(!isset($postData['userId']) || $postData['userId'] == ""){
    $response = setError($response,"006");                  // 【エラーコード】ユーザID：006

}

if(!isset($postData['loginUserId']) || $postData['loginUserId'] == ""){
    $response = setError($response,"015");                  // 【エラーコード】パスワード：015
}

if($response["errCode"] == null){

    $userId = $postData["userId"];
    $loginUserId = $postData["loginUserId"];

    include('mysqlConnect.php');                            // DB接続処理を呼び出し

    // ユーザ情報を取得するSQL文を実行
    $sql = "SELECT u.userName, u.profile, fcv.cnt AS follows, fscv.cnt AS followers 
    FROM user as u 
    LEFT JOIN followCntView AS fcv ON u.userId = fcv.userId 
    LEFT JOIN followerCntView AS fscv ON u.userid = fscv.followUserId
    WHERE u.userId = :userId";

    $stmt = $pdo->prepare($sql); 
    $stmt->bindParam(":userId", $userId, PDO::PARAM_STR);
    $stmt->execute();

    if($stmt == null){                                          // データが存在しない場合
        $response = setError($response,"004");                  // 【エラーコード】004
    }

    try{
        while ($row = $stmt->fetch()) { 
            $data["userName"] = $row["userName"];
            $data["profile"] = $row["profile"];
            $data["follows"] = $row["follows"];
            $data["followers"] = $row["followers"];
            $response["userList"][] = $data;
        }
        $response["result"] = "success";    // successに書き換え
    }catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }

    $stmt = null;                                                // SQL情報をクローズさせる

    // フォロー中情報を取得するSQL文
    $sql = "SELECT * FROM follow WHERE userId = :loginUserId";

    $stmt = $pdo->prepare($sql); 
    $stmt->bindParam(":loginUserId", $loginUserId, PDO::PARAM_STR);
    $stmt->execute();

    if($stmt == null){                                          // データが存在しない場合
        $response = setError($response,"004");                  // 【エラーコード】004
    }

    $stmt = null;                                               // SQL情報をクローズさせる

    // ささやきリストを取得するSQL文
    $sql = "SELECT w.whisperNo, u.userId, u.userName,w.postDate, w.content,
    CASE 
        WHEN g.userId IS NOT NULL THEN TRUE 
        ELSE FALSE 
    END AS goodflg
    FROM whisper AS w
    LEFT JOIN user AS u ON u.userId = w.userId
    LEFT JOIN goodInfo AS g ON g.whisperNo = w.whisperNo AND g.userId = :loginUserId
    ORDER BY w.postDate DESC";

    $stmt = $pdo->prepare($sql); 
    $stmt->bindParam(":loginUserId", $loginUserId, PDO::PARAM_STR);
    $stmt->execute();

    try{
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data["whisperNo"] = $row["whisperNo"];
            $data["userId"] = $row["userId"];
            $data["userName"] = $row["userName"];
            $data["postDate"] = $row["postDate"];
            $data["content"] = $row["content"];
            $data["goodflg"] = $row["goodflg"];
            $response["whisperList"][] = $data;
        }
        $response["result"] = "success";                        // successに書き換え
    }catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }

    $stmt =null;                                                //  SQL情報をクローズさせる

    // イイねリストを取得するSQL文
    $sql = "SELECT w.whisperNo, u.userId, u.userName, w.postDate, w.content,
    CASE 
        WHEN g.userId IS NOT NULL THEN TRUE 
        ELSE FALSE 
    END AS goodflg
    FROM goodInfo AS g
    LEFT JOIN whisper AS w ON w.whisperNo = g.whisperNo AND w.userId = :loginUserId
    LEFT JOIN user AS u ON g.userid = u.userId
    ORDER BY w.postdate DESC";

    $stmt = $pdo->prepare($sql); 
    $stmt->bindParam(":loginUserId", $loginUserId, PDO::PARAM_STR);
    $stmt->execute();

    try{
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data["whisperNo"] = $row["whisperNo"];
            $data["userId"] = $row["userId"];
            $data["userName"] = $row["userName"];
            $data["postDate"] = $row["postDate"];
            $data["content"] = $row["content"];
            $data["goodflg"] = $row["goodflg"];
            $response["goodList"][] = $data;
        }
        $response["result"] = "success";                      // successに書き換え
    }catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }

    $stmt = null;                                             // SQL情報をクローズさせる 

    include('mysqlClose.php');                                  // DB切断処理を呼び出し
}

header('Content-Type: application/json');          // JSON形式でレスポンスを送信するよう指定
echo json_encode($response, JSON_UNESCAPED_UNICODE); // $responseのデータをJSON形式に加工して出力                                                          

?>