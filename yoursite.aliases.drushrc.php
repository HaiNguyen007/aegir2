<?php
/**
* @file yoursite.aliases.drushrc.php
* Site aliases for [your site domain]
* Place this file at ~/.drush/  (~/ means your home path)
* Phương pháp: pull data & push config (data kéo xuống, config đẩy lên)
* Local server: dev, test
* Remote server: prod
* Pull data:
* prod => dev
* prod => test
* test => dev
* Push config:
* dev => test => prod
*
* Usage:
* Tạo dev (local) site:
*drush rsync @yoursite.prod @yoursite.dev --include-config (chỉnh settings.php cho dev)
* drush sql-sync @yoursite.prod @yoursite.dev --create-db
* có thể cần cung cấp thêm: --db-su và --db-su-pw (sau đó xóa cache tại dev)
* data:  test => dev:
*   $ drush sql-sync @yoursite.test @yoursite.dev
*  data: dev => test:
*   $ drush sql-sync @yoursite.local @yoursite.dev -structure-tables-key=common --no-ordered-dump --sanitize=0 --no-cache
*   data: prod => dev:
*   $ drush sql-sync @yoursite.prod @yoursite.dev
*   To copy all files in test site to your dev site:
*   $ drush rsync @yoursite.test:%files @yoursite.dev:%files
*   Clear the cache in production:
*   $ drush @yoursite.prod cc all
*
* You can copy the site alias configuration of an existing site into a file
* with the following commands:
*   $ cd /path/to/settings.php/of/the/site/
*   $ drush site-alias @self --full --with-optional >> ~/.drush/mysite.aliases.drushrc.php
*
*drush dd @dev:%files
*cd `drush dd @dev:%files`
*
*export & import db:
*drush @dev sql-dump > `drush dd @dev:%dump`
*hoặc
* drush @x sql-dump --result-file=`drush dd @dev:%dump`
*`drush @dev sql-connect` < `drush dd @dev:%dump`
*
* structure-tables: list of table we never pull down data => ++speed
* drush sql-sync --structure-tables-key=common @site1 @site2 (key=common)
*
* prevent prod db & files: simulation hoặc policy.drush.inc
* dev => prod: simulate = 1
* prod => dev: simulate = 0
* override by command line
*
* help:
* drush help site-alias
* drush help rsync
* drush help sql-sync
* drush topic (chọn site-alias)
* example alias file: 
* https://drupal.org/node/1401522
* http://drush.ws/examples/example.aliases.drushrc.php
*
*password-less ssh:
* cd /var/aegir/
* ssh-keygen (để sinh ra .ssh và publickey)
* copy content of public key to remote /var/aegir/.ssh/authorized_keys (chmod 600)
* .ssh (chmod 700)
* hoặc:
* cd /var/aegir/.drush
* drush dl drush_extra
* drush pushkey @prod
* Test OK nếu:
* ssh aegir@domain không hỏi pass
* Tại local check remote: drush @mysite.prod status (hoặc: sql-conf || --withdb)
*
*
* EC2 open port: 3306 cho Mysql
* my.cnf (#bind ip)
*
* More:
* cd /var/aegir/.drush
* drush dl drush-hosts
* drush hosts --help
* drush hs --ip=192.168.33.20 --fqdn=test.local
* drush hs --ip=192.168.33.20 --fqdn=test.local --remove
*/

/**
* dev alias
* Set the root and site_path values to point to your dev site
*/
$aliases['dev'] = array(
  'root' => '...',
  'uri'  => 'yoursite.dev',
  'path-aliases' => array(
    '%dump-dir' => '/tmp',
  ),
  'source-command-specific' => array (
    'sql-sync' => array (
      'no-cache' => TRUE,
      'structure-tables-key' => 'common',
    ),
  ),
  
// No need to modify the following settings
  'command-specific' => array (
    'sql-sync' => array (
      'sanitize' => TRUE,
      'no-ordered-dump' => TRUE,
      'simulate' => '0',
      'structure-tables' => array(
       // You can add more tables which contain data to be ignored by the database dump
        'common' => array('cache', 'cache_filter', 'cache_menu', 'cache_page', 'history',     'sessions', 'watchdog'),
      ),
      ),
       'rsync' => array (
       'simulate' => '0',
       'mode' => 'rlptDz',
       ),
    ),
);

/**
* test alias
* Set up each entry to suit your site configuration
*/
$aliases['test'] = array (
  'uri' => 'yoursite.test',
  'root' => '...',
  'path-aliases' => array(
    '%dump-dir' => '/tmp',
  ),
);

/**
* Production alias
* Set each option to match your configuration
*/
$aliases['prod'] = array (
  // This is the full site alias name from which we inherit its config.
  //'parent' => '@yoursite.dev',  
  'uri' => 'yoursite.com',
  'root' => '...',
  'remote-user' => 'ssh-user',
  'remote-host' => 'ssh-host',
  'command-specific' => array (
    'sql-sync' => array (
       'simulate' => '1',
    ),
   'rsync' => array (
      'simulate' => '1',
      'ssh-options' => '-p9999',
   ),
 ),
);
?>
