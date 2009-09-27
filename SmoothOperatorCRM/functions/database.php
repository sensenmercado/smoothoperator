<?
if (!function_exists('mysql_is_table') ) {
	function mysql_is_table($host, $user, $pass, $db, $tbl)
	{
		$result = FALSE;
		$tables = array();
		$link = mysql_connect($host, $user, $pass) or die(mysql_error());
		mysql_select_db($db) or die(mysql_error());
		$q = @mysql_query("SHOW TABLES");
		while ($r = @mysql_fetch_array($q)) { $tables[] = $r[0]; }
		@mysql_free_result($q);
	    // @mysql_close($link);
		if (in_array($tbl, $tables)) { $result =  TRUE; }
		return $result;
	}
}

if (!function_exists('create_missing_tables') ) {
	function create_missing_tables($db_host,$db_user,$db_pass) {
		$link = mysql_connect($db_host, $db_user, $db_pass) or die(mysql_error());
		
		/*======================================================================
									names Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","names")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `names` (
		  `campaignid` int(200) NOT NULL default '0',
		  `phonenumber` varchar(50) NOT NULL default '',
		  `name` varchar(50) NOT NULL default '',
		  `datetime` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`campaignid`,`phonenumber`)
		)";
		
			$result = mysql_query($sql,$link) or die(mysql_error());
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created names Table')";
		  $result=mysql_query($sql, $link);
		
		}




		/*======================================================================
                            Schedule Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","schedule")){
		
		  $sql = "CREATE TABLE `schedule` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `name` varchar(255) default NULL,
		  `description` varchar(255) default NULL,
		  `campaignid` int default NULL,
		  `start_hour` tinyint(2) zerofill  default NULL,
		  `start_minute` tinyint(2) zerofill default NULL,
		  `end_hour` tinyint(2) zerofill  default NULL,
		  `end_minute` tinyint(2) zerofill  default NULL,
		  `regularity` varchar(255) default NULL,
		  `username` varchar(255) default NULL,
		  PRIMARY KEY  (`id`)
		  )";
		  $result=mysql_query($sql, $link) or die (mysql_error());
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created Schedule Table')";
		  $result=mysql_query($sql, $link);
		}
		/*======================================================================
                            Web_config Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","web_config")){
		
		  $sql = "
CREATE TABLE `web_config` (
  `url` varchar(250) default NULL,
  `LANG` varchar(250) default NULL,
  `language` varchar(250) default NULL,
  `colour` varchar(250) default NULL,
  `title` varchar(250) default NULL,
  `logo` varchar(250) default NULL,
  `contact_text` text,
  `sox` varchar(250) default NULL,
  `userid` varchar(250) default NULL,
  `licence` varchar(250) default NULL,
  `cdr_host` varchar(250) default NULL,
  `cdr_user` varchar(250) default NULL,
  `cdr_pass` varchar(250) default NULL,
  `cdr_db` varchar(250) default NULL,
  `cdr_table` varchar(250) default NULL,
  `menu_home` varchar(250) default NULL,
  `menu_campaigns` varchar(250) default NULL,
  `menu_numbers` varchar(250) default NULL,
  `menu_dnc` varchar(250) default NULL,
  `menu_messages` varchar(250) default NULL,
  `menu_schedules` varchar(250) default NULL,
  `menu_customers` varchar(250) default NULL,
  `menu_queues` varchar(250) default NULL,
  `menu_servers` varchar(250) default NULL,
  `menu_trunks` varchar(250) default NULL,
  `menu_admin` varchar(250) default NULL,
  `menu_logout` varchar(250) default NULL,
  `date_colour` varchar(250) default NULL,
  `main_page_text` text,
  `main_page_username` varchar(250) default NULL,
  `main_page_password` varchar(250) default NULL,
  `main_page_login` varchar(250) default NULL,
  `currency_symbol` varchar(250) default NULL,
  `per_minute` varchar(250) default NULL,
  `use_billing` varchar(250) default NULL,
  `front_page_billing` varchar(250) default NULL,
  `spare1` varchar(250) default NULL,
  `spare2` varchar(250) default NULL,
  `spare3` varchar(250) default NULL,
  `spare4` varchar(250) default NULL,
  `spare5` varchar(250) default NULL,
  `st_mysql_host` varchar(250) default NULL,
  `st_mysql_user` varchar(250) default NULL,
  `st_mysql_pass` varchar(250) default NULL,
  `add_campaign` varchar(250) default NULL,
  `view_campaign` varchar(250) default NULL,
  `per_page` varchar(250) default NULL,
  `numbers_view` varchar(250) default NULL,
  `numbers_system` varchar(250) default NULL,
  `numbers_generate` varchar(250) default NULL,
  `numbers_manual` varchar(250) default NULL,
  `numbers_upload` varchar(250) default NULL,
  `numbers_export` varchar(250) default NULL,
  `numbers_search` varchar(250) default NULL,
  `numbers_title` varchar(250) default NULL,
  `billing_text` varchar(250) default NULL,
  `cdr_text` varchar(250) default NULL,
  `use_generate` varchar(250) default NULL,
  `dnc_numbers_title` varchar(250) default NULL,
  `dnc_view` varchar(250) default NULL,
  `dnc_search` varchar(250) default NULL,
  `dnc_upload` varchar(250) default NULL,
  `dnc_add` varchar(250) default NULL,
  `per_lead` varchar(250) default NULL,
  `smtp_host` varchar(250) default NULL,
  `smtp_user` varchar(250) default NULL,
  `smtp_pass` varchar(250) default NULL,
  `smtp_from` varchar(250) default NULL,
  `use_separate_dnc` varchar(250) default NULL,
  `allow_numbers_manual` varchar(250) default NULL
)		  ";
		  $result=mysql_query($sql, $link) or die (mysql_error());
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created Web_config Table')";
		  $result=mysql_query($sql, $link);
		}

		
		/*======================================================================
                            test_results Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","test_results")){
		
		
		
		$sql = "CREATE TABLE `test_results` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `camaignid` int(11) unsigned NOT NULL,
  `description` varchar(255) default NULL,
  `channels` int(10) unsigned default NULL,
  `avg_busy` varchar(255) default NULL,
  `timespent` varchar(255) default NULL,
  `dialed` varchar(255) default NULL,
  `avg_cps` varchar(255) default NULL,
  `tot_cps` varchar(255) default NULL,
  `overs` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
)";
		$result=mysql_query($sql, $link) or die (mysql_error());
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created test_results Table')";
		  $result=mysql_query($sql, $link);
		} 
		
		/*======================================================================
									Log Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","log")){
		
		  $sql = "CREATE TABLE `log` (
		  `timestamp` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
		  `activity` varchar(255) default NULL,
		  `username` varchar(255) default NULL
		  )";
		  $result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Attempted login')";
		  $result=mysql_query($sql, $link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created Log Table')";
		  $result=mysql_query($sql, $link);
		}
		
		/*======================================================================
									System Billing Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","system_billing")){
		  include "admin/db_config.php";
		  $sql = "CREATE TABLE `system_billing` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `groupid` int(11) default NULL,
		  `totalcost` double default '0',
		  `timestamp` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`id`)
		)";
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created System Timestamp Billing Table')";
		  $result=mysql_query($sql, $link);
		}
		
		/*======================================================================
									campaign Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","campaign")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `campaign` (
		  `id` int(200) NOT NULL auto_increment,
		  `description` varchar(250) default NULL,
		  `name` varchar(200) NOT NULL default '',
		  `groupid` int(200) NOT NULL default '0',
		  `messageid` int(200) NOT NULL default '0',
		  `campaignconfigid` int(11) NOT NULL default '0',
		  `messageid2` int(200) unsigned NOT NULL default '0',
		  `messageid3` int(200) unsigned NOT NULL default '0',
		  `astqueuename` varchar(255) default NULL,
		  `mode` int(11) default '0',
		  `clid` varchar(255) default 'nocallerid <>',
		  `trclid` varchar(255) default 'nocallerid',
		  `maxagents` int(11) default '30',
		  `did` varchar(255) default 'nodid',
		  `context` varchar(255) default 'ls3',
		  `cost` varchar(10) default NULL,
		  PRIMARY KEY  (`id`)
		)";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created campaign Table')";
		  $result=mysql_query($sql, $link);
		}
		
		
		/*======================================================================
									campaigngroup Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","campaigngroup")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `campaigngroup` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` varchar(200) NOT NULL default '',
		  `description` varchar(200) default NULL,
		  PRIMARY KEY  (`id`)
		)";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created campaigngroup Table')";
		  $result=mysql_query($sql, $link);
			$sql = "insert  into campaigngroup values
		(1, 'VentureVoIP', 'A demonstation group which contains a single demo campaign')";
		 $result = mysql_query($sql,$link);
		
		}
		
		
		/*======================================================================
									campaignmessage Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","campaignmessage")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `campaignmessage` (
		  `id` int(11) NOT NULL auto_increment,
		  `filename` varchar(250) NOT NULL default '',
		  `name` varchar(200) NOT NULL default '',
		  `description` varchar(250) NOT NULL default '',
		  `customer_id` int(11) default '0',
		  `length` varchar(255) default NULL,
		  PRIMARY KEY  (`id`)
		)";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created campaignmessage Table')";
		  $result=mysql_query($sql, $link);
		  $sql = "insert  into campaignmessage values
		(27, 'fax-33e5c3b94674a138bc5b390c06e2dba2e7488cb6.tiff', 'New Test Fax', 'A fax broadcasting test', 1, ''),
		(14, 'x-afa871459b4fff189d78420ad7f3158918ca8333.sln', 'Ringin', 'The windows ring in sound', 1, '0.905500'),
		(13, 'x-aba93245ef688df351b4c1765307c1e00a7d3b2e.sln', 'Chord', 'The windows chord sound', 1, '1.099000'),
		(19, 'x-02c4778bdf0e525aa5bbfc5190a9ff7b184136b2.sln', 'Popcorn', 'Popcorn song', 1, '28.585125'),
		(21, 'x-df6efd23c65b97ae1920ceb5ad7b2ee2a2732431.sln', 'Tada', 'The windows tada sound', 26, '1.939000'),
		(24, 'x-d91f8f58dd14d004a31780540d34bba034f3bb1c.sln', 'Transfer 1 -Great', 'Great -here we go', 26, '1.656625'),
		(28, 'x-f9036629b654fffe0bdee6db47521dcd2ceb84b1.sln', 'Ding', 'The windows ding alert sound', 85, '0.915750')";
		$result = mysql_query($sql,$link);
		}
		
		
		/*======================================================================
									cdr Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","cdr")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `cdr` (
		  `calldate` datetime NOT NULL default '0000-00-00 00:00:00',
		  `clid` varchar(80) NOT NULL default '',
		  `src` varchar(80) NOT NULL default '',
		  `dst` varchar(80) NOT NULL default '',
		  `dcontext` varchar(80) NOT NULL default '',
		  `channel` varchar(80) NOT NULL default '',
		  `dstchannel` varchar(80) NOT NULL default '',
		  `lastapp` varchar(80) NOT NULL default '',
		  `lastdata` varchar(80) NOT NULL default '',
		  `duration` int(11) NOT NULL default '0',
		  `billsec` int(11) NOT NULL default '0',
		  `disposition` varchar(45) NOT NULL default '',
		  `amaflags` int(11) NOT NULL default '0',
		  `accountcode` varchar(20) NOT NULL default '',
		  `userfield` varchar(255) NOT NULL default '',
		  `userfield2` varchar(2) NOT NULL default '',
		  KEY  (`dcontext`,`userfield`,`userfield2`),
		  KEY `calldate` (`calldate`),
		  KEY `dst` (`dst`),
		  KEY `accountcode` (`accountcode`)
		)";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created cdr Table')";
		  $result=mysql_query($sql, $link);
		}
		
		
		/*======================================================================
									config Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","config")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `config` (
		  `parameter` varchar(255) NOT NULL default '0',
		  `value` varchar(255) NOT NULL,
		  PRIMARY KEY  (`parameter`)
		) ";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created config Table')";
		  $result=mysql_query($sql, $link);
		  $sql = "insert  into config values
		('backend', '0'),
		('userid', 'VentureVoIP'),
		('licencekey', 'DRFHUJWQIWU')";
		 $result = mysql_query($sql,$link);
		}
		
		/* Check if the length of the parameter field is 255 - if not make it so */
		$result = mysql_query("SELECT parameter, value FROM config");
		$param_length = mysql_field_len($result, 0);
		$value_length = mysql_field_len($result, 1);
		if ($param_length != 255) {
			$sql = "ALTER TABLE config MODIFY parameter VARCHAR(255)";
			$result=mysql_query($sql, $link);
			$sql = "ALTER TABLE config MODIFY value VARCHAR(255)";
			$result=mysql_query($sql, $link);
		}
		
		/* Check if there is a primary key on the config table - if not create it */
		$result = mysql_query("SHOW INDEXES FROM config");
		if (mysql_num_rows($result) == 0) {
			$sql = "ALTER TABLE config ADD PRIMARY KEY (parameter)";
			$result=mysql_query($sql, $link);
		}
		
		/*======================================================================
									rates Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","rates")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `rates` (
		  `campaignid` int(11) NOT NULL,
		  `idx` int(11) NOT NULL,
		  `value` double NOT NULL,
		  UNIQUE KEY `c_i` (`campaignid`,`idx`),
		  KEY `campaignid` (`campaignid`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created rates Table')";
		  $result=mysql_query($sql, $link);
		}
		/*======================================================================
									engine_stats Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","engine_stats")){
		  include "admin/db_config.php";
		$sql = "
		CREATE TABLE `engine_stats` (
		 `stat` varchar(250) NOT NULL,
		 `value` varchar(250) NOT NULL default 'null',
		 PRIMARY KEY  (`stat`)
		 ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		 ";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created profracs Table')";
		  $result=mysql_query($sql, $link);
		}
		/*======================================================================
									profracs Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","profracs")){
		  include "admin/db_config.php";
		$sql = "
		CREATE TABLE `profracs` (
		  `campaignid` int(11) NOT NULL,
		  `idx` int(11) NOT NULL,
		  `value` double NOT NULL,
		  UNIQUE KEY `c_i` (`campaignid`,`idx`),
		  KEY `campaignid` (`campaignid`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		 ";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created profracs Table')";
		  $result=mysql_query($sql, $link);
		}
		/*======================================================================
									sleeps Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","sleeps")){
		  include "admin/db_config.php";
		$sql = "
		CREATE TABLE `sleeps` (
		  `campaignid` int(11) NOT NULL,
		  `idx` int(11) NOT NULL,
		  `value` double NOT NULL,
		  UNIQUE KEY `c_i` (`campaignid`,`idx`),
		  KEY `campaignid` (`campaignid`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		 ";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created sleeps Table')";
		  $result=mysql_query($sql, $link);
		}
		/*======================================================================
									campaign_stats Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","campaign_stats")){
		  include "admin/db_config.php";
		$sql = "
		CREATE TABLE `campaign_stats` (
		  `campaignid` int(11) NOT NULL,
		  `min_agents` int(11) default NULL,
		  `busy_agents` int(11) default NULL,
		  `total_agents` int(11) default NULL,
		  `dialed` int(11) default NULL,
		  `speed_multiplyer` double default NULL,
		  `max_running_speed` double default NULL,
		  `adjuster` int(11) default NULL,
		  `time_spent` bigint(20) default NULL,
		  `weighted` double default NULL,
		  `cummulative_area_diff` double default NULL,
		  `ms_sleep` double default NULL,
		  `max_delay_calc` double default NULL,
		  `overs_1` int(11) default NULL,
		  `overs_2` double default NULL,
		  PRIMARY KEY  (`campaignid`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		
		 ";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created campaign_stats Table')";
		  $result=mysql_query($sql, $link);
		}
		/*======================================================================
									customer Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","customer")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `customer` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `username` varchar(30) NOT NULL default '',
		  `password` varchar(200) NOT NULL default '',
		  `campaigngroupid` int(10) unsigned NOT NULL default '0',
		  `address1` varchar(250) default NULL,
		  `address2` varchar(250) default NULL,
		  `city` varchar(250) default NULL,
		  `country` varchar(250) default NULL,
		  `phone` varchar(250) default NULL,
		  `email` varchar(250) default NULL,
		  `fax` varchar(250) default NULL,
		  `website` varchar(250) default NULL,
		  `security` int(3) unsigned default '0',
		  `company` varchar(250) default NULL,
		  `trunkid` int(11) default '-1',
		  `zip` varchar(25) default NULL,
		  `state` varchar(250) default NULL,
		  `maxcps` int(11) default '1',
		  `maxchans` int(11) default '100',
		  `adminlists` varchar(2555) default NULL,
		  `didlogin` varchar(255) default NULL,
		  `interface_type` VARCHAR(255) default 'default',
		  PRIMARY KEY  (`id`)
		) ";
		
			$result = mysql_query($sql,$link) or die(mysql_error());
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created customer Table')";
		  $result=mysql_query($sql, $link);
		  $sql = "insert  into customer (id, username, password, campaigngroupid, maxcps, maxchans, security)
		  values (2, 'admin', '".sha1("adminpass")."', 1, 1000, 1001, 100)";
		  $result=mysql_query($sql, $link) or die(mysql_error());
		}
		
		/*======================================================================
									dncnumber Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","dncnumber")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `dncnumber` (
		  `campaignid` int(200) NOT NULL default '0',
		  `phonenumber` varchar(50) NOT NULL default '',
		  `status` varchar(50) NOT NULL default '',
		  `type` int(5) NOT NULL default '0',
		  PRIMARY KEY  (`campaignid`,`phonenumber`),
		  KEY `test` (`phonenumber`,`campaignid`)
		) ";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created dncnumber Table')";
		  $result=mysql_query($sql, $link);
		}
		
		/*======================================================================
									number Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","number")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `number` (
		  `campaignid` int(200) NOT NULL default '0',
		  `phonenumber` varchar(50) NOT NULL default '',
		  `status` varchar(50) NOT NULL default '',
		  `type` int(5) NOT NULL default '0',
		  `datetime` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
		  `random_sort` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`campaignid`,`phonenumber`),
		  KEY `status` (`campaignid`,`status`),
		  KEY `randomize` (`random_sort`,`campaignid`, `status`),
		  KEY `status2` (`status`)
		)";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created number Table')";
		  $result=mysql_query($sql, $link);
		
		}
		  $fields = mysql_list_fields('SineDialer', 'number');
		  $columns = mysql_num_fields($fields);
		  for ($i = 0; $i < $columns; $i++) {
			  $field_array[] = mysql_field_name($fields, $i);
		  }
		
		  if (!in_array('random_sort', $field_array))
		  {
			  echo "Please wait, updating system...this may take a while - please don't stop it<br />";
			  flush();
			  sleep(1);
			  echo "Starting with adding the random sort field to the numbers table<br />";
			  flush();
			  sleep(1);
			  $result = mysql_query('ALTER TABLE number ADD random_sort int(10)') or die(mysql_error());
			  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Added number random_sort field')";
			  $result=mysql_query($sql, $link);
			  echo "Added field - now updating the numbers to give them each a random value<br />";
			  flush();
			  sleep(1);
			  $result = mysql_query('UPDATE number SET random_sort = ROUND(RAND() * 999999999)') or die(mysql_error());
			  $result = mysql_query("ALTER TABLE number ADD INDEX randomize (random_sort, campaignid, status)") or die(mysql_error());;
			  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'randomized existing number field')";
			  echo "Update complete - please log back in";
			  ?><META HTTP-EQUIV=REFRESH CONTENT="0; URL=/index.php"><?
			  exit(0);
		  }
		
		
		
		/*======================================================================
									number_done Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","number_done")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `number_done` (
		  `campaignid` int(200) NOT NULL default '0',
		  `phonenumber` varchar(50) NOT NULL default '',
		  `status` varchar(50) NOT NULL default '',
		  `type` int(5) NOT NULL default '0',
		  `datetime` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`campaignid`,`phonenumber`),
		  KEY `status` (`campaignid`,`status`),
		  KEY `status2` (`status`)
		)";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created number_done Table')";
		  $result=mysql_query($sql, $link);
		}
		
		
		/*======================================================================
									queue Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","queue")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `queue` (
		  `queueID` int(11) NOT NULL auto_increment,
		  `queuename` varchar(100) default NULL,
		  `status` tinyint(4) NOT NULL default '0',
		  `campaignID` int(11) NOT NULL default '0',
		  `details` varchar(250) default NULL,
		  `flags` int(11) NOT NULL default '0',
		  `transferclid` varchar(20) default '0',
		  `starttime` time default NULL,
		  `endtime` time default NULL,
		  `startdate` date default NULL,
		  `enddate` date default NULL,
		  `did` varchar(20) default NULL,
		  `clid` varchar(20) default NULL,
		  `context` int(1) NOT NULL default '0',
		  `maxcalls` int(11) default '100',
		  `maxchans` int(11) default '100',
		  `maxretries` int(11) default '0',
		  `retrytime` int(11) default '30',
		  `waittime` int(11) default '30',
		  `timespent` varchar(20) default '0',
		  `progress` varchar(20) default '0',
		  `expectedRate` float NOT NULL default '100',
		  `mode` varchar(120) default '0',
		  `astqueuename` varchar(20) default '',
		  `trunk` varchar(255) default 'Local/s@\${EXTEN}',
		  `accountcode` varchar(255) default 'noaccount',
		  `trunkid` int(11) default '-1',
		  `customerID` int(11) default '-1',
		  `maxcps` int(11) default '31',
		  PRIMARY KEY  (`queueID`)
		) ";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created queue Table')";
		  $result=mysql_query($sql, $link);
		}
		
		
		
		
		
		/*======================================================================
									campaignconfig Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","campaignconfig")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `campaignconfig` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `type` int(11) default '0',
		  `astqueuename` varchar(255) default NULL,
		  `did` varchar(255) default NULL,
		  `clid` varchar(255) default NULL,
		  `trclid` varchar(255) default NULL,
		  `maxchans` int(11) default '10',
		  `numagents` int(11) default '10',
		  PRIMARY KEY  (`id`)
		) ";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created campaignconfig Table')";
		  $result=mysql_query($sql, $link);
		}
		
		
		
		
		/*======================================================================
									Billing Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","billing")){
		  include "admin/db_config.php";
		  $sql = "CREATE TABLE `billing` (
		  `customerid` int(11) unsigned NOT NULL default '0',
		  `accountcode` varchar(250) NOT NULL default '',
		  `priceperminute` double(10,5) default '0.00000',
		  `firstperiod` int(10) unsigned default '1',
		  `increment` int(10) unsigned default '1',
		  `credit` double(100,10) default '0.0000000000',
		  `pricepercall` double(10,5) default '0.00000',
		  `priceperconnectedcall` double(10,5) default '0.00000',
		  `priceperpress1` double(10,5) default '0.00000',
		  `creditlimit` double(100,10) default '0.0000000000',
		  PRIMARY KEY  (`customerid`,`accountcode`)
		  )";
		  $result = mysql_query($sql,$link);
		}
		
		/*======================================================================
									Billing Log Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","billinglog")){
		  include "admin/db_config.php";
		
		  $sql = "CREATE TABLE `billinglog` (
		  `timestamp` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
		  `activity` varchar(255) default NULL,
		  `receipt` varchar(255) default NULL,
		  `paymentmode` varchar(255) default NULL,
		  `username` varchar(255) default NULL,
		  `addedby` varchar(255) default NULL
		  )";
		  $result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created Billing Log Table')";
		  $result=mysql_query($sql, $link);
		}
		
		
		/*======================================================================
									Realtime SIP
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","sip_buddies")){
		  //echo "Not there";
		  include "admin/db_config.php";
		  $sql = "CREATE TABLE `sip_buddies` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` varchar(80) NOT NULL default '',
		  `accountcode` varchar(20) default NULL,
		  `callerid` varchar(80) default NULL,
		  `canreinvite` char(3) default 'no',
		  `context` varchar(80) default 'internal',
		  `dtmfmode` varchar(7) default 'rfc2833',
		  `host` varchar(31) default 'dynamic',
		  `language` char(2) default 'it',
		  `nat` varchar(5) default 'yes',
		  `port` varchar(5) default '5060',
		  `qualify` char(3) default NULL,
		  `secret` varchar(80) default NULL,
		  `type` varchar(6) NOT NULL default 'friend',
		  `username` varchar(80) NOT NULL default '',
		  `disallow` varchar(100) default 'all',
		  `allow` varchar(100) default 'gsm;ulaw;alaw',
		  `regseconds` int(11) NOT NULL default '0',
		  `ipaddr` varchar(150) NOT NULL default '',
		  `regexten` varchar(80) NOT NULL default '',
		  `cancallforward` char(3) default 'yes',
		  `setvar` varchar(100) NOT NULL default '',
		  `clientid` int(13) default NULL,
		  `description` varchar(100) default NULL,
		  `fullcontact` varchar(250) default NULL,
		  `visible` varchar(11) default NULL,
		  `isagent` tinyint(3) unsigned NOT NULL default '0',
		  `regserver` varchar(250) default NULL,
		  `email` varchar(250) default NULL,
		  `lastname` varchar(250) default NULL,
		  `firstname` varchar(250) default NULL,
		  `country` varchar(250) default NULL,
		  `hasaccount` int(11) default NULL,
		  `dateadded` datetime default NULL,
		  `transfer` varchar(250) default NULL,
		  `lastms` varchar(250) default NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `name` (`name`),
		  KEY `name_2` (`name`)
		  );";
		  $result = mysql_query($sql,$link) or die(mysql_error());
		}
		
		/*======================================================================
									Realtime IAX2
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","iax_buddies")){
		  include "admin/db_config.php";
		  $sql = "CREATE TABLE `iax_buddies` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `name` varchar(32) NOT NULL default '',
		  `username` varchar(30) default NULL,
		  `type` varchar(6) NOT NULL default 'friend',
		  `secret` varchar(50) default NULL,
		  `transfer` varchar(10) default 'mediaonly',
		  `accountcode` varchar(100) default NULL,
		  `callerid` varchar(100) default NULL,
		  `context` varchar(100) default 'freevoip',
		  `host` varchar(31) NOT NULL default 'dynamic',
		  `language` varchar(5) default 'it',
		  `mailbox` varchar(50) default NULL,
		  `qualify` varchar(4) default '400',
		  `disallow` varchar(100) default 'all',
		  `allow` varchar(100) default 'gsm,ulaw,alaw',
		  `ipaddr` varchar(15) default NULL,
		  `port` int(11) default '0',
		  `regseconds` int(11) default '0',
		  `clientid` int(13) unsigned default NULL,
		  `description` varchar(100) default NULL,
		  `visible` varchar(11) default NULL,
		  `encryption` varchar(40) default NULL,
		  `auth` varchar(10) default NULL,
		  `isagent` tinyint(3) unsigned NOT NULL default '0',
		  `firstname` varchar(255) default NULL,
		  `lastname` varchar(255) default NULL,
		  `email` varchar(255) default NULL,
		  `country` varchar(255) default NULL,
		  `hasaccount` int(11) default NULL,
		  `dateadded` datetime default NULL,
		  `trunk` char(3) default 'no',
		  `sendmail` int(3) default '1',
		  `regcontext` varchar(60) default 'iaxregs',
		  `jitterbuffer` varchar(4) default 'no',
		  PRIMARY KEY  (`id`)
		  );";
			$result = mysql_query($sql,$link);
		
		}
		/*======================================================================
									Campaign
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","campaign")){
		  include "admin/db_config.php";
			$sql = "Create table `campaign` (
			`id` int(200) NOT NULL auto_increment,
			`description` varchar(250) default NULL,
			`name` varchar(200) NOT NULL default '',
			`groupid` int(200) NOT NULL default '0',
			`messageid` int(200) NOT NULL default '0',
			`campaignconfigid` int(11) NOT NULL default '0',
			`messageid2` INT(200) NOT NULL unsigned default '0',
			`messageid3` INT(200) NOT NULL unsigned default '0',
			`astqueuename` VARCHAR(255) default NULL,
			`mode` INT(11)  default '0',
			`clid` varchar(255) default 'nocallerid <>',
			`trclid` varchar(255) default 'nocallerid',
			`maxagents` int(11) default '30',
			`did` varchar(255) default 'nodid',
			`context` varchar(255) default 'ls3',
			`cost` varchar(10) default NULL,
			PRIMARY KEY (`id`)
			);";
			$result = mysql_query($sql,$link);
		}
		
		/*======================================================================
									Campaign Config
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","campaignconfig")){
		  include "admin/db_config.php";
				$sql = "Create table `campaignconfig` (
			`id` int(10) unsigned not null auto_increment,
			`type` int (11) default '0',
			`astqueuename` varchar(255) default NULL,
			`did` varchar(255) default NULL,
			`clid` varchar(255) default NULL,
			`trclid` varchar(255) default NULL,
			`maxchans` int(11) default 10,
			`numagents` int(11) default 10,
			PRIMARY KEY(`id`)
				);";
			$result = mysql_query($sql,$link);
		}
		
		/*======================================================================
									Campaign Message
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","campaignconfig")){
		  include "admin/db_config.php";
				$sql = "Create table `campaignmessage` (
				`id` int(10) unsigned not null auto_increment,
			`filename` varchar(250) not null,
			`name` varchar(200) not null,
			`description` varchar(250) not null,
			`customer_id` int(11),
			primary key(`id`)
				);";
				$result = mysql_query($sql,$link);
		}
		
		
		/*======================================================================
									CDR
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","cdr")){
		  include "admin/db_config.php";
				$sql = "Create table `cdr` (
		  `calldate` datetime NOT NULL default '0000-00-00 00:00:00',
		  `clid` varchar(80) NOT NULL default '',
		  `src` varchar(80) NOT NULL default '',
		  `dst` varchar(80) NOT NULL default '',
		  `dcontext` varchar(80) NOT NULL default '',
		  `channel` varchar(80) NOT NULL default '',
		  `dstchannel` varchar(80) NOT NULL default '',
		  `lastapp` varchar(80) NOT NULL default '',
		  `lastdata` varchar(80) NOT NULL default '',
		  `duration` int(11) NOT NULL default '0',
		  `billsec` int(11) NOT NULL default '0',
		  `disposition` varchar(45) NOT NULL default '',
		  `amaflags` int(11) NOT NULL default '0',
		  `accountcode` varchar(20) NOT NULL default '',
		  `userfield` varchar(255) NOT NULL default '',
		  `userfield2` varchar(255) NOT NULL default '',
		  `userfield3` varchar(255) NOT NULL default '',
		  `userfield4` varchar(255) NOT NULL default '',
		  `userfield5` varchar(255) NOT NULL default '',
		  KEY `calldate` (`calldate`),
		  KEY `dst` (`dst`),
		  KEY `accountcode` (`accountcode`)
				);";
				$result = mysql_query($sql,$link);
		}
		
		
		/*======================================================================
									Config
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","config")){
		  include "admin/db_config.php";
				$sql = "Create table `config` (
		`parameter` varchar(11) NOT NULL default '0',
		  `value` varchar(110) NOT NULL
				);";
				$result = mysql_query($sql,$link);
		}
		
		/*======================================================================
									Customer
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","customer")){
		  include "admin/db_config.php";
				$sql = "Create table `customer` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `username` varchar(30) NOT NULL default '',
		  `password` varchar(200) NOT NULL default '',
		  `campaigngroupid` int(10) unsigned NOT NULL default '0',
		  `address1` varchar(250) default NULL,
		  `address2` varchar(250) default NULL,
		  `city` varchar(250) default NULL,
		  `country` varchar(250) default NULL,
		  `phone` varchar(250) default NULL,
		  `email` varchar(250) default NULL,
		  `fax` varchar(250) default NULL,
		  `website` varchar(250) default NULL,
		  `security` int(3) unsigned default '0',
		  `company` varchar(250) default NULL,
		  `trunkid` int(11) default '-1',
		  `zip` varchar(25) default NULL,
		  `state` varchar(250) default NULL,
		  `maxcps` int(11) default '10',
		  `maxchans` int(11) default '100',
		  `adminlists` varchar(2555) default NULL,
		  PRIMARY KEY  (`id`)
				);";
				$result = mysql_query($sql,$link);
			$result = mysql_query("INSERT INTO customer (`username`,`password`,`security`) VALUES ('admin',".sha1("adminpass").",100)",$link);
		}
		
		/*======================================================================
								 DNC Number
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","dncnumber")){
		  include "admin/db_config.php";
				$sql = "Create table `dncnumber` (
		  `campaignid` int(200) NOT NULL default '0',
		  `phonenumber` varchar(50) NOT NULL default '',
		  `status` varchar(50) NOT NULL default '',
		  `type` int(5) NOT NULL default '0',
		  PRIMARY KEY  (`campaignid`,`phonenumber`),
		  KEY `test` (`phonenumber`,`campaignid`)
				);";
				$result = mysql_query($sql,$link);
		}
		
		/*======================================================================
								 Number
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","number")){
		  include "admin/db_config.php";
				$sql = "Create table `number` (
		  `campaignid` int(200) NOT NULL default '0',
		  `phonenumber` varchar(50) NOT NULL default '',
		  `status` varchar(50) NOT NULL default '',
		  `type` int(5) NOT NULL default '0',
		  `datetime` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`campaignid`,`phonenumber`),
		  KEY `test` (`phonenumber`,`campaignid`)
		  KEY `status` (`campaignid`,`status`),
		  KEY `status2` (`status`)
				);";
				$result = mysql_query($sql,$link);
		}
		
		/*======================================================================
								 Queue
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","queue")){
		  include "admin/db_config.php";
				$sql = "Create table `queue` (
		  `queueID` int(11) NOT NULL auto_increment,
		  `queuename` varchar(100) default NULL,
		  `status` tinyint(4) NOT NULL default '0',
		  `campaignID` int(11) NOT NULL default '0',
		  `details` varchar(250) default NULL,
		  `flags` int(11) NOT NULL default '0',
		  `transferclid` varchar(20) default '0',
		  `starttime` time default NULL,
		  `endtime` time default NULL,
		  `startdate` date default NULL,
		  `enddate` date default NULL,
		  `did` varchar(20) default NULL,
		  `clid` varchar(20) default NULL,
		  `context` int(1) NOT NULL default '0',
		  `maxcalls` int(11) default '100',
		  `maxchans` int(11) default '100',
		  `maxretries` int(11) default '0',
		  `retrytime` int(11) default '30',
		  `waittime` int(11) default '30',
		  `timespent` varchar(20) default '0',
		  `progress` varchar(20) default '0',
		  `expectedRate` float NOT NULL default '100',
		  `mode` varchar(120) default '0',
		  `astqueuename` varchar(20) default '',
		  `trunk` varchar(255) default 'Local/s@\${EXTEN}',
		  `accountcode` varchar(255) default 'noaccount',
		  `trunkid` int(11) default '-1',
		  `customerID` int(11) default '-1',
		  `maxcps` int(11) default '31',
		  PRIMARY KEY  (`queueID`)
				);";
				$result = mysql_query($sql,$link);
		}
		
		/*======================================================================
									servers Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","servers")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `servers` (
		  `id` int(11) NOT NULL auto_increment,
		  `address` varchar(250) NOT NULL default '',
		  `name` varchar(200) NOT NULL default '',
		  `username` varchar(250) NOT NULL default '',
		  `password` varchar(250) NOT NULL default '',
		  `status` int(10) default '0',
		  `readonly` int(10) default '0',
		  PRIMARY KEY  (`id`)
		)";
		
		  $result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created servers Table')";
		  $result=mysql_query($sql, $link);
		}
		
		$fields = mysql_list_fields('SineDialer', 'servers');
		$columns = mysql_num_fields($fields);
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
		
		if (!in_array('readonly', $field_array))
		{
			$result = mysql_query('ALTER TABLE servers ADD readonly int(10)');
			$sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Added server readonly field')";
			$result=mysql_query($sql, $link);
		}
		
		
		/*======================================================================
									stage Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","stage")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `stage` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `phonenumber` varchar(50) NOT NULL default '',
		  `stage` int(3) NOT NULL default '0',
		  `campaignid` int(3) NOT NULL default '0',
		  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`id`)
		) ";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created stage Table')";
		  $result=mysql_query($sql, $link);
		}
		
		
		/*======================================================================
									trunk Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","trunk")){
		  include "admin/db_config.php";
		$sql = "CREATE TABLE `trunk` (
		  `id` int(15) unsigned NOT NULL auto_increment,
		  `name` varchar(250) NOT NULL default '',
		  `dialstring` varchar(250) NOT NULL default '',
		  `current` int(1) NOT NULL default '0',
		  `maxchans` int(11) unsigned default '100',
		  `maxcps` varchar(255) default '30',
		  PRIMARY KEY  (`id`)
		) ";
		
			$result = mysql_query($sql,$link);
		  $sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Created trunk Table')";
		  $result=mysql_query($sql, $link);
		  $sql = "insert  into trunk values
		(1, 'Load Test', 'Local/s@staff/\${EXTEN}', 1, 300, '10'),
		(11, 'Local Hardware', 'Zap/g1/\${EXTEN}', 0, 10, '3'),
		(13, 'Dialplan', 'Local/\${EXTEN}@my_context', 0, 1000, '3'),
		(16, 'IAX2 Trunk', 'IAX2/my-provider/\${EXTEN}', 0, 100, '10'),
		(17, 'SIP Trunk', 'SIP/\${EXTEN}@my-provider', 0, 100, '5')";
		  $result=mysql_query($sql, $link);
		
		}
		
		
		/*======================================================================
								 Queue_Member_Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","queue_member_table")){
		  include "admin/db_config.php";
				$sql = "Create table `queue_member_table` (
		  `uniqueid` int(10) unsigned NOT NULL auto_increment,
		  `membername` varchar(40) default NULL,
		  `queue_name` varchar(128) default NULL,
		  `interface` varchar(128) default NULL,
		  `penalty` int(11) default NULL,
		  `paused` tinyint(1) default NULL,
		  PRIMARY KEY  (`uniqueid`),
		  UNIQUE KEY `queue_interface` (`queue_name`,`interface`)
				);";
				$result = mysql_query($sql,$link);
		}
		
		
		/*======================================================================
								 Queue_Table
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","queue_table")){
		  include "admin/db_config.php";
				$sql = "Create table `queue_table` (
		  `name` varchar(128) NOT NULL,
		  `musiconhold` varchar(128) default 'default',
		  `announce` varchar(128) default NULL,
		  `context` varchar(128) default NULL,
		  `timeout` int(11) default NULL,
		  `monitor_join` tinyint(1) default NULL,
		  `monitor_format` varchar(128) default NULL,
		  `queue_youarenext` varchar(128) default 'queue-youarenext',
		  `queue_thereare` varchar(128) default 'queue-thereare',
		  `queue_callswaiting` varchar(128) default 'queue-callswaiting',
		  `queue_holdtime` varchar(128) default 'queue-holdtime',
		  `queue_minutes` varchar(128) default 'queue-minutes',
		  `queue_seconds` varchar(128) default 'queue-seconds',
		  `queue_lessthan` varchar(128) default 'queue-less-than',
		  `queue_thankyou` varchar(128) default 'queue-thankyou',
		  `queue_reporthold` varchar(128) default NULL,
		  `announce_frequency` int(11) default 0,
		  `announce_round_seconds` int(11) default NULL,
		  `announce_holdtime` varchar(128) default NULL,
		  `retry` int(11) default NULL,
		  `wrapuptime` int(11) default NULL,
		  `maxlen` int(11) default NULL,
		  `servicelevel` int(11) default NULL,
		  `strategy` varchar(128) default NULL,
		  `joinempty` varchar(128) default NULL,
		  `leavewhenempty` varchar(128) default NULL,
		  `eventmemberstatus` tinyint(1) default NULL,
		  `eventwhencalled` tinyint(1) default NULL,
		  `reportholdtime` tinyint(1) default NULL,
		  `memberdelay` int(11) default NULL,
		  `weight` int(11) default NULL,
		  `timeoutrestart` tinyint(1) default NULL,
		  `periodic_announce` varchar(50) default NULL,
		  `periodic_announce_frequency` int(11) default NULL,
		  PRIMARY KEY  (`name`)
				);";
				$result = mysql_query($sql,$link);
		}
		
		
		/*======================================================================
								 Servers
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","servers")){
		  include "admin/db_config.php";
				$sql = "Create table `servers` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `phonenumber` varchar(50) NOT NULL default '',
		  `stage` int(3) NOT NULL default '0',
		  `campaignid` int(3) NOT NULL default '0',
		  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`id`)
				);";
				$result = mysql_query($sql,$link);
		}
		
		/*======================================================================
								 Stage
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","stage")){
		  include "admin/db_config.php";
				$sql = "Create table `stage` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `phonenumber` varchar(50) NOT NULL default '',
		  `stage` int(3) NOT NULL default '0',
		  `campaignid` int(3) NOT NULL default '0',
		  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`id`)
				);";
				$result = mysql_query($sql,$link);
		}
		
		/*======================================================================
								Trunk
		  ======================================================================*/
		if (!mysql_is_table($db_host,$db_user,$db_pass,"SineDialer","trunk")){
		  include "admin/db_config.php";
				$sql = "Create table `trunk` (
		  `id` int(15) unsigned NOT NULL auto_increment,
		  `name` varchar(250) NOT NULL default '',
		  `dialstring` varchar(250) NOT NULL default '',
		  `current` int(1) NOT NULL default '0',
		  `maxchans` int(11) unsigned default '100',
		  `maxcps` varchar(255) default '30',
		  PRIMARY KEY  (`id`)
				);";
				$result = mysql_query($sql,$link);
		}
		
		$fields = mysql_list_fields('SineDialer', 'campaign', $link);
		$columns = mysql_num_fields($fields);
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
		
		if (!in_array('cost', $field_array))
		{
			$result = mysql_query('ALTER TABLE campaign ADD cost VARCHAR(10)');
			$sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Added campaign cost field')";
			$result=mysql_query($sql, $link);
		}
		
		/*****************************************************************
		*           ALTER customer TABLE TO ADD astqueuename FIELD       *
		******************************************************************/
		unset($field_array);
		$fields = mysql_list_fields('SineDialer', 'customer', $link);
		$columns = mysql_num_fields($fields);
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
		if (!in_array('astqueuename', $field_array))
		{
			$result = mysql_query('ALTER TABLE customer ADD astqueuename VARCHAR(255)');
			$sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Added customer astqueuename field')";
			$result=mysql_query($sql, $link);
		}
		
		
		/*****************************************************************
		*           ALTER sip_buddies TABLE TO ADD call-limit FIELD      *
		******************************************************************/
		unset($field_array);
		$fields = mysql_list_fields('SineDialer', 'sip_buddies', $link);
		$columns = mysql_num_fields($fields);
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
		if (!in_array('call-limit', $field_array))
		{
			$result = mysql_query('ALTER TABLE sip_buddies ADD `call-limit` int(8) default 1') or die(mysql_error());
			$result = mysql_query('UPDATE sip_buddies SET `call-limit`=1') or die(mysql_error());
			$sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Added sip_buddies call-limit field')";
			$result=mysql_query($sql, $link);
		}
		
		/*======================================================================
								 Stats Only Users
		  ======================================================================*/
		if ( ! mysql_is_table($db_host, $db_user, $db_pass, "SineDialer", "statuser") ) {
				include "admin/db_config.php";
				$sql = "Create table `statuser` (
										`id` int(10) unsigned NOT NULL auto_increment,
										`campaignid` int(3) NOT NULL default '0',
										`hash` varchar(255) NOT NULL default '',
										PRIMARY KEY  (`id`)
								);";
				$result = mysql_query($sql,$link);
		}
		
		
		/*****************************************************************
		*           ALTER customer TABLE TO ADD didlogin FIELD             *
		******************************************************************/
		unset($field_array);
		$fields = mysql_list_fields('SineDialer', 'customer', $link);
		$columns = mysql_num_fields($fields);
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
		
		if (!in_array('didlogin', $field_array))
		{
			$result = mysql_query('ALTER TABLE customer ADD didlogin VARCHAR(255)');
			$sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Added customer didlogin field')";
			$result=mysql_query($sql, $link);
		}
		
		/*****************************************************************
		*           ALTER MESSAGE TABLE TO ADD length FIELD             *
		******************************************************************/
		unset($field_array);
		$fields = mysql_list_fields('SineDialer', 'campaignmessage', $link);
		$columns = mysql_num_fields($fields);
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
		
		if (!in_array('length', $field_array))
		{
			$result = mysql_query('ALTER TABLE campaignmessage ADD length VARCHAR(255)');
			$sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Added campaignmessage length field')";
			$result=mysql_query($sql, $link);
		}
		
		/*****************************************************************
		*           ALTER BILLING TABLE TO ADD receipt FIELD             *
		******************************************************************/
		unset($field_array);
		$fields = mysql_list_fields('SineDialer', 'billinglog', $link);
		$columns = mysql_num_fields($fields);
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
		
		if (!in_array('receipt', $field_array))
		{
			$result = mysql_query('ALTER TABLE billinglog ADD receipt VARCHAR(255)');
			$sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Added billinglog receipt field')";
			$result=mysql_query($sql, $link);
		}
		
		if (!in_array('paymentmode', $field_array))
		{
			$result = mysql_query('ALTER TABLE billinglog ADD paymentmode VARCHAR(255)');
			$sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Added billinglog paymentmode field')";
			$result=mysql_query($sql, $link);
		}
		
		/*****************************************************************
		*           ALTER SIP_BUDDIES TABLE TO ADD lastms FIELD             *
		******************************************************************/
		unset($field_array);
		$fields = mysql_list_fields('SineDialer', 'sip_buddies', $link);
		$columns = mysql_num_fields($fields);
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
		
		if (!in_array('lastms', $field_array))
		{
			$result = mysql_query('ALTER TABLE sip_buddies ADD lastms VARCHAR(255)');
			$sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Added sip_buddies lastms field')";
			$result=mysql_query($sql, $link);
		}

		/*****************************************************************
		*           ALTER CUSTOMER TABLE TO ADD interface_type FIELD     *
		******************************************************************/
		unset($field_array);
		$fields = mysql_list_fields('SineDialer', 'customer', $link);
		$columns = mysql_num_fields($fields);
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
		
		if (!in_array('interface_type', $field_array))
		{
			$result = mysql_query('ALTER TABLE customer ADD interface_type VARCHAR(255) default \'default\'');
			$sql = "INSERT INTO log (timestamp, username, activity) VALUES (NOW(), '$_POST[user]', 'Added customer.interface_type field')";
			$result=mysql_query($sql, $link) or die(mysql_error());
		}

	}
}


?>
