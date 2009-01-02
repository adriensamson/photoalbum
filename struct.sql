CREATE TABLE `photoalbum_albums` (
  `id_album` int(11) NOT NULL auto_increment,
  `title` text,
  `id_owner` int(11) default NULL,
  `album_date` date NOT NULL,
  PRIMARY KEY  (`id_album`),
  KEY `id_owner` (`id_owner`)
);

CREATE TABLE `photoalbum_comments` (
  `id_comment` int(11) NOT NULL auto_increment,
  `id_photo` int(11) default NULL,
  `id_user` int(11) default NULL,
  `comment` text,
  PRIMARY KEY  (`id_comment`)
);

CREATE TABLE `photoalbum_photos` (
  `id_photo` int(11) NOT NULL auto_increment,
  `filename` text,
  `id_album` int(11) default NULL,
  `lastchanged` int(11) NOT NULL,
  `legend` text NOT NULL,
  PRIMARY KEY  (`id_photo`),
  KEY `id_album` (`id_album`)
);

CREATE TABLE `photoalbum_tags` (
  `id_tag` int(11) NOT NULL auto_increment,
  `id_photo` int(11) default NULL,
  `id_user` int(11) default NULL,
  `x` int(11) default NULL,
  `y` int(11) default NULL,
  `width` int(11) default NULL,
  `height` int(11) default NULL,
  `id_tager` int(11) default NULL,
  `fake_tag` text,
  PRIMARY KEY  (`id_tag`),
  KEY `id_photo` (`id_photo`),
  KEY `id_user` (`id_user`)
);

CREATE TABLE `photoalbum_unseen_changes` (
  `id_user` int(11) default NULL,
  `id_photo` int(11) default NULL,
  KEY `id_user` (`id_user`)
);

CREATE TABLE `photoalbum_users` (
  `id_user` int(11) NOT NULL auto_increment,
  `name` text,
  `md5` char(32) default NULL,
  `email` text,
  `invite` int(11) default NULL,
  `lastvisit` int(11) NOT NULL,
  `lastmail` int(11) NOT NULL,
  PRIMARY KEY  (`id_user`)
);

