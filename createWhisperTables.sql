/*-- Whisperデータベース  テーブル作成スクリプト */


/*---------------以下テーブル削除----------------------*/

DROP TABLE IF EXISTS goodInfo;
DROP TABLE IF EXISTS follow;
DROP TABLE IF EXISTS whisper;
DROP TABLE IF EXISTS user;
DROP VIEW IF EXISTS followCntView;
DROP VIEW IF EXISTS followerCntView;
DROP VIEW IF EXISTS whisperCntView;
DROP VIEW IF EXISTS goodCntView;

/*---------------以下テーブル作成----------------------*/

-- ユーザ情報表
CREATE TABLE user(
	userId   VARCHAR(30)  PRIMARY KEY,
	userName VARCHAR(20)  NOT NULL,
	password VARCHAR(64)  NOT NULL,
	profile  VARCHAR(200) DEFAULT '',
	iconPath VARCHAR(100)
);

-- フォロー情報表
CREATE TABLE follow(
	userId        VARCHAR(30),
	followUserId  VARCHAR(30),
	PRIMARY KEY(userId,followUserId),
	FOREIGN KEY ( userId ) REFERENCES user( userId ),
	FOREIGN KEY ( followUserId ) REFERENCES user( userId )
);


-- ささやき管理表
CREATE TABLE whisper(
	whisperNo	BIGINT PRIMARY KEY AUTO_INCREMENT,
	userId		VARCHAR(30)  NOT NULL,
	postDate	DATE NOT NULL,
	content		VARCHAR(256) NOT NULL,
	imagePath	VARCHAR(100),
	FOREIGN KEY ( userId ) REFERENCES user( userId )
);

-- イイね情報表
CREATE TABLE goodInfo(
	userId     VARCHAR(30),
	whisperNo  BIGINT,
	PRIMARY KEY(userId,whisperNo),
	FOREIGN KEY ( userId ) REFERENCES user( userId ),
	FOREIGN KEY ( whisperNo ) REFERENCES whisper( whisperNo )
);

-- フォロー件数ビュー
CREATE view followCntView AS
SELECT userId, COUNT(*) AS cnt FROM follow
GROUP BY userId;

-- フォロワー件数ビュー
CREATE view followerCntView AS
SELECT followUserId, COUNT(*) AS cnt FROM follow
GROUP BY userId;

-- ささやき件数ビュー
CREATE view whisperCntView AS
SELECT userId, COUNT(*) AS cnt FROM whisper
GROUP BY userId;

-- イイね件数ビュー
CREATE view goodCntView AS
SELECT whisperNo, COUNT(*) AS cnt FROM goodInfo
GROUP BY whisperNo;


