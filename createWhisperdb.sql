
/*-- Whisperデータベース  環境構築スクリプト */

-- ユーザ作成(User名：wipuser)
DROP USER wipuser;
CREATE USER wipuser IDENTIFIED WITH MYSQL_NATIVE_PASSWORD BY 'whisper';

-- データベース削除
DROP DATABASE IF EXISTS WHISPER;
-- データベース作成(WHISPER：テスト環境)
CREATE DATABASE WHISPER;

-- ユーザにデータベース権限付与
GRANT ALL ON WHISPER.* TO wipuser;

-- データベース移動にテーブル作成(テスト環境)
USE WHISPER;
source createWhisperTables.sql
