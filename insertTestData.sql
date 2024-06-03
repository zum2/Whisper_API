/*---------------------テストデータ挿入---------------------------*/
-- ユーザ(3人分) user
INSERT INTO user(userId, userName, password, profile, iconPath)
VALUES ('testTanaka@mail.com','田中','tanaka0101','こんにちは！','\\home\\Documents\\Whisper_API\¥tanakaicon.png');
INSERT INTO user(userId, userName, password, profile, iconPath)
VALUES ('testSuzuki@mail.com','鈴木','suzuki0102','おはようございます。','\\home\\Documents\\Whisper_API\\suzukiicon.png');
INSERT INTO user(userId, userName, password, profile, iconPath)
VALUES ('testSatou@mail.com','佐藤','satou0103','こんばんみ','\\home\\Documents\\Whisper_API\\satouicon.png');

-- フォロー情報 follow

-- 田中→鈴木
INSERT INTO follow(userId,followUserId)
VALUES ('testTanaka@mail.com','testSuzuki@mail.com');

-- 鈴木→佐藤
INSERT INTO follow(userId,followUserId)
VALUES ('testSuzuki@mail.com','testSatou@mail.com');

-- 佐藤→田中、鈴木
INSERT INTO follow(userId,followUserId)
VALUES ('testSatou@mail.com','testTanaka@mail.com');
INSERT INTO follow(userId,followUserId)
VALUES ('testSatou@mail.com','testSuzuki@mail.com');

-- ささやき(12件分) whisper
INSERT INTO whisper(userId,postDate,content)
VALUES ('testTanaka@mail.com',CURRENT_TIMESTAMP,'おなかすいた');
INSERT INTO whisper(userId,postDate,content,imagePath)
VALUES ('testTanaka@mail.com',CURRENT_TIMESTAMP,'いい天気だな','\\home\\Documents\\Whisper_API\\sky.png');
INSERT INTO whisper(userId,postDate,content)
VALUES ('testTanaka@mail.com',CURRENT_TIMESTAMP,'もう寝ようかな');
INSERT INTO whisper(userId,postDate,content)
VALUES ('testTanaka@mail.com',CURRENT_TIMESTAMP,'また明日会おう');

INSERT INTO whisper(userId,postDate,content,imagePath)
VALUES ('testSuzuki@mail.com',CURRENT_TIMESTAMP,'こちらの今日の天気は雨です','\\home\\Documents\\Whisper_API\\rainysky.png');
INSERT INTO whisper(userId,postDate,content)
VALUES ('testSuzuki@mail.com',CURRENT_TIMESTAMP,'財布を忘れてしまいました');
INSERT INTO whisper(userId,postDate,content)
VALUES ('testSuzuki@mail.com',CURRENT_TIMESTAMP,'猫ってかわいいですよね');
INSERT INTO whisper(userId,postDate,content,imagePath)
VALUES ('testSuzuki@mail.com',CURRENT_TIMESTAMP,'駅前で見かけました','\\home\\Documents\\Whisper_API\\cat.png');

INSERT INTO whisper(userId,postDate,content)
VALUES ('testSatou@mail.com',CURRENT_TIMESTAMP,'エナドリうまい');
INSERT INTO whisper(userId,postDate,content,imagePath)
VALUES ('testSatou@mail.com',CURRENT_TIMESTAMP,'なんだこれ','\\home\\Documents\\Whisper_API\\sonmething.png');
INSERT INTO whisper(userId,postDate,content)
VALUES ('testSatou@mail.com',CURRENT_TIMESTAMP,'頭痛ぇ～～');
INSERT INTO whisper(userId,postDate,content)
VALUES ('testSatou@mail.com',CURRENT_TIMESTAMP,'ゲーム楽しすぎ');

-- いいね情報 goodInfo
INSERT INTO goodInfo(userId,whisperNo)
VALUES ('testTanaka@mail.com',5);
INSERT INTO goodInfo(userId,whisperNo)
VALUES ('testTanaka@mail.com',8);

INSERT INTO goodInfo(userId,whisperNo)
VALUES ('testSuzuki@mail.com',9);
INSERT INTO goodInfo(userId,whisperNo)
VALUES ('testSuzuki@mail.com',10);
INSERT INTO goodInfo(userId,whisperNo)
VALUES ('testSuzuki@mail.com',11);

INSERT INTO goodInfo(userId,whisperNo)
VALUES ('testSatou@mail.com',1);
INSERT INTO goodInfo(userId,whisperNo)
VALUES ('testSatou@mail.com',3);
INSERT INTO goodInfo(userId,whisperNo)
VALUES ('testSatou@mail.com',6);
INSERT INTO goodInfo(userId,whisperNo)
VALUES ('testSatou@mail.com',8);
















