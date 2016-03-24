-- MySQL dump 9.10
--
-- Host: localhost    Database: bittorrent
-- ------------------------------------------------------
-- Server version	4.0.18-log

--
-- Table structure for table `avps`
--

CREATE TABLE avps (
  arg varchar(20) NOT NULL default '',
  value_s text NOT NULL,
  value_i int(11) NOT NULL default '0',
  value_u int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (arg)
) TYPE=MyISAM;

--
-- Table structure for table `bans`
--

CREATE TABLE bans (
  id int(10) unsigned NOT NULL auto_increment,
  added datetime NOT NULL default '0000-00-00 00:00:00',
  addedby int(10) unsigned NOT NULL default '0',
  comment varchar(255) NOT NULL default '',
  first int(11) default NULL,
  last int(11) default NULL,
  PRIMARY KEY  (id),
  KEY first_last (first,last)
) TYPE=MyISAM;

--
-- Table structure for table `blocks`
--

CREATE TABLE blocks (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  blockid int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY userfriend (userid,blockid)
) TYPE=MyISAM;

--
-- Table structure for table `categories`
--

CREATE TABLE categories (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(30) NOT NULL default '',
  image varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `comments`
--

CREATE TABLE comments (
  id int(10) unsigned NOT NULL auto_increment,
  user int(10) unsigned NOT NULL default '0',
  torrent int(10) unsigned NOT NULL default '0',
  added datetime NOT NULL default '0000-00-00 00:00:00',
  text text NOT NULL,
  ori_text text NOT NULL,
  editedby int(10) unsigned NOT NULL default '0',
  editedat datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id),
  KEY user (user),
  KEY torrent (torrent)
) TYPE=MyISAM;

--
-- Table structure for table `countries`
--

CREATE TABLE countries (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(50) default NULL,
  flagpic varchar(50) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `files`
--

CREATE TABLE files (
  id int(10) unsigned NOT NULL auto_increment,
  torrent int(10) unsigned NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  size bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY torrent (torrent)
) TYPE=MyISAM;

--
-- Table structure for table `forums`
--

CREATE TABLE forums (
  sort tinyint(3) unsigned NOT NULL default '0',
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(60) NOT NULL default '',
  description varchar(200) default NULL,
  minclassread tinyint(3) unsigned NOT NULL default '0',
  minclasswrite tinyint(3) unsigned NOT NULL default '0',
  postcount int(10) unsigned NOT NULL default '0',
  topiccount int(10) unsigned NOT NULL default '0',
  minclasscreate tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `friends`
--

CREATE TABLE friends (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  friendid int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY userfriend (userid,friendid)
) TYPE=MyISAM;

--
-- Table structure for table `messages`
--

CREATE TABLE messages (
  id int(10) unsigned NOT NULL auto_increment,
  sender int(10) unsigned NOT NULL default '0',
  receiver int(10) unsigned NOT NULL default '0',
  added datetime default NULL,
  msg text,
  unread enum('yes','no') NOT NULL default 'yes',
  poster bigint(20) unsigned NOT NULL default '0',
  location enum('in','out','both') NOT NULL default 'in',
  PRIMARY KEY  (id),
  KEY receiver (receiver)
) TYPE=MyISAM;

--
-- Table structure for table `news`
--

CREATE TABLE news (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(11) NOT NULL default '0',
  added datetime NOT NULL default '0000-00-00 00:00:00',
  body text NOT NULL,
  PRIMARY KEY  (id),
  KEY added (added)
) TYPE=MyISAM;

--
-- Table structure for table `peers`
--

CREATE TABLE peers (
  id int(10) unsigned NOT NULL auto_increment,
  torrent int(10) unsigned NOT NULL default '0',
  peer_id varchar(20) binary NOT NULL default '',
  ip varchar(64) NOT NULL default '',
  port smallint(5) unsigned NOT NULL default '0',
  uploaded bigint(20) unsigned NOT NULL default '0',
  downloaded bigint(20) unsigned NOT NULL default '0',
  to_go bigint(20) unsigned NOT NULL default '0',
  seeder enum('yes','no') NOT NULL default 'no',
  started datetime NOT NULL default '0000-00-00 00:00:00',
  last_action datetime NOT NULL default '0000-00-00 00:00:00',
  connectable enum('yes','no') NOT NULL default 'yes',
  userid int(10) unsigned NOT NULL default '0',
  agent varchar(60) NOT NULL default '',
  finishedat int(10) unsigned NOT NULL default '0',
  downloadoffset bigint(20) unsigned NOT NULL default '0',
  uploadoffset bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY torrent_peer_id (torrent,peer_id),
  KEY torrent (torrent),
  KEY torrent_seeder (torrent,seeder),
  KEY last_action (last_action),
  KEY connectable (connectable),
  KEY userid (userid)
) TYPE=MyISAM;

--
-- Table structure for table `pollanswers`
--

CREATE TABLE pollanswers (
  id int(10) unsigned NOT NULL auto_increment,
  pollid int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  selection tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY pollid (pollid),
  KEY selection (selection),
  KEY userid (userid)
) TYPE=MyISAM;

--
-- Table structure for table `polls`
--

CREATE TABLE polls (
  id int(10) unsigned NOT NULL auto_increment,
  added datetime NOT NULL default '0000-00-00 00:00:00',
  question varchar(255) NOT NULL default '',
  option0 varchar(40) NOT NULL default '',
  option1 varchar(40) NOT NULL default '',
  option2 varchar(40) NOT NULL default '',
  option3 varchar(40) NOT NULL default '',
  option4 varchar(40) NOT NULL default '',
  option5 varchar(40) NOT NULL default '',
  option6 varchar(40) NOT NULL default '',
  option7 varchar(40) NOT NULL default '',
  option8 varchar(40) NOT NULL default '',
  option9 varchar(40) NOT NULL default '',
  option10 varchar(40) NOT NULL default '',
  option11 varchar(40) NOT NULL default '',
  option12 varchar(40) NOT NULL default '',
  option13 varchar(40) NOT NULL default '',
  option14 varchar(40) NOT NULL default '',
  option15 varchar(40) NOT NULL default '',
  option16 varchar(40) NOT NULL default '',
  option17 varchar(40) NOT NULL default '',
  option18 varchar(40) NOT NULL default '',
  option19 varchar(40) NOT NULL default '',
  sort enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `posts`
--

CREATE TABLE posts (
  id int(10) unsigned NOT NULL auto_increment,
  topicid int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  added datetime default NULL,
  body text,
  editedby int(10) unsigned NOT NULL default '0',
  editedat datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id),
  KEY topicid (topicid),
  KEY userid (userid),
  FULLTEXT KEY body (body)
) TYPE=MyISAM;

--
-- Table structure for table `readposts`
--

CREATE TABLE readposts (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  topicid int(10) unsigned NOT NULL default '0',
  lastpostread int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY userid (id),
  KEY topicid (topicid)
) TYPE=MyISAM;

--
-- Table structure for table `sitelog`
--

CREATE TABLE sitelog (
  id int(10) unsigned NOT NULL auto_increment,
  added datetime default NULL,
  txt text,
  PRIMARY KEY  (id),
  KEY added (added)
) TYPE=MyISAM;

--
-- Table structure for table `stylesheets`
--

CREATE TABLE stylesheets (
  id int(10) unsigned NOT NULL auto_increment,
  uri varchar(255) NOT NULL default '',
  name varchar(64) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `topics`
--

CREATE TABLE topics (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  subject varchar(40) default NULL,
  locked enum('yes','no') NOT NULL default 'no',
  forumid int(10) unsigned NOT NULL default '0',
  lastpost int(10) unsigned NOT NULL default '0',
  sticky enum('yes','no') NOT NULL default 'no',
  views int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY userid (userid),
  KEY subject (subject),
  KEY lastpost (lastpost)
) TYPE=MyISAM;

--
-- Table structure for table `torrents`
--

CREATE TABLE torrents (
  id int(10) unsigned NOT NULL auto_increment,
  info_hash varchar(20) binary NOT NULL default '',
  name varchar(255) NOT NULL default '',
  filename varchar(255) NOT NULL default '',
  save_as varchar(255) NOT NULL default '',
  search_text text NOT NULL,
  descr text NOT NULL,
  ori_descr text NOT NULL,
  category int(10) unsigned NOT NULL default '0',
  size bigint(20) unsigned NOT NULL default '0',
  added datetime NOT NULL default '0000-00-00 00:00:00',
  type enum('single','multi') NOT NULL default 'single',
  numfiles int(10) unsigned NOT NULL default '0',
  comments int(10) unsigned NOT NULL default '0',
  views int(10) unsigned NOT NULL default '0',
  hits int(10) unsigned NOT NULL default '0',
  times_completed int(10) unsigned NOT NULL default '0',
  leechers int(10) unsigned NOT NULL default '0',
  seeders int(10) unsigned NOT NULL default '0',
  last_action datetime NOT NULL default '0000-00-00 00:00:00',
  visible enum('yes','no') NOT NULL default 'yes',
  banned enum('yes','no') NOT NULL default 'no',
  owner int(10) unsigned NOT NULL default '0',
  numratings int(10) unsigned NOT NULL default '0',
  ratingsum int(10) unsigned NOT NULL default '0',
  nfo text NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY info_hash (info_hash),
  KEY owner (owner),
  KEY visible (visible),
  KEY category_visible (category,visible),
  FULLTEXT KEY ft_search (search_text,ori_descr)
) TYPE=MyISAM;

--
-- Table structure for table `users`
--

CREATE TABLE users (
  id int(10) unsigned NOT NULL auto_increment,
  username varchar(40) NOT NULL default '',
  old_password varchar(40) NOT NULL default '',
  passhash varchar(32) NOT NULL default '',
  secret varchar(20) binary NOT NULL default '',
  email varchar(80) NOT NULL default '',
  status enum('pending','confirmed') NOT NULL default 'pending',
  added datetime NOT NULL default '0000-00-00 00:00:00',
  last_login datetime NOT NULL default '0000-00-00 00:00:00',
  last_access datetime NOT NULL default '0000-00-00 00:00:00',
  editsecret varchar(20) binary NOT NULL default '',
  privacy enum('strong','normal','low') NOT NULL default 'normal',
  stylesheet int(10) default '1',
  info text,
  acceptpms enum('yes','friends','no') NOT NULL default 'yes',
  ip varchar(15) NOT NULL default '',
  class tinyint(3) unsigned NOT NULL default '0',
  avatar varchar(100) NOT NULL default '',
  uploaded bigint(20) unsigned NOT NULL default '0',
  downloaded bigint(20) unsigned NOT NULL default '0',
  title varchar(30) NOT NULL default '',
  country int(10) unsigned NOT NULL default '0',
  notifs varchar(100) NOT NULL default '',
  modcomment text NOT NULL,
  enabled enum('yes','no') NOT NULL default 'yes',
  avatars enum('yes','no') NOT NULL default 'yes',
  donor enum('yes','no') NOT NULL default 'no',
  warned enum('yes','no') NOT NULL default 'no',
  warneduntil datetime NOT NULL default '0000-00-00 00:00:00',
  torrentsperpage int(3) unsigned NOT NULL default '0',
  topicsperpage int(3) unsigned NOT NULL default '0',
  postsperpage int(3) unsigned NOT NULL default '0',
  deletepms enum('yes','no') NOT NULL default 'yes',
  savepms enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (id),
  UNIQUE KEY username (username),
  KEY status_added (status,added),
  KEY ip (ip),
  KEY uploaded (uploaded),
  KEY downloaded (downloaded),
  KEY country (country),
  KEY last_access (last_access),
  KEY enabled (enabled),
  KEY warned (warned)
) TYPE=MyISAM;

