<?php 
$UserName = $_REQUEST['UserName'];
$DbPassword = $_REQUEST['DbPassword'];
$SubDomain = $_REQUEST['SubDomain'];

//echo " $SubDomain $UserName $DbPassword";

//shell_exec("cp -r safecopy/* sub13/");
shell_exec("cp -r safecopy/* ".$_REQUEST['SubDomain']."/");
shell_exec("find ".$_REQUEST['SubDomain']."/mysite -type f -iname '_conn.php' -exec sed -i 's/ezb2554_ss/".$UserName."_".$_REQUEST['SubDomain']."/' \"{}\" +;");
shell_exec("find ".$_REQUEST['SubDomain']."/mysite -type f -iname '_conn.php' -exec sed -i 's/ezb2554_admin/".$UserName."_".$_REQUEST['SubDomain']."/' \"{}\" +;");
shell_exec("find ".$_REQUEST['SubDomain']."/mysite -type f -iname '_conn.php' -exec sed -i 's/12qwaszx/".$DbPassword."/' \"{}\" +;");
    

$data = "
### SILVERSTRIPE START ###
<Files *.ss>
	Order deny,allow
	Deny from all
	Allow from 127.0.0.1
</Files>

<Files web.config>
	Order deny,allow
	Deny from all
</Files>

ErrorDocument 404 /assets/error-404.html
ErrorDocument 500 /assets/error-500.html

<IfModule mod_alias.c>
	RedirectMatch 403 /silverstripe-cache(/|$)
</IfModule>

<IfModule mod_rewrite.c>
	SetEnv HTTP_MOD_REWRITE On
	RewriteEngine On
	RewriteBase '/'

	RewriteCond %{REQUEST_URI} ^(.*)$
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteRule .* framework/main.php?url=%1&%{QUERY_STRING} [L]
</IfModule>
### SILVERSTRIPE END ###
";
@file_put_contents($_REQUEST['SubDomain'].'/.htaccess', $data);



$url="http://smallbusiness.info/create_db.php?Task=ConfigureSubDomainSystem&UserName=".$UserName."&SubDomain=". $SubDomain."&DbPassword=".$DbPassword;
header("Location:".$url);
