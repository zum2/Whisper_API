<?php

include('errorMsgs.php');		                            // エラーメッセージ用のPHPファイルの読み込み

$response =[
    
    "result" => "",                                         // 実行結果を格納する(success or error)
    "errCode" => null,                                      // エラーコードがある場合格納する
    "errMsg" => null,                                       // エラーメッセージがある場合格納する
    "followList" =>[],                                     	// フォロー情報の配列
    "followerList" =>[],                                    // フォロワー情報の配列

];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {                // HTTPメソッドがPOST形式で送られてきたか確認。
	
    $postData = json_decode(file_get_contents('php://input'), true);

}

// Inputパラメータの必須チェックを行う
if(!isset($postData["userId"]) || $postData["userId"] == ""){
    $response = setError($response,"006");                  // 【エラーコード】ユーザID：006
}

if($response["errCode"] == null){

	$userId = $postData["userId"];

    include('mysqlConnect.php');

	// フォローリストを取得するSQL文
	$sql = "SELECT u.userId, u.userName, wcv.cnt AS whispers, fcv.cnt AS follow,
    				COALESCE(fscv.cnt, 0) AS followers
			FROM follow AS f
			LEFT JOIN user AS u ON u.userId = f.followUserId
			LEFT JOIN whisperCntView AS wcv ON wcv.userId = f.followUserId
			LEFT JOIN followCntView AS fcv ON fcv.userId = f.followUserId
			LEFT JOIN followerCntView AS fscv ON fscv.followUserId = f.followUserId
			WHERE f.userId = :userId";

	$stmt = $pdo->prepare($sql); 
	$stmt->bindParam(":userId", $userId, PDO::PARAM_STR);
	$stmt->execute();

	try{
        while ($row = $stmt->fetch()) { 
            $data["userId"] = $row["userId"];
			$data["userId"] = $row["userId"];
			$data["userName"] = $row["userName"];
			$data["whispers"] = $row["whispers"];
			$data["follow"] = $row["follow"];
			$data["followers"] = $row["followers"];
            $response["followList"][] = $data;
        }
        $response["result"] = "success";    // successに書き換え
    }catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }

	$stmt = null;										// SQL情報をクローズ
	
	// フォロワーリストを取得するSQL文
	$sql ="SELECT u.userId, u.userName, wcv.cnt AS whispers, fcv.cnt AS follow, 
					COALESCE(fscv.cnt, 0) AS followers
			FROM follow AS f
			LEFT JOIN user AS u ON u.userId = f.userId
			LEFT JOIN whisperCntView AS wcv ON wcv.userId = f.userId
			LEFT JOIN followCntView AS fcv ON fcv.userId = f.userId
			LEFT JOIN followerCntView AS fscv ON fscv.followUserId = f.userId
			WHERE f.followUserId = :userId";

	$stmt = $pdo->prepare($sql); 
	$stmt->bindParam(":userId", $userId, PDO::PARAM_STR);
	$stmt->execute();

	try{
        while ($row = $stmt->fetch()) { 
            $data["userId"] = $row["userId"];
			$data["userId"] = $row["userId"];
			$data["userName"] = $row["userName"];
			$data["whispers"] = $row["whispers"];
			$data["follow"] = $row["follow"];
			$data["followers"] = $row["followers"];
            $response["followerList"][] = $data;
        }
        $response["result"] = "success";    // successに書き換え
    }catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }

	$stmt = null;										// SQL情報をクローズ

	include('mysqlClose.php');                          // DB切断処理を呼び出し
}
header('Content-Type: application/json');          		// JSON形式でレスポンスを送信するよう指定
echo json_encode($response, JSON_UNESCAPED_UNICODE); 	// $responseのデータをJSON形式に加工して出力                                                          

?>