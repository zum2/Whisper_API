/*-- Whisperデータベース  テーブル作成スクリプト */


/*---------------以下テーブル削除----------------------*/

DROP TABLE user;
DROP TABLE follow;
DROP TABLE Whisper;
DROP TABLE goodInfo;
DROP TABLE followCntView;
DROP TABLE followerCntView;
DROP TABLE whisperCntView;
DROP TABLE goodCntView;

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
	PRIMARY KEY(userId,followUserId)
);


-- ささやき管理表
CREATE TABLE whisper(
	whisperNo   BIGINT PRIMARY KEY AUTO_INCREMENT,
	userId      VARCHAR(30)  NOT NULL
	postDate    date         NOT NULL DEFAULT CURRENT_TIMESTAMP,	
	content     VARCHAR(256) NOT NULL,
	imagePath   VARCHAR(100)
);

-- イイね情報表
CREATE TABLE goodInfo(
	userId     VARCHAR(30),
	whisperNo  BIGINT,
	PRIMARY KEY(userId,whisperNo)
);

-- フォロー件数ビュー
CREATE followCntView(
);



