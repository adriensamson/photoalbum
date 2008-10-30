CREATE TABLE photoalbum_users (id_user INT AUTO_INCREMENT PRIMARY KEY, name TEXT, md5 CHAR(32), email TEXT, invite INT, lastvisit INT, lastmail INT);
CREATE TABLE photoalbum_albums (id_album INT AUTO_INCREMENT PRIMARY KEY, title TEXT, id_owner INT);
CREATE TABLE photoalbum_photos (id_photo INT AUTO_INCREMENT PRIMARY KEY, filename TEXT, id_album INT, lastchanged INT);
CREATE TABLE photoalbum_tags (id_tag INT AUTO_INCREMENT PRIMARY KEY, id_photo INT, id_user INT, x INT, y INT, width INT, height INT, id_tager INT);
CREATE TABLE photoalbum_comments (id_comment INT AUTO_INCREMENT PRIMARY KEY, id_photo INT, id_user INT, comment TEXT);
CREATE TABLE photoalbum_unseen_changes (id_user INT, id_photo INT);
