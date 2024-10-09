<?php if (!defined('PmWiki'))
    exit();



#-----------------------------------------------
#  authentication
#-----------------------------------------------
# doc: http://www.pmwiki.org/wiki/PmWiki/AuthUser


# enable user authentication in pmwiki
# ------------------------------------
# Note: users and groups must be defined before including authuser.php because otherwise they are ignored!
# set fixed hidden user accounts 
$AuthUser['admin'] = crypt('admin', 'somesalt');
$AuthUser['testuser'] = crypt('testuser', 'somesalt'); #   
# add people to admin group as follow:
$AuthUser['@admins'][] = 'admin';  # local 'hidden' pmwiki account

# -> immediately authenticates user
#      * either checks if authentication with saml already succeeded
#      * or does local authentication with local user/passwd file
include_once("$FarmD/scripts/authuser.php");


#-----------------------------------------------
#  authorization
#-----------------------------------------------
# doc: https://www.pmwiki.org/wiki/PmWiki/PasswordsAdmin

# sets DEFAULT authorization rules TO:
#  * everone : read permission
#  * authenticated users: read+write permission
#  * people in admin group :  all permissions
$DefaultPasswords['read'] = ""; # empty means : everybody allowed
$DefaultPasswords['admin'] = array('@admins');
$DefaultPasswords['attr'] = array('@admins');
$DefaultPasswords['edit'] = 'id:*';         # special syntax to allow logged in users
$DefaultPasswords['upload'] = 'id:*';

#-----------------------------------------------
#  clean urls
#-----------------------------------------------
# https://www.pmwiki.org/wiki/Cookbook/CleanUrls#URL_rewriting
# A) we use cleanurls to change url to right file
# 1. htaccess files rewriting  (one in /.htaccess redirecting top /pmwiki/, and one in /pmwiki/.htaccess rewrite as pmwiki.php argument)
#     https://localhost/Group/Page to file .../pmwiki/pmwiki.php?n=Group/Page .
# B) then we use the following to change the generated urls in  html output
# 1. changing page url used in generated content from .../pmwiki.php?n=Main.Main  to .../pmwiki.php/Group/Page instead of 
#     https://www.pmwiki.org/wiki/PmWiki/LayoutVariables#EnablePathInfo
#       Changes the handling of the page URL. When set to 1 page URL will be .../pmwiki.php/Main/Main, when set to 0 (default) it will be .../pmwiki.php?n=Main.Main .
$EnablePathInfo = 1;
# 2. strip for url used in generated content the script name pmwiki.php and the subdir pmwiki
# changing the generated url from https://localhost/pmwiki/pmwiki.php/Group/Page  to url https://localhost/Group/Page
$ScriptUrl = dirname(dirname($ScriptUrl));
