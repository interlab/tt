-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Янв 22 2019 г., 11:06
-- Версия сервера: 5.7.22-log
-- Версия PHP: 7.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `ttpmr`
--

-- --------------------------------------------------------

--
-- Структура таблицы `addedrequests`
--

CREATE TABLE `addedrequests` (
  `id` int(10) UNSIGNED NOT NULL,
  `requestid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `userid` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `avps`
--

CREATE TABLE `avps` (
  `arg` varchar(20) NOT NULL DEFAULT '',
  `value_s` mediumtext NOT NULL,
  `value_i` int(11) NOT NULL DEFAULT '0',
  `value_u` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `bans`
--

CREATE TABLE `bans` (
  `id` int(10) UNSIGNED NOT NULL,
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `addedby` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `comment` varchar(255) NOT NULL DEFAULT '',
  `first` int(11) DEFAULT NULL,
  `last` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `sort_index` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `image` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `sort_index`, `image`) VALUES
(1, 'Games', 1, 'games.jpg'),
(2, 'Console', 2, 'games.jpg'),
(3, 'Applications', 3, 'apps.jpg'),
(5, 'Movies', 5, 'movies.jpg'),
(6, 'Music', 6, 'music.jpg'),
(7, 'Mac', 7, 'music.jpg'),
(8, 'Comics', 8, 'comics.jpg'),
(9, 'Anime', 9, 'anime.jpg'),
(10, 'TV', 10, 'tv.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `censor`
--

CREATE TABLE `censor` (
  `word` varchar(10) NOT NULL DEFAULT '',
  `censor` varchar(10) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `censor`
--

INSERT INTO `censor` (`word`, `censor`) VALUES
('fuck', 'f**k');

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `user` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `torrent` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `text` mediumtext NOT NULL,
  `ori_text` mediumtext NOT NULL,
  `news` int(10) NOT NULL DEFAULT '0',
  `nzb` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `countries`
--

CREATE TABLE `countries` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `flagpic` varchar(50) DEFAULT NULL,
  `domain` char(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `countries`
--

INSERT INTO `countries` (`id`, `name`, `flagpic`, `domain`) VALUES
(1, 'Sweden', 'sweden.gif', 'SE'),
(2, 'United States of America', 'usa.gif', 'US'),
(3, 'Russia', 'russia.gif', 'RU'),
(4, 'Finland', 'finland.gif', 'FI'),
(5, 'Canada', 'canada.gif', 'CA'),
(6, 'France', 'france.gif', 'FR'),
(7, 'Germany', 'germany.gif', 'DE'),
(8, 'China', 'china.gif', 'CN'),
(9, 'Italy', 'italy.gif', 'IT'),
(10, 'Denmark', 'denmark.gif', 'DK'),
(11, 'Norway', 'norway.gif', 'NO'),
(12, 'United Kingdom', 'uk.gif', 'UK'),
(13, 'Ireland', 'ireland.gif', 'IE'),
(14, 'Poland', 'poland.gif', 'PL'),
(15, 'Netherlands', 'netherlands.gif', 'NL'),
(16, 'Belgium', 'belgium.gif', 'BE'),
(17, 'Japan', 'japan.gif', 'JP'),
(18, 'Brazil', 'brazil.gif', 'BR'),
(19, 'Argentina', 'argentina.gif', 'AR'),
(20, 'Australia', 'australia.gif', 'AU'),
(21, 'New Zealand', 'newzealand.gif', 'NZ'),
(23, 'Spain', 'spain.gif', 'ES'),
(24, 'Portugal', 'portugal.gif', 'PT'),
(25, 'Mexico', 'mexico.gif', 'MX'),
(26, 'Singapore', 'singapore.gif', 'SG'),
(29, 'South Africa', 'southafrica.gif', 'ZA'),
(30, 'South Korea', 'southkorea.gif', 'KR'),
(31, 'Jamaica', 'jamaica.gif', 'JM'),
(32, 'Luxembourg', 'luxembourg.gif', 'LU'),
(33, 'Hong Kong', 'hongkong.gif', 'HK'),
(34, 'Belize', 'belize.gif', 'BZ'),
(35, 'Algeria', 'algeria.gif', 'DZ'),
(36, 'Angola', 'angola.gif', 'AO'),
(37, 'Austria', 'austria.gif', 'AT'),
(38, 'Yugoslavia', 'yugoslavia.gif', 'YU'),
(39, 'Western Samoa', 'westernsamoa.gif', 'WS'),
(40, 'Malaysia', 'malaysia.gif', 'MY'),
(41, 'Dominican Republic', 'dominicanrep.gif', 'DO'),
(42, 'Greece', 'greece.gif', 'GR'),
(43, 'Guatemala', 'guatemala.gif', 'GT'),
(44, 'Israel', 'israel.gif', 'IL'),
(45, 'Pakistan', 'pakistan.gif', 'PK'),
(46, 'Czech Republic', 'czechrep.gif', 'CZ'),
(47, 'Serbia', 'serbia.gif', 'YU'),
(48, 'Seychelles', 'seychelles.gif', 'SC'),
(49, 'Taiwan', 'taiwan.gif', 'TW'),
(50, 'Puerto Rico', 'puertorico.gif', 'PR'),
(51, 'Chile', 'chile.gif', 'CL'),
(52, 'Cuba', 'cuba.gif', 'CU'),
(53, 'Congo', 'congo.gif', 'CG'),
(54, 'Afghanistan', 'afghanistan.gif', 'AF'),
(55, 'Turkey', 'turkey.gif', 'TR'),
(56, 'Uzbekistan', 'uzbekistan.gif', 'UZ'),
(57, 'Switzerland', 'switzerland.gif', 'CH'),
(58, 'Kiribati', 'kiribati.gif', 'KI'),
(59, 'Philippines', 'philippines.gif', 'PH'),
(60, 'Burkina Faso', 'burkinafaso.gif', 'BF'),
(61, 'Nigeria', 'nigeria.gif', 'NG'),
(62, 'Iceland', 'iceland.gif', 'IS'),
(63, 'Nauru', 'nauru.gif', 'NR'),
(64, 'Slovenia', 'slovenia.gif', 'SI'),
(65, 'Albania', 'albania.gif', 'AL'),
(66, 'Turkmenistan', 'turkmenistan.gif', 'TM'),
(67, 'Bosnia Herzegovina', 'bosniaherzegovina.gif', 'BA'),
(68, 'Andorra', 'andorra.gif', 'AD'),
(69, 'Lithuania', 'lithuania.gif', 'LT'),
(70, 'India', 'india.gif', 'IN'),
(71, 'Netherlands Antilles', 'nethantilles.gif', 'AN'),
(72, 'Ukraine', 'ukraine.gif', 'UA'),
(73, 'Venezuela', 'venezuela.gif', 'VE'),
(74, 'Hungary', 'hungary.gif', 'HU'),
(75, 'Romania', 'romania.gif', 'RO'),
(76, 'Vanuatu', 'vanuatu.gif', 'VU'),
(77, 'Vietnam', 'vietnam.gif', 'VN'),
(78, 'Trinidad & Tobago', 'trinidadandtobago.gif', 'TT'),
(79, 'Honduras', 'honduras.gif', 'HN'),
(80, 'Kyrgyzstan', 'kyrgyzstan.gif', 'KG'),
(81, 'Ecuador', 'ecuador.gif', 'EC'),
(82, 'Bahamas', 'bahamas.gif', 'BS'),
(83, 'Peru', 'peru.gif', 'PE'),
(84, 'Cambodia', 'cambodia.gif', 'KH'),
(85, 'Barbados', 'barbados.gif', 'BB'),
(86, 'Bangladesh', 'bangladesh.gif', 'BD'),
(87, 'Laos', 'laos.gif', 'LA'),
(88, 'Uruguay', 'uruguay.gif', 'UY'),
(89, 'Antigua Barbuda', 'antiguabarbuda.gif', 'AG'),
(90, 'Paraguay', 'paraguay.gif', 'PY'),
(92, 'Union of Soviet Socialist Republics', 'ussr.gif', 'SU'),
(93, 'Thailand', 'thailand.gif', 'TH'),
(94, 'Senegal', 'senegal.gif', 'SN'),
(95, 'Togo', 'togo.gif', 'TG'),
(96, 'North Korea', 'northkorea.gif', 'KP'),
(97, 'Croatia', 'croatia.gif', 'HR'),
(98, 'Estonia', 'estonia.gif', 'EE'),
(99, 'Colombia', 'colombia.gif', 'CO'),
(100, 'England', 'england.gif', 'GB'),
(101, 'Egypt', 'egypt.gif', 'EG');

-- --------------------------------------------------------

--
-- Структура таблицы `dbbackup`
--

CREATE TABLE `dbbackup` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `added` date DEFAULT '0000-00-00',
  `day` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `downloaded`
--

CREATE TABLE `downloaded` (
  `torrent` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `user` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `faq`
--

CREATE TABLE `faq` (
  `id` int(10) NOT NULL,
  `type` set('categ','item') NOT NULL DEFAULT 'item',
  `question` mediumtext NOT NULL,
  `answer` mediumtext NOT NULL,
  `flag` set('0','1','2','3') NOT NULL DEFAULT '1',
  `categ` int(10) NOT NULL DEFAULT '0',
  `order` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `faq`
--

INSERT INTO `faq` (`id`, `type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES
(1, 'categ', 'Site information', '', '1', 0, 1),
(2, 'categ', 'User information', '', '1', 0, 2),
(3, 'categ', 'Stats', '', '1', 0, 3),
(4, 'categ', 'Uploading', '', '1', 0, 4),
(5, 'categ', 'Downloading', '', '1', 0, 5),
(6, 'categ', 'How can I improve my download speed?', '', '1', 0, 6),
(7, 'categ', 'My ISP uses a transparent proxy. What should I do?', '', '1', 0, 7),
(8, 'categ', 'Why can\'t I connect? Is the site blocking me?', '', '1', 0, 8),
(9, 'categ', 'What if I can\'t find the answer to my problem here?', '', '1', 0, 9),
(10, 'item', 'What is this bittorrent all about anyway? How do I get the files?', 'Check out <a class=altlink href=\"http://www.btfaq.com/\">Brian\'s BitTorrent FAQ and Guide</a>', '1', 1, 1),
(13, 'item', 'I registered an account but did not receive the confirmation e-mail!', 'You can use <a class=altlink href=account-delete.php>this form</a> to delete the account so you can re-register.\r\nNote though that if you didn\'t receive the email the first time it will probably not\r\nsucceed the second time either so you should really try another email address.', '1', 2, 1),
(14, 'item', 'I\'ve lost my user name or password! Can you send it to me?', 'Please use <a class=altlink href=account-recover.php>this form</a> to have the login details mailed back to you.', '1', 2, 2),
(15, 'item', 'Can you rename my account?', 'We do not rename accounts. Please create a new one. (Use <a href=delacct.php class=altlink>this form</a> to\r\ndelete your present account.)', '1', 2, 3),
(16, 'item', 'Can you delete my (confirmed) account?', 'You can do it yourself by using <a href=account-delete.php class=altlink>this form</a>.', '1', 2, 4),
(17, 'item', 'So, what\'s MY ratio?', 'Click on your <a class=altlink href=account.php>profile</a>, then on your user name (at the top).<br>\r\n<br>\r\nIt\'s important to distinguish between your overall ratio and the individual ratio on each torrent\r\nyou may be seeding or leeching. The overall ratio takes into account the total uploaded and downloaded\r\nfrom your account since you joined the site. The individual ratio takes into account those values for each torrent.<br>\r\n<br>\r\nYou may see two symbols instead of a number: &quot;Inf.&quot;, which is just an abbreviation for Infinity, and\r\nmeans that you have downloaded 0 bytes while uploading a non-zero amount (ul/dl becomes infinity); &quot;---&quot;,\r\nwhich should be read as &quot;non-available&quot;, and shows up when you have both downloaded and uploaded 0 bytes\r\n(ul/dl = 0/0 which is an indeterminate amount).', '1', 2, 5),
(18, 'item', 'Why is my IP displayed on my details page?', 'Only you and the site moderators can view your IP address and email. Regular users do not see that information.', '1', 2, 6),
(19, 'item', 'Help! I cannot login!?', 'This problem sometimes occurs with MSIE. Close all Internet Explorer windows and open Internet Options in the control panel. Click the Delete Cookies button. You should now be able to login.\r\n', '1', 2, 7),
(20, 'item', 'My IP address is dynamic. How do I stay logged in?', 'You do not have to anymore. All you have to do is make sure you are logged in with your actual\r\nIP when starting a torrent session. After that, even if the IP changes mid-session,\r\nthe seeding or leeching will continue and the statistics will update without any problem.', '1', 2, 8),
(21, 'item', 'Why is my port number reported as \"---\"? (And why should I care?)', 'The tracker has determined that you are firewalled or NATed and cannot accept incoming connections.\r\n<br>\r\n<br>\r\nThis means that other peers in the swarm will be unable to connect to you, only you to them. Even worse,\r\nif two peers are both in this state they will not be able to connect at all. This has obviously a\r\ndetrimental effect on the overall speed.\r\n<br>\r\n<br>\r\nThe way to solve the problem involves opening the ports used for incoming connections\r\n(the same range you defined in your client) on the firewall and/or configuring your\r\nNAT server to use a basic form of NAT\r\nfor that range instead of NAPT (the actual process differs widely between different router models.\r\nCheck your router documentation and/or support forum. You will also find lots of information on the\r\nsubject at <a class=altlink href=\"http://portforward.com/\">PortForward</a>).', '1', 2, 9),
(27, 'item', 'Most common reason for stats not updating', '<ul>\r\n<li>The user is cheating. (a.k.a. &quot;Summary Ban&quot;)</li>\r\n<li>The server is overloaded and unresponsive. Just try to keep the session open until the server responds again. (Flooding the server with consecutive manual updates is not recommended.)</li>\r\n</ul>', '1', 3, 1),
(28, 'item', 'Best practices', '<ul>\r\n<li>If a torrent you are currently leeching/seeding is not listed on your profile, just wait or force a manual update.</li>\r\n<li>Make sure you exit your client properly, so that the tracker receives &quot;event=completed&quot;.</li>\r\n<li>If the tracker is down, do not stop seeding. As long as the tracker is back up before you exit the client the stats should update properly.</li>\r\n</ul>', '1', 3, 2),
(29, 'item', 'May I use any bittorrent client?', 'Yes. The tracker now updates the stats correctly for all bittorrent clients. However, we still recommend\r\nthat you <b>avoid</b> the following clients:<br>\r\n<br>\r\n? BitTorrent++,<br>\r\n? Nova Torrent,<br>\r\n? TorrentStorm.<br>\r\n<br>\r\nThese clients do not report correctly to the tracker when canceling/finishing a torrent session.\r\nIf you use them then a few MB may not be counted towards\r\nthe stats near the end, and torrents may still be listed in your profile for some time after you have closed the client.<br>\r\n<br>\r\nAlso, clients in alpha or beta version should be avoided.', '1', 3, 3),
(30, 'item', 'Why is a torrent I\'m leeching/seeding listed several times in my profile?', 'If for some reason (e.g. pc crash, or frozen client) your client exits improperly and you restart it,\r\nit will have a new peer_id, so it will show as a new torrent. The old one will never receive a &quot;event=completed&quot;\r\nor &quot;event=stopped&quot; and will be listed until some tracker timeout. Just ignore it, it will eventually go away.', '1', 3, 4),
(31, 'item', 'I\'ve finished or cancelled a torrent. Why is it still listed in my profile?', 'Some clients, notably TorrentStorm and Nova Torrent, do not report properly to the tracker when canceling or finishing a torrent.\r\nIn that case the tracker will keep waiting for some message - and thus listing the torrent as seeding or leeching - until some\r\ntimeout occurs. Just ignore it, it will eventually go away.', '1', 3, 5),
(32, 'item', 'Why do I sometimes see torrents I\'m not leeching in my profile!?', 'When a torrent is first started, the tracker uses the IP to identify the user. Therefore the torrent will\r\nbecome associated with the user <i>who last accessed the site</i> from that IP. If you share your IP in some\r\nway (you are behind NAT/ICS, or using a proxy), and some of the persons you share it with are also users,\r\nyou may occasionally see their torrents listed in your profile. (If they start a torrent session from that\r\nIP and you were the last one to visit the site the torrent will be associated with you). Note that now\r\ntorrents listed in your profile will always count towards your total stats.', '2', 3, 6),
(34, 'item', 'How does NAT/ICS change the picture?', 'This is a very particular case in that all computers in the LAN will appear to the outside world as having the same IP. We must distinguish\r\nbetween two cases:<br>\r\n<br>\r\n<b>1.</b> <i>You are the single  user in the LAN</i><br>\r\n<br>\r\nYou should use the same account in all the computers.<br>\r\n<br>\r\nNote also that in the ICS case it is preferable to run the BT client on the ICS gateway. Clients running on the other computers\r\nwill be unconnectable (their ports will be listed as &quot;---&quot;, as explained elsewhere in the FAQ) unless you specify\r\nthe appropriate services in your ICS configuration (a good explanation of how to do this for Windows XP can be found\r\n<a class=altlink href=\"redirect.php?url=http://www.microsoft.com/downloads/details.aspx?FamilyID=1dcff3ce-f50f-4a34-ae67-cac31ccd7bc9&amp;displaylang=en\">here</a>).\r\nIn the NAT case you should configure different ranges for clients on different computers and create appropriate NAT rules in the router. (Details vary widely from router to router and are outside the scope of this FAQ. Check your router documentation and/or support forum.)<br>\r\n<br>\r\n<br>\r\n<b>2.</b> <i>There are multiple users in the LAN</i><br>\r\n<br>\r\nAt present there is no way of making this setup always work properly.\r\nEach torrent will be associated with the user who last accessed the site from within\r\nthe LAN before the torrent was started.\r\nUnless there is cooperation between the users mixing of statistics is possible.\r\n(User A accesses the site, downloads a .torrent file, but does not start the torrent immediately.\r\nMeanwhile, user B accesses the site. User A then starts the torrent. The torrent will count\r\ntowards user B\'s statistics, not user A\'s.)\r\n<br>\r\n<br>\r\nIt is your LAN, the responsibility is yours. Do not ask us to ban other users\r\nwith the same IP, we will not do that. (Why should we ban <i>him</i> instead of <i>you</i>?)', '1', 3, 8),
(36, 'item', 'Why can\'t I upload torrents?', 'Only specially authorized users (<font color=\"#4040c0\"><b>Uploaders</b></font>) have permission to upload torrents.', '1', 4, 1),
(37, 'item', 'What criteria must I meet before I can join the <font color=\"#4040c0\">Uploader</font> team?', 'You must be able to provide releases that:<br>\r\n? include a proper NFO,<br>\r\n? are genuine scene releases. If it\'s not on <a class=altlink href=\"redir.php?url=http://www.nforce.nl\">NFOrce</a> then forget it! (except music).<br>\r\n? are not older than seven (7) days,<br>\r\n? have all files in original format (usually 14.3 MB RARs),<br>\r\n? you\'ll be able to seed, or make sure are well-seeded, for at least 24 hours.<br>\r\n? you should have atleast 2MBit upload bandwith.<br>\r\n<br>\r\nIf you think you can match these criteria do not hesitate to <a class=altlink href=staff.php>contact</a> one of the administrators.<br>\r\n<b>Remember!</b> Write your application carefully! Be sure to include your UL speed and what kind of stuff you\'re planning to upload.<br>\r\nOnly well written letters with serious intent will be considered.', '1', 4, 2),
(39, 'item', 'How do I use the files I\'ve downloaded?', 'Check out <a class=altlink href=videoformats.php>this guide</a>.', '1', 5, 1),
(40, 'item', 'Downloaded a movie and don\'t know what CAM/TS/TC/SCR means?', 'Check out <a class=altlink href=videoformats.php>this guide.', '1', 5, 2),
(41, 'item', 'Why did an active torrent suddenly disappear?', 'There may be three reasons for this:<br>\r\n(<b>1</b>) The torrent may have been out-of-sync with the site\r\n<a class=altlink href=rules.php>rules</a>.<br>\r\n(<b>2</b>) The uploader may have deleted it because it was a bad release.\r\nA replacement will probably be uploaded to take its place.<br>\r\n(<b>3</b>) Torrents are automatically deleted after 28 days.', '2', 5, 3),
(42, 'item', 'How do I resume a broken download or reseed something?', 'Open the .torrent file. When your client asks you for a location, choose the location of the existing file(s) and it will resume/reseed the torrent.\r\n', '1', 5, 4),
(43, 'item', 'Why do my downloads sometimes stall at 99%?', 'The more pieces you have, the harder it becomes to find peers who have pieces you are missing. That is why downloads sometimes slow down or even stall when there are just a few percent remaining. Just be patient and you will, sooner or later, get the remaining pieces.\r\n', '1', 5, 5),
(44, 'item', 'What are these \"a piece has failed an hash check\" messages?', 'Bittorrent clients check the data they receive for integrity. When a piece fails this check it is\r\nautomatically re-downloaded. Occasional hash fails are a common occurrence, and you shouldn\'t worry.<br>\r\n<br>\r\nSome clients have an (advanced) option/preference to \'kick/ban clients that send you bad data\' or\r\nsimilar. It should be turned on, since it makes sure that if a peer repeatedly sends you pieces that\r\nfail the hash check it will be ignored in the future.', '1', 5, 6),
(45, 'item', 'The torrent is supposed to be 100MB. How come I downloaded 120MB?', 'See the hash fails topic. If your client receives bad data it will have to redownload it, therefore\r\nthe total downloaded may be larger than the torrent size. Make sure the &quot;kick/ban&quot; option is turned on\r\nto minimize the extra downloads.', '1', 5, 7),
(46, 'item', 'Why do I get a \"Not authorized (xx h) - READ THE FAQ\" error?', 'From the time that each <b>new</b> torrent is uploaded to the tracker, there is a period of time that\r\nsome users must wait before they can download it.<br>\r\nThis delay in downloading will only affect users with a low ratio, and users with low upload amounts.<br>\r\n<br>\r\n<table cellspacing=3 cellpadding=0>\r\n <tr>\r\n	<td class=embedded width=\"70\">Ratio below</td>\r\n	<td class=embedded width=\"40\" bgcolor=\"#F5F4EA\"><font color=\"#BB0000\"><div align=\"center\">0.50</div></font></td>\r\n	<td class=embedded width=\"10\">&nbsp;</td>\r\n	<td class=embedded width=\"110\">and/or upload below</td>\r\n	<td class=embedded width=\"40\" bgcolor=\"#F5F4EA\"><div align=\"center\">5.0GB</div></td>\r\n	<td class=embedded width=\"10\">&nbsp;</td>\r\n	<td class=embedded width=\"50\">delay of</td>\r\n	<td class=embedded width=\"40\" bgcolor=\"#F5F4EA\"><div align=\"center\">48h</div></td>\r\n </tr>\r\n <tr>\r\n	<td class=embedded>Ratio below</td>\r\n	<td class=embedded bgcolor=\"#F5F4EA\"><font color=\"#A10000\"><div align=\"center\">0.65</div></font></td>\r\n	<td class=embedded width=\"10\">&nbsp;</td>\r\n	<td class=embedded>and/or upload below</td>\r\n	<td class=embedded bgcolor=\"#F5F4EA\"><div align=\"center\">6.5GB</div></td>\r\n	<td class=embedded width=\"10\">&nbsp;</td>\r\n	<td class=embedded>delay of</td>\r\n	<td class=embedded bgcolor=\"#F5F4EA\"><div align=\"center\">24h</div></td>\r\n </tr>\r\n <tr>\r\n	<td class=embedded>Ratio below</td>\r\n	<td class=embedded bgcolor=\"#F5F4EA\"><font color=\"#880000\"><div align=\"center\">0.80</div></font></td>\r\n	<td class=embedded width=\"10\">&nbsp;</td>\r\n	<td class=embedded>and/or upload below</td>\r\n	<td class=embedded bgcolor=\"#F5F4EA\"><div align=\"center\">8.0GB</div></td>\r\n	<td class=embedded width=\"10\">&nbsp;</td>\r\n	<td class=embedded>delay of</td>\r\n	<td class=embedded bgcolor=\"#F5F4EA\"><div align=\"center\">12h</div></td>\r\n </tr>\r\n <tr>\r\n	<td class=embedded>Ratio below</td>\r\n	<td class=embedded bgcolor=\"#F5F4EA\"><font color=\"#6E0000\"><div align=\"center\">0.95</div></font></td>\r\n	<td class=embedded width=\"10\">&nbsp;</td>\r\n	<td class=embedded>and/or upload below</td>\r\n	<td class=embedded bgcolor=\"#F5F4EA\"><div align=\"center\">9.5GB</div></td>\r\n	<td class=embedded width=\"10\">&nbsp;</td>\r\n	<td class=embedded>delay of</td>\r\n	<td class=embedded bgcolor=\"#F5F4EA\"><div align=\"center\">06h</div></td>\r\n </tr>\r\n</table>\r\n<br>\r\n\"<b>And/or</b>\" means any or both. Your delay will be the <b>largest</b> one for which you meet <b>at least</b> one condition.<br>\r\n<br>\r\nThis applies to new users as well, so opening a new account will not help. Note also that this\r\nworks at tracker level, you will be able to grab the .torrent file itself at any time.<br>\r\n<br>\r\n<!--The delay applies only to leeching, not to seeding. If you got the files from any other source and\r\nwish to seed them you may do so at any time irrespectively of your ratio or total uploaded.<br>-->\r\nN.B. Due to some users exploiting the \'no-delay-for-seeders\' policy we had to change it. The delay\r\nnow applies to both seeding and leeching. So if you are subject to a delay and get the files from\r\nsome other source you will not be able to seed them until the delay has elapsed.', '3', 5, 8),
(47, 'item', 'Why do I get a \"rejected by tracker - Port xxxx is blacklisted\" error?', 'Your client is reporting to the tracker that it uses one of the default bittorrent ports\r\n(6881-6889) or any other common p2p port for incoming connections.<br>\r\n<br>\r\nThis tracker does not allow clients to use ports commonly associated with p2p protocols.\r\nThe reason for this is that it is a common practice for ISPs to throttle those ports\r\n(that is, limit the bandwidth, hence the speed). <br>\r\n<br>\r\nThe blocked ports list include, but is not neccessarily limited to, the following:<br>\r\n<br>\r\n<table cellspacing=3 cellpadding=0>\r\n  <tr>\r\n	<td class=embedded width=\"80\">Direct Connect</td>\r\n	<td class=embedded width=\"80\" bgcolor=\"#F5F4EA\"><div align=\"center\">411 - 413</div></td>\r\n  </tr>\r\n  <tr>\r\n	<td class=embedded width=\"80\">Kazaa</td>\r\n	<td class=embedded width=\"80\" bgcolor=\"#F5F4EA\"><div align=\"center\">1214</div></td>\r\n  </tr>\r\n  <tr>\r\n	<td class=embedded width=\"80\">eDonkey</td>\r\n	<td class=embedded width=\"80\" bgcolor=\"#F5F4EA\"><div align=\"center\">4662</div></td>\r\n  </tr>\r\n  <tr>\r\n	<td class=embedded width=\"80\">Gnutella</td>\r\n	<td class=embedded width=\"80\" bgcolor=\"#F5F4EA\"><div align=\"center\">6346 - 6347</div></td>\r\n  </tr>\r\n  <tr>\r\n	<td class=embedded width=\"80\">BitTorrent</td>\r\n	<td class=embedded width=\"80\" bgcolor=\"#F5F4EA\"><div align=\"center\">6881 - 6889</div></td>\r\n </tr>\r\n</table>\r\n<br>\r\nIn order to use use our tracker you must  configure your client to use\r\nany port range that does not contain those ports (a range within the region 49152 through 65535 is preferable,\r\ncf. <a class=altlink href=\"http://www.iana.org/assignments/port-numbers\">IANA</a>). Notice that some clients,\r\nlike Azureus 2.0.7.0 or higher, use a single port for all torrents, while most others use one port per open torrent. The size\r\nof the range you choose should take this into account (typically less than 10 ports wide. There\r\nis no benefit whatsoever in choosing a wide range, and there are possible security implications). <br>\r\n<br>\r\nThese ports are used for connections between peers, not client to tracker.\r\nTherefore this change will not interfere with your ability to use other trackers (in fact it\r\nshould <i>increase</i> your speed with torrents from any tracker, not just ours). Your client\r\nwill also still be able to connect to peers that are using the standard ports.\r\nIf your client does not allow custom ports to be used, you will have to switch to one that does.<br>\r\n<br>\r\nDo not ask us, or in the forums, which ports you should choose. The more random the choice is the harder\r\nit will be for ISPs to catch on to us and start limiting speeds on the ports we use.\r\nIf we simply define another range ISPs will start throttling that range also. <br>\r\n<br>\r\nFinally, remember to forward the chosen ports in your router and/or open them in your\r\nfirewall, should you have them.', '3', 5, 9),
(48, 'item', 'What\'s this \"IOError - [Errno13] Permission denied\" error?', 'If you just want to fix it reboot your computer, it should solve the problem.\r\nOtherwise read on.<br>\r\n<br>\r\nIOError means Input-Output Error, and that is a file system error, not a tracker one.\r\nIt shows up when your client is for some reason unable to open the partially downloaded\r\ntorrent files. The most common cause is two instances of the client to be running\r\nsimultaneously:\r\nthe last time the client was closed it somehow didn\'t really close but kept running in the\r\nbackground, and is therefore still\r\nlocking the files, making it impossible for the new instance to open them.<br>\r\n<br>\r\nA more uncommon occurrence is a corrupted FAT. A crash may result in corruption\r\nthat makes the partially downloaded files unreadable, and the error ensues. Running\r\nscandisk should solve the problem. (Note that this may happen only if you\'re running\r\nWindows 9x - which only support FAT - or NT/2000/XP with FAT formatted hard drives.\r\nNTFS is much more robust and should never permit this problem.)', '3', 5, 10),
(49, 'item', 'What\'s this \"TTL\" in the browse page?', 'The torrent\'s Time To Live, in hours. It means the torrent will be deleted\r\nfrom the tracker after that many hours have elapsed (yes, even if it is still active).\r\nNote that this a maximum value, the torrent may be deleted at any time if it\'s inactive.', '3', 5, 11),
(50, 'item', 'Do not immediately jump on new torrents', 'The download speed mostly depends on the seeder-to-leecher ratio (SLR). Poor download speed is\r\nmainly a problem with new and very popular torrents where the SLR is low.<br>\r\n<br>\r\n(Proselytising sidenote: make sure you remember that you did not enjoy the low speed.\r\n<b>Seed</b> so that others will not endure the same.)<br>\r\n<br>\r\nThere are a couple of things that you can try on your end to improve your speed:<br>\r\n<br>In particular, do not do it if you have a slow connection. The best speeds will be found around the\r\nhalf-life of a torrent, when the SLR will be at its highest. (The downside is that you will not be able to seed\r\nso much. It\'s up to you to balance the pros and cons of this.)', '1', 6, 1),
(51, 'item', 'Limit your upload speed', 'The upload speed affects the download speed in essentially two ways:<br>\r\n<ul>\r\n    <li>Bittorrent peers tend to favour those other peers that upload to them. This means that if A and B\r\n	are leeching the same torrent and A is sending data to B at high speed then B will try to reciprocate.\r\n	So due to this effect high upload speeds lead to high download speeds.</li>\r\n\r\n    <li>Due to the way TCP works, when A is downloading something from B it has to keep telling B that\r\n        it received the data sent to him. (These are called acknowledgements - ACKs -, a sort of &quot;got it!&quot; messages).\r\n        If A fails to do this then B will stop sending data and wait. If A is uploading at full speed there may be no\r\n        bandwidth left for the ACKs and they will be delayed. So due to this effect excessively high upload speeds lead\r\n        to low download speeds.</li>\r\n</ul>\r\n\r\nThe full effect is a combination of the two. The upload should be kept as high as possible while allowing the\r\nACKs to get through without delay. <b>A good thumb rule is keeping the upload at about 80% of the theoretical\r\nupload speed.</b> You will have to fine tune yours to find out what works best for you. (Remember that keeping the\r\nupload high has the additional benefit of helping with your ratio.) <br>\r\n<br>\r\nIf you are running more than one instance of a client it is the overall upload speed that you must take into account.\r\nSome clients (e.g. Azureus) limit global upload speed, others (e.g. Shad0w\'s) do it on a per torrent basis.\r\nKnow your client. The same applies if you are using your connection for anything else (e.g. browsing or ftp),\r\nalways think of the overall upload speed.', '1', 6, 2),
(52, 'item', 'Limit the number of simultaneous connections', 'Some operating systems (like Windows 9x) do not deal well with a large number of connections, and may even crash.\r\nAlso some home routers (particularly when running NAT and/or firewall with stateful inspection services) tend to become\r\nslow or crash when having to deal with too many connections. There are no fixed values for this, you may try 60 or 100\r\nand experiment with the value. Note that these numbers are additive, if you have two instances of\r\na client running the numbers add up.', '1', 6, 3),
(53, 'item', 'Limit the number of simultaneous uploads', 'Isn\'t this the same as above? No. Connections limit the number of peers your client is talking to and/or\r\ndownloading from. Uploads limit the number of peers your client is actually uploading to. The ideal number is\r\ntypically much lower than the number of connections, and highly dependent on your (physical) connection.', '1', 6, 4),
(54, 'item', 'Just give it some time', 'As explained above peers favour other peers that upload to them. When you start leeching a new torrent you have\r\nnothing to offer to other peers and they will tend to ignore you. This makes the starts slow, in particular if,\r\nby change, the peers you are connected to include few or no seeders. The download speed should increase as soon\r\nas you have some pieces to share.', '1', 6, 5),
(55, 'item', 'Why is my browsing so slow while leeching?', 'Your download speed is always finite. If you are a peer in a fast torrent it will almost certainly saturate your\r\ndownload bandwidth, and your browsing will suffer. At the moment there is no client that allows you to limit the\r\ndownload speed, only the upload. You will have to use a third-party solution,\r\nsuch as <a class=altlink href=\"redir.php?url=http://www.netlimiter.com/\">NetLimiter</a>.<br>\r\n<br>\r\nBrowsing was used just as an example, the same would apply to gaming, IMing, etc...', '1', 6, 6),
(56, 'item', 'What is a proxy?', 'Basically a middleman. When you are browsing a site through a proxy your requests are sent to the proxy and the proxy\r\nforwards them to the site instead of you connecting directly to the site. There are several classifications\r\n(the terminology is far from standard):<br>\r\n<br>\r\n\r\n\r\n<table cellspacing=3 cellpadding=0>\r\n <tr>\r\n	<td class=embedded valign=\"top\" bgcolor=\"#F5F4EA\" width=\"100\">&nbsp;Transparent</td>\r\n	<td class=embedded width=\"10\">&nbsp;</td>\r\n	<td class=embedded valign=\"top\">A transparent proxy is one that needs no configuration on the clients. It works by automatically redirecting all port 80 traffic to the proxy. (Sometimes used as synonymous for non-anonymous.)</td>\r\n </tr>\r\n <tr>\r\n	<td class=embedded valign=\"top\" bgcolor=\"#F5F4EA\">&nbsp;Explicit/Voluntary</td>\r\n	<td class=embedded width=\"10\">&nbsp;</td>\r\n	<td class=embedded valign=\"top\">Clients must configure their browsers to use them.</td>\r\n </tr>\r\n <tr>\r\n	<td class=embedded valign=\"top\" bgcolor=\"#F5F4EA\">&nbsp;Anonymous</td>\r\n	<td class=embedded width=\"10\">&nbsp;</td>\r\n	<td class=embedded valign=\"top\">The proxy sends no client identification to the server. (HTTP_X_FORWARDED_FOR header is not sent; the server does not see your IP.)</td>\r\n </tr>\r\n <tr>\r\n	<td class=embedded valign=\"top\" bgcolor=\"#F5F4EA\">&nbsp;Highly Anonymous</td>\r\n	<td class=embedded width=\"10\">&nbsp;</td>\r\n	<td class=embedded valign=\"top\">The proxy sends no client nor proxy identification to the server. (HTTP_X_FORWARDED_FOR, HTTP_VIA and HTTP_PROXY_CONNECTION headers are not sent; the server doesn\'t see your IP and doesn\'t even know you\'re using a proxy.)</td>\r\n </tr>\r\n <tr>\r\n	<td class=embedded valign=\"top\" bgcolor=\"#F5F4EA\">&nbsp;Public</td>\r\n	<td class=embedded width=\"10\">&nbsp;</td>\r\n	<td class=embedded valign=\"top\">(Self explanatory)</td>\r\n </tr>\r\n</table>\r\n<br>\r\nA transparent proxy may or may not be anonymous, and there are several levels of anonymity.', '1', 7, 1),
(57, 'item', 'How do I find out if I\'m behind a (transparent/anonymous) proxy?', 'Try <a href=http://proxyjudge.org class=\"altlink\">ProxyJudge</a>. It lists the HTTP headers that the server where it is running\r\nreceived from you. The relevant ones are HTTP_CLIENT_IP, HTTP_X_FORWARDED_FOR and REMOTE_ADDR.<br>\r\n<br>\r\n<br>\r\n<b>Why is my port listed as &quot;---&quot; even though I\'m not NAT/Firewalled?</b><a name=\"prox3\"></a><br>\r\n<br>\r\nThe tracker is quite smart at finding your real IP, but it does need the proxy to send the HTTP header\r\nHTTP_X_FORWARDED_FOR. If your ISP\'s proxy does not then what happens is that the tracker will interpret the proxy\'s IP\r\naddress as the client\'s IP address. So when you login and the tracker tries to connect to your client to see if you are\r\nNAT/firewalled it will actually try to connect to the proxy on the port your client reports to be using for\r\nincoming connections. Naturally the proxy will not be listening on that port, the connection will fail and the\r\ntracker will think you are NAT/firewalled.', '1', 7, 2),
(58, 'item', 'Can I bypass my ISP\'s proxy?', 'If your ISP only allows HTTP traffic through port 80 or blocks the usual proxy ports then you would need to use something\r\nlike <a href=http://www.socks.permeo.com>socks</a> and that is outside the scope of this FAQ.<br>\r\n<br>\r\nOtherwise you may try the following:<br>\r\n<ul>\r\n    <li>Choose any public <b>non-anonymous</b> proxy that does <b>not</b> use port 80\r\n	(e.g. from <a href=http://tools.rosinstrument.com/proxy  class=\"altlink\">this</a>,\r\n	<a href=http://www.proxy4free.com/index.html  class=\"altlink\">this</a> or\r\n	<a href=http://www.samair.ru/proxy  class=\"altlink\">this</a> list).</li>\r\n\r\n    <li>Configure your computer to use that proxy. For Windows XP, do <i>Start</i>, <i>Control Panel</i>, <i>Internet Options</i>,\r\n	<i>Connections</i>, <i>LAN Settings</i>, <i>Use a Proxy server</i>, <i>Advanced</i> and type in the IP and port of your chosen\r\n	proxy. Or from Internet Explorer use <i>Tools</i>, <i>Internet Options</i>, ...<br></li>\r\n\r\n    <li>(Facultative) Visit <a href=http://proxyjudge.org  class=\"altlink\">ProxyJudge</a>. If you see an HTTP_X_FORWARDED_FOR in\r\n	the list followed by your IP then everything should be ok, otherwise choose another proxy and try again.<br></li>\r\n\r\n    <li>Visit this site. Hopefully the tracker will now pickup your real IP (check your profile to make sure).</li>\r\n</ul>\r\n<br>\r\nNotice that now you will be doing all your browsing through a public proxy, which are typically quite slow.\r\nCommunications between peers do not use port 80 so their speed will not be affected by this, and should be better than when\r\nyou were &quot;unconnectable&quot;.', '1', 7, 3),
(59, 'item', 'How do I make my bittorrent client use a proxy?', 'Just configure Windows XP as above. When you configure a proxy for Internet Explorer you\r\nre actually configuring a proxy for\r\nall HTTP traffic (thank Microsoft and their &quot;IE as part of the OS policy&quot; ). On the other hand if you use another\r\nbrowser (Opera/Mozilla/Firefox) and configure a proxy there you\'ll be configuring a proxy just for that browser. We don\'t\r\nknow of any BT client that allows a proxy to be specified explicitly.', '1', 7, 4),
(60, 'item', 'Why can\'t I signup from behind a proxy?', 'It is our policy not to allow new accounts to be opened from behind a proxy.', '1', 7, 5),
(62, 'item', 'Maybe my address is blacklisted?', 'The site blocks addresses listed in the (former) <a class=altlink href=\"http://methlabs.org/\">PeerGuardian</a>\r\ndatabase, as well as addresses of banned users. This works at Apache/PHP level, it\'s just a script that\r\nblocks <i>logins</i> from those addresses. It should not stop you from reaching the site. In particular\r\nit does not block lower level protocols, you should be able to ping/traceroute the server even if your\r\naddress is blacklisted. If you cannot then the reason for the problem lies elsewhere.<br>\r\n<br>\r\nIf somehow your address is indeed blocked in the PG database do not contact us about it, it is not our\r\npolicy to open <i>ad hoc</i> exceptions. You should clear your IP with the database maintainers instead.', '1', 8, 1),
(63, 'item', 'Your ISP blocks the site\'s address', '(In first place, it\'s unlikely your ISP is doing so. DNS name resolution and/or network problems are the usual culprits.)\r\n<br>\r\nThere\'s nothing we can do.\r\nYou should contact your ISP (or get a new one). Note that you can still visit the site via a proxy, follow the instructions\r\nin the relevant section. In this case it doesn\'t matter if the proxy is anonymous or not, or which port it listens to.<br>\r\n<br>\r\nNotice that you will always be listed as an &quot;unconnectable&quot; client because the tracker will be unable to\r\ncheck that you\'re capable of accepting incoming connections.', '1', 8, 2),
(65, 'item', 'You can try these:', 'Post in the <a class=\"altlink\" href=\"forums.php\">Forums</a>, by all means. You\'ll find they\r\nare usually a friendly and helpful place,\r\nprovided you follow a few basic guidelines:\r\n<ul>\r\n<li>Make sure your problem is not really in this FAQ. There\'s no point in posting just to be sent\r\nback here.\r\n<li>Before posting read the sticky topics (the ones at the top). Many times new information that\r\nstill hasn\'t been incorporated in the FAQ can be found there.</li>\r\n<li>Help us in helping you. Do not just say \"it doesn\'t work!\". Provide details so that we don\'t\r\nhave to guess or waste time asking. What client do you use? What\'s your OS? What\'s your network setup? What\'s the exact\r\nerror message you get, if any? What are the torrents you are having problems with? The more\r\nyou tell the easiest it will be for us, and the more probable your post will get a reply.</li>\r\n<li>And needless to say: be polite. Demanding help rarely works, asking for it usually does\r\nthe trick.', '1', 9, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE `files` (
  `id` int(10) UNSIGNED NOT NULL,
  `torrent` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `size` bigint(20) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `forumcats`
--

CREATE TABLE `forumcats` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL DEFAULT '',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `forumcats`
--

INSERT INTO `forumcats` (`id`, `name`, `sort`) VALUES
(1, 'Test Cat 1', 1),
(3, 'Test Cat 2', 3),
(4, 'Test Cat 3', 4);

-- --------------------------------------------------------

--
-- Структура таблицы `forum_forums`
--

CREATE TABLE `forum_forums` (
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL DEFAULT '',
  `description` varchar(200) DEFAULT NULL,
  `minclassread` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `minclasswrite` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `category` tinyint(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `forum_forums`
--

INSERT INTO `forum_forums` (`sort`, `id`, `name`, `description`, `minclassread`, `minclasswrite`, `category`) VALUES
(1, 1, 'Example Forum', 'Here is a example forum, you can edit the forums via the control panel', 0, 0, 1),
(0, 2, 'Test Forum 2', 'test2', 0, 0, 3),
(0, 3, 'Test Forum 3', 'test', 0, 0, 4);

-- --------------------------------------------------------

--
-- Структура таблицы `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `topicid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `userid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `added` datetime DEFAULT NULL,
  `body` mediumtext,
  `editedby` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `editedat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `topicid`, `userid`, `added`, `body`, `editedby`, `editedat`) VALUES
(5, 3, 1, '2005-02-27 19:40:38', 'Here is a example post', 0, '0000-00-00 00:00:00'),
(8, 3, 1, '2005-05-18 08:18:22', 'test 2', 1, '2005-11-11 14:40:44'),
(9, 6, 1, '2005-07-11 18:49:27', 'test 3', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `forum_readposts`
--

CREATE TABLE `forum_readposts` (
  `id` int(10) UNSIGNED NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `topicid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `lastpostread` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `forum_topics`
--

CREATE TABLE `forum_topics` (
  `id` int(10) UNSIGNED NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `subject` varchar(40) DEFAULT NULL,
  `locked` enum('yes','no') NOT NULL DEFAULT 'no',
  `forumid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `lastpost` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `moved` enum('yes','no') NOT NULL DEFAULT 'no',
  `sticky` enum('yes','no') NOT NULL DEFAULT 'no',
  `views` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `forum_topics`
--

INSERT INTO `forum_topics` (`id`, `userid`, `subject`, `locked`, `forumid`, `lastpost`, `moved`, `sticky`, `views`) VALUES
(3, 1, 'Test Topic', 'no', 2, 8, 'no', 'no', 15),
(6, 1, 'Test Topic 2', 'no', 3, 9, 'no', 'no', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `guests`
--

CREATE TABLE `guests` (
  `ip` varchar(15) NOT NULL DEFAULT '',
  `time` decimal(20,0) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `languages`
--

CREATE TABLE `languages` (
  `id` int(10) UNSIGNED NOT NULL,
  `uri` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `languages`
--

INSERT INTO `languages` (`id`, `uri`, `name`) VALUES
(1, 'english.lang', 'English'),
(2, 'french.lang', 'French'),
(3, 'dutch.lang', 'Dutch'),
(4, 'portuguese.lang', 'Portuguese'),
(5, 'swedish.lang', 'Swedish'),
(6, 'german.lang', 'German'),
(7, 'bulgarian.lang', 'Bulgarian'),
(8, 'danish.lang', 'Danish'),
(9, 'italian.lang', 'Italian'),
(10, 'lithuanian.lang', 'Lithuanian'),
(11, 'hungarian.lang', 'Hungarian'),
(13, 'russian.lang', 'Russian'),
(14, 'spanish.lang', 'Spanish');

-- --------------------------------------------------------

--
-- Структура таблицы `log`
--

CREATE TABLE `log` (
  `id` int(10) UNSIGNED NOT NULL,
  `added` datetime DEFAULT NULL,
  `txt` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `sender` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `receiver` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `added` datetime DEFAULT NULL,
  `msg` mediumtext,
  `unread` enum('yes','no') NOT NULL DEFAULT 'yes',
  `poster` bigint(20) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE `news` (
  `id` int(2) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `date` varchar(255) NOT NULL DEFAULT '0000-00-00',
  `text` mediumtext NOT NULL,
  `comments` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `news_options`
--

CREATE TABLE `news_options` (
  `max_display` int(3) NOT NULL DEFAULT '0',
  `scrolling` varchar(100) NOT NULL DEFAULT '',
  `archive` char(3) NOT NULL DEFAULT '',
  `comment` char(3) NOT NULL DEFAULT '',
  `titles` char(3) NOT NULL DEFAULT '',
  `subc` varchar(255) NOT NULL DEFAULT '',
  `subs` char(3) NOT NULL DEFAULT '',
  `sspeed` char(3) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `news_options`
--

INSERT INTO `news_options` (`max_display`, `scrolling`, `archive`, `comment`, `titles`, `subc`, `subs`, `sspeed`) VALUES
(3, 'on', 'on', 'on', '2', 'red', '1', '2');

-- --------------------------------------------------------

--
-- Структура таблицы `peers`
--

CREATE TABLE `peers` (
  `id` int(10) UNSIGNED NOT NULL,
  `torrent` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `peer_id` varchar(20) NOT NULL DEFAULT '',
  `ip` varchar(64) NOT NULL DEFAULT '',
  `port` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `uploaded` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `downloaded` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `to_go` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `seeder` enum('yes','no') NOT NULL DEFAULT 'no',
  `started` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_action` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `connectable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `client` varchar(60) NOT NULL DEFAULT '',
  `userid` varchar(32) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `pollanswers`
--

CREATE TABLE `pollanswers` (
  `id` int(10) UNSIGNED NOT NULL,
  `pollid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `userid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `selection` tinyint(3) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `polls`
--

CREATE TABLE `polls` (
  `id` int(10) UNSIGNED NOT NULL,
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `question` varchar(255) NOT NULL DEFAULT '',
  `option0` varchar(40) NOT NULL DEFAULT '',
  `option1` varchar(40) NOT NULL DEFAULT '',
  `option2` varchar(40) NOT NULL DEFAULT '',
  `option3` varchar(40) NOT NULL DEFAULT '',
  `option4` varchar(40) NOT NULL DEFAULT '',
  `option5` varchar(40) NOT NULL DEFAULT '',
  `option6` varchar(40) NOT NULL DEFAULT '',
  `option7` varchar(40) NOT NULL DEFAULT '',
  `option8` varchar(40) NOT NULL DEFAULT '',
  `option9` varchar(40) NOT NULL DEFAULT '',
  `sort` enum('yes','no') NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `polls`
--

INSERT INTO `polls` (`id`, `added`, `question`, `option0`, `option1`, `option2`, `option3`, `option4`, `option5`, `option6`, `option7`, `option8`, `option9`, `sort`) VALUES
(4, '2006-01-01 18:25:58', 'So... how are things?', 'Good', 'Bad', 'Ugly', '', '', '', '', '', '', '', 'yes');

-- --------------------------------------------------------

--
-- Структура таблицы `ratings`
--

CREATE TABLE `ratings` (
  `torrent` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `user` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `rating` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `ratiowarn`
--

CREATE TABLE `ratiowarn` (
  `id` int(10) UNSIGNED NOT NULL,
  `userid` int(11) NOT NULL DEFAULT '0',
  `warned` enum('yes','no') NOT NULL DEFAULT 'no',
  `banned` enum('yes','no') NOT NULL DEFAULT 'no',
  `ratiodate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `warntime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `reports`
--

CREATE TABLE `reports` (
  `id` int(10) UNSIGNED NOT NULL,
  `addedby` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `votedfor` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `votedfor_xtra` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `type` enum('torrent','user','forum','tc_comment') NOT NULL DEFAULT 'torrent',
  `reason` varchar(255) NOT NULL DEFAULT '',
  `dealtby` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dealtwith` tinyint(1) NOT NULL DEFAULT '0',
  `complete` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `requests`
--

CREATE TABLE `requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `request` varchar(225) DEFAULT NULL,
  `descr` mediumtext NOT NULL,
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `cat` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `filled` varchar(75) DEFAULT NULL,
  `filledby` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `rules`
--

CREATE TABLE `rules` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `text` mediumtext NOT NULL,
  `public` enum('yes','no') NOT NULL DEFAULT 'yes',
  `class` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `rules`
--

INSERT INTO `rules` (`id`, `title`, `text`, `public`, `class`) VALUES
(1, 'General rules - Breaking these rules can and will get you banned!', '? We are a English only site, so please only talk in english! \r\n\r\n? Keep your overall ratio at or above 0.5 at all times! \r\n\r\n? Do not defy the moderators expressed wishes! ', 'yes', 0),
(2, 'General Forum Guidelines', '? No aggressive behaviour or flaming in the forums. \r\n? No trashing of other peoples topics (i.e. SPAM). \r\n? No language other than English in the forums. \r\n? No links to warez or crack sites in the forums. \r\n? No serials, CD keys, passwords or cracks in the forums. \r\n? No requesting if the release is over 7 days old. \r\n? No bumping... (All bumped threads will be deleted.) \r\n? No double posting. If you wish to post again, and yours is the last post\r\nin the thread please use the EDIT function, instead of posting a double. \r\n? Please ensure all questions are posted in the correct section!\r\n(eg; Game questions in the Game section, Apps questions in the Apps section. etc.) \r\n? Last, Please read the FAQ before asking any questions!  \r\n', 'yes', 0),
(3, 'Moderating Rules', '? The most important rule!; Use your better judgement! \r\n? Don\'t defy another mod in public, instead send a PM or make a post in the \\\"Site admin\\\". \r\n? Be tolerant! give the user(s) a chance to reform. \r\n? Don\'t act prematurely, Let the users make their mistake and THEN correct them. \r\n? Try correcting any \\\"off topics\\\" rather then closing the thread. \r\n? Move topics rather than locking / deleting them. \r\n? Be tolerant when moderating the Chit-chat section. (give them some slack) \r\n? If you lock a topic, Give a brief explanation as to why you\'re locking it. \r\n? Before banning a user, Send him/her a PM and If they reply, put them on a 2 week trial. \r\n? Don\'t ban a user until he or she has been a member for at least 4 weeks. \r\n? Always state a reason (in the user comment box) as to why the user is being banned. \r\n', 'no', 4);

-- --------------------------------------------------------

--
-- Структура таблицы `shoutbox`
--

CREATE TABLE `shoutbox` (
  `msgid` int(10) UNSIGNED NOT NULL,
  `user` varchar(50) NOT NULL DEFAULT '0',
  `message` mediumtext,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `userid` int(8) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `shoutbox_emoticons`
--

CREATE TABLE `shoutbox_emoticons` (
  `id` int(9) NOT NULL,
  `text` varchar(20) NOT NULL DEFAULT '',
  `image` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `shoutbox_emoticons`
--

INSERT INTO `shoutbox_emoticons` (`id`, `text`, `image`) VALUES
(1, '%-|', 'confused.gif'),
(2, '%-(', 'sigh.gif'),
(3, 'X-|', 'sleep.gif'),
(4, ':-(', 'upset.gif'),
(5, '$-|', 'rolleyes.gif'),
(6, ':mad:', 'mad.gif'),
(7, ':yes:', 'yes.gif'),
(8, ':no:', 'no.gif'),
(9, ':shy:', 'shy.gif'),
(10, ':laugh:', 'laugh.gif'),
(11, ':dead:', 'dead.gif'),
(12, ':cry:', 'cry.gif'),
(13, ':-)', 'smile.gif'),
(14, ':-(', 'sad.gif'),
(15, ';-)', 'smilewinkgrin.gif'),
(16, ':-|', 'none.gif'),
(17, 'B-)', 'cool.gif'),
(18, ':-D', 'biggrin.gif'),
(19, ':-b', 'bigrazz.gif'),
(20, ':-o', 'bigeek.gif'),
(21, ':)', 'smile.gif'),
(22, ':D', 'biggrin.gif'),
(23, ':(', 'sad.gif');

-- --------------------------------------------------------

--
-- Структура таблицы `site2`
--

CREATE TABLE `site2` (
  `maxusers` int(10) NOT NULL DEFAULT '0',
  `maxusersdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `site_settings`
--

CREATE TABLE `site_settings` (
  `donations` decimal(5,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `requireddonations` decimal(5,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `donatepage` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `site_settings`
--

INSERT INTO `site_settings` (`donations`, `requireddonations`, `donatepage`) VALUES
('10.00', '100.00', 'Place your donation infomation text here, paypal worldpay etc etc\r\n<br><br>\r\nHTML code is accepted so you can have paypal donate buttons and what-not\r\n<br><br>\r\nThis text can be edited via the Admin CP under Donation Settings');

-- --------------------------------------------------------

--
-- Структура таблицы `snatched`
--

CREATE TABLE `snatched` (
  `id` int(10) UNSIGNED NOT NULL,
  `torrent` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `torrentid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `userid` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `torrent_name` varchar(255) NOT NULL DEFAULT '',
  `torrent_category` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `port` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `uploaded` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `downloaded` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `to_go` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `seeder` enum('yes','no') NOT NULL DEFAULT 'no',
  `last_action` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `startdat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `completedat` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `connectable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `agent` varchar(60) NOT NULL DEFAULT '',
  `finished` enum('yes','no') NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `stylesheets`
--

CREATE TABLE `stylesheets` (
  `id` int(10) UNSIGNED NOT NULL,
  `uri` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `stylesheets`
--

INSERT INTO `stylesheets` (`id`, `uri`, `name`) VALUES
(1, 'default', 'Default'),
(2, 'moobile_attract', 'Attract'),
(3, 'troots2', 'TorrentRoots');

-- --------------------------------------------------------

--
-- Структура таблицы `torrents`
--

CREATE TABLE `torrents` (
  `id` int(10) UNSIGNED NOT NULL,
  `info_hash` blob,
  `name` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `save_as` varchar(255) NOT NULL DEFAULT '',
  `search_text` mediumtext NOT NULL,
  `descr` mediumtext NOT NULL,
  `ori_descr` mediumtext NOT NULL,
  `category` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `size` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` enum('single','multi') NOT NULL DEFAULT 'single',
  `numfiles` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `comments` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `views` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `hits` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `times_completed` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `leechers` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `seeders` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `last_action` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `visible` enum('yes','no') NOT NULL DEFAULT 'yes',
  `banned` enum('yes','no') NOT NULL DEFAULT 'no',
  `owner` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `numratings` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ratingsum` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `nfo` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `uploadapp`
--

CREATE TABLE `uploadapp` (
  `id` int(10) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `userid` int(10) NOT NULL DEFAULT '0',
  `applied` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `grpacct` tinyint(1) NOT NULL DEFAULT '0',
  `grpname` varchar(100) NOT NULL DEFAULT '',
  `grpdes` varchar(4) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  `comment` longtext NOT NULL,
  `seeding` tinyint(1) NOT NULL DEFAULT '0',
  `othergrps` tinyint(1) NOT NULL DEFAULT '0',
  `seedtime` varchar(100) NOT NULL DEFAULT '',
  `modcomments` mediumtext NOT NULL,
  `votes` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(40) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL DEFAULT '',
  `secret` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(80) NOT NULL DEFAULT '',
  `status` enum('pending','confirmed') NOT NULL DEFAULT 'pending',
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_access` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editsecret` varchar(20) NOT NULL DEFAULT '',
  `privacy` enum('strong','normal','low') NOT NULL DEFAULT 'normal',
  `stylesheet` int(10) DEFAULT '1',
  `language` varchar(20) NOT NULL DEFAULT '1',
  `info` mediumtext,
  `acceptpms` enum('yes','no') NOT NULL DEFAULT 'yes',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `class` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `avatar` varchar(100) NOT NULL DEFAULT '',
  `uploaded` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `downloaded` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `ircnick` varchar(30) NOT NULL DEFAULT '',
  `ircpass` varchar(30) NOT NULL DEFAULT '',
  `title` varchar(30) NOT NULL DEFAULT '',
  `donated` decimal(5,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `country` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `notifs` varchar(100) NOT NULL DEFAULT '',
  `enabled` varchar(10) NOT NULL DEFAULT 'yes',
  `modcomment` varchar(100) DEFAULT NULL,
  `gender` varchar(6) NOT NULL DEFAULT '',
  `client` varchar(25) NOT NULL DEFAULT '',
  `age` int(3) NOT NULL DEFAULT '0',
  `warned` char(3) NOT NULL DEFAULT 'no',
  `signature` varchar(200) NOT NULL DEFAULT '',
  `last_browse` int(11) NOT NULL DEFAULT '0',
  `forumbanned` char(3) NOT NULL DEFAULT 'no',
  `invited_by` int(10) NOT NULL DEFAULT '0',
  `invitees` varchar(100) NOT NULL DEFAULT '',
  `invites` smallint(5) NOT NULL DEFAULT '0',
  `invitedate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tzoffset` smallint(4) NOT NULL DEFAULT '0',
  `commentpm` enum('yes','no') NOT NULL DEFAULT 'yes',
  `dob` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=0;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `secret`, `email`, `status`, `added`, `last_login`, `last_access`, `editsecret`, `privacy`, `stylesheet`, `language`, `info`, `acceptpms`, `ip`, `class`, `avatar`, `uploaded`, `downloaded`, `ircnick`, `ircpass`, `title`, `donated`, `country`, `notifs`, `enabled`, `modcomment`, `gender`, `client`, `age`, `warned`, `signature`, `last_browse`, `forumbanned`, `invited_by`, `invitees`, `invites`, `invitedate`, `tzoffset`, `commentpm`, `dob`) VALUES
(1, 'Admin', '21232f297a57a5a743894a0e4a801fc3', 'ì[Ó+\\Z–/A»HßÎeT;', 'email@address.com', 'confirmed', '2005-07-06 12:03:29', '2006-09-27 21:35:45', '2006-09-29 10:57:44', '', 'strong', 1, '1', NULL, 'no', '86.134.184.202', 5, '', 1070000000, 100000000, '', '', 'System Admin', '0.00', 12, '', 'yes', '', 'Female', 'mine', 10, 'no', 'Site Admin', 1159527454, 'no', 2, '', 5, '0000-00-00 00:00:00', 0, 'no', '0000-00-00');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `addedrequests`
--
ALTER TABLE `addedrequests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pollid` (`id`),
  ADD KEY `userid` (`userid`);

--
-- Индексы таблицы `avps`
--
ALTER TABLE `avps`
  ADD PRIMARY KEY (`arg`);

--
-- Индексы таблицы `bans`
--
ALTER TABLE `bans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `first_last` (`first`,`last`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `torrent` (`torrent`);

--
-- Индексы таблицы `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dbbackup`
--
ALTER TABLE `dbbackup`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `torrent` (`torrent`);

--
-- Индексы таблицы `forumcats`
--
ALTER TABLE `forumcats`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `forum_forums`
--
ALTER TABLE `forum_forums`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topicid` (`topicid`),
  ADD KEY `userid` (`userid`);
ALTER TABLE `forum_posts` ADD FULLTEXT KEY `body` (`body`);

--
-- Индексы таблицы `forum_readposts`
--
ALTER TABLE `forum_readposts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`id`),
  ADD KEY `topicid` (`topicid`);

--
-- Индексы таблицы `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`),
  ADD KEY `subject` (`subject`),
  ADD KEY `lastpost` (`lastpost`);

--
-- Индексы таблицы `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`ip`),
  ADD UNIQUE KEY `IP` (`ip`);

--
-- Индексы таблицы `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added` (`added`);

--
-- Индексы таблицы `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receiver` (`receiver`);

--
-- Индексы таблицы `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `peers`
--
ALTER TABLE `peers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `torrent_peer_id` (`torrent`,`peer_id`),
  ADD KEY `torrent` (`torrent`),
  ADD KEY `torrent_seeder` (`torrent`,`seeder`),
  ADD KEY `last_action` (`last_action`);

--
-- Индексы таблицы `pollanswers`
--
ALTER TABLE `pollanswers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pollid` (`pollid`),
  ADD KEY `selection` (`selection`),
  ADD KEY `userid` (`userid`);

--
-- Индексы таблицы `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`torrent`,`user`),
  ADD KEY `user` (`user`);

--
-- Индексы таблицы `ratiowarn`
--
ALTER TABLE `ratiowarn`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`);

--
-- Индексы таблицы `rules`
--
ALTER TABLE `rules`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `shoutbox`
--
ALTER TABLE `shoutbox`
  ADD PRIMARY KEY (`msgid`),
  ADD KEY `msgid` (`msgid`);

--
-- Индексы таблицы `shoutbox_emoticons`
--
ALTER TABLE `shoutbox_emoticons`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `snatched`
--
ALTER TABLE `snatched`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `torrentid_3` (`torrentid`,`userid`),
  ADD KEY `finished` (`finished`,`torrentid`),
  ADD KEY `torrentid` (`userid`),
  ADD KEY `torrentid_2` (`torrentid`),
  ADD KEY `userid` (`userid`,`torrentid`);

--
-- Индексы таблицы `stylesheets`
--
ALTER TABLE `stylesheets`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `torrents`
--
ALTER TABLE `torrents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `info_hash` (`info_hash`(20)),
  ADD KEY `owner` (`owner`),
  ADD KEY `visible` (`visible`),
  ADD KEY `category_visible` (`category`,`visible`);
ALTER TABLE `torrents` ADD FULLTEXT KEY `ft_search` (`search_text`,`ori_descr`);

--
-- Индексы таблицы `uploadapp`
--
ALTER TABLE `uploadapp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users` (`userid`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `status_added` (`status`,`added`),
  ADD KEY `ip` (`ip`),
  ADD KEY `uploaded` (`uploaded`),
  ADD KEY `downloaded` (`downloaded`),
  ADD KEY `country` (`country`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `addedrequests`
--
ALTER TABLE `addedrequests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `bans`
--
ALTER TABLE `bans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT для таблицы `dbbackup`
--
ALTER TABLE `dbbackup`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT для таблицы `files`
--
ALTER TABLE `files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `forumcats`
--
ALTER TABLE `forumcats`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `forum_forums`
--
ALTER TABLE `forum_forums`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `forum_readposts`
--
ALTER TABLE `forum_readposts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `log`
--
ALTER TABLE `log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `peers`
--
ALTER TABLE `peers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pollanswers`
--
ALTER TABLE `pollanswers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `ratiowarn`
--
ALTER TABLE `ratiowarn`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `rules`
--
ALTER TABLE `rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `shoutbox`
--
ALTER TABLE `shoutbox`
  MODIFY `msgid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `shoutbox_emoticons`
--
ALTER TABLE `shoutbox_emoticons`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `snatched`
--
ALTER TABLE `snatched`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stylesheets`
--
ALTER TABLE `stylesheets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `torrents`
--
ALTER TABLE `torrents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `uploadapp`
--
ALTER TABLE `uploadapp`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
