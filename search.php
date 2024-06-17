<?php
include('errorMsgs.php');		                            // エラーメッセージ用のPHPファイルの読み込み

$response =[
    
    "result" => "",                                         // 実行結果を格納する(success or error)
    "errCode" => null,                                      // エラーコードがある場合格納する
    "errMsg" => null,                                       //エラーメッセージがある場合格納する
    "userList" =>[],
    "whisperList" =>[],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {                // HTTPメソッドがPOST形式で送られてきたか確認。
	
    $postData = json_decode(file_get_contents('php://input'), true);

}

// Inputパラメータの必須チェックを行う									
if (!isset($postData['section']) || $postData['section'] == "") {
    $response = setError($response, "009");                 //【エラーコード】検索区分：009
}

if(!isset($postData['string'])|| $postData['string'] == ""){
    $response = setError($response,"010");                  //【エラーコード】検索文字列：010
}

if($response["errCode"] == null){
    // 検索区分の整合性チェック
    if($section != "1" && $section != "2"){                     // 検索区分が1または2の場合
        $response = setError($response,"016");                  //【エラーコード】検索文字列：010
    }

    $section = $postData["section"];
    $string = $postData["string"];

    include('mysqlConnect.php');                                // DB接続処理を呼び出し

    if ($section == 1) {                                        // 検索区分が1(ユーザ検索)の場合

        $sql = "SELECT u.userId, u.userName, fw.cnt AS follow, fwer.cnt AS followers, wh.cnt AS whispers 
        FROM user AS u 
        LEFT JOIN followCntView AS fw ON u.userId = fw.userId 
        LEFT JOIN followerCntView AS fwer ON u.userId = fwer.followuserId 
        LEFT JOIN whisperCntView AS wh ON u.userId = wh.userId 
        WHERE u.userId LIKE :userInput";

        $stmt = $pdo->prepare($sql);
        $string = "%" . $string . "%";
        $stmt->bindValue(':userInput', $string);
        $stmt->execute();

        try{
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {      // データのフェッチを行い、検索結果のデータがある間以下の処理を繰り返す
                $data["userId"] = $row["userId"];
                $data["userName"] = $row["userName"];
                $data["follow"] = $row["follow"];
                $data["followers"] = $row["followers"];
                $data["whispers"] = $row["whispers"];
                $response["userList"][] = $data;                   // ユーザーリストの連想配列にデータを追加

            }

            $response["result"] = "success";                    // successに書き換え
        
        }catch (PDOException $e) {
            
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        
        }

        $stmt = null;                                           // SQL情報をクローズ

    }else if($section == 2){                                    // 検索区分が2(ささやき検索)の場合
        
        $sql="SELECT w.whisperNo, u.userName, w.postDate, w.content, gcv.cnt AS goods 
        FROM whisper AS w 
        LEFT JOIN goodCntView AS gcv ON w.whisperNo = gcv.whisperNo 
        LEFT JOIN user AS u ON w.userId = u.userId 
        WHERE w.content LIKE :ctInput";
        
        $stmt = $pdo->prepare($sql);
        $string = "%" . $string . "%";
        $stmt->bindValue(':ctInput', $string);
        $stmt->execute();

        try{
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {      //  データのフェッチを行い、検索結果のデータがある間以下の処理を繰り返す
                $data["whisperNo"] = $row["whisperNo"];
                $data["userName"] = $row["userName"];
                $data["postDate"] = $row["postDate"];
                $data["content"] = $row["content"];
                $data["goods"] = $row["goods"];
                $response["whisperList"][] = $data;                   // ささやきリストの連想配列にデータを追加する
            
            }
            
            $response["result"] = "success";                    // successに書き換え
        
        }catch (PDOException $e) {
            
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        
        }
        
        $stmt = null;                                           // SQL情報をクローズさせる
    }

    include('mysqlClose.php');                                  // ７．DB切断処理を呼び出し、データベースの接続を解除する。
}
// 返却値の連想配列をJSONにエンコードしてoutputパラメータを出力する。									
header('Content-Type: application/json');                   // JSON形式でレスポンスを送信するよう指定
echo json_encode($response, JSON_UNESCAPED_UNICODE);        // $responseのデータをJSON形式に加工して出力									 -->
?>