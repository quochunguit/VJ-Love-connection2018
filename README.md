BzCMSCustom
=======================

Introduction
------------
This is a cms of Bizzon. Use Zend Framework 2.0

I. Installation CAN use COMPOSER UPDATE (php >=5.4 or higher)
------------
1. Update composer: 
	- cd your_project_folder
	- composer update
2. application/config/autoload/
	- copy and rename file "env.php.dist" to "env.php"
	- copy and rename file "database.development.php.dist" to "database.development.php"
3. application/config/defines/
	- copy and rename file "constant.php.dist" to "constant.development.php"

II. Installation CANNOT use COMPOSER UPDATE (php 5.3.*)
------------
1. Extract file vendor_php_5_3_3.zip
2. application/config/autoload/
	- copy and rename file "env.php.dist" to "env.php"
	- copy and rename file "database.development.php.dist" to "database.development.php"
3. application/config/defines/
	- copy and rename file "constant.php.dist" to "constant.development.php"	
4. Change code
	- application/module/Core/Form/CoreForm.php UNCOMMENT line 20 and COMMENT line 19
	- application/module/Core/InputFilter/MyHtmlPurifier.php UNCOMMENT line 4

III. Removed Folder, Modules, Files are not used
------------
	- Remove Folders: root_folder/zdb, root_folder/ztools
	- Remove Files: root_folder/zdc.php, root_folder/zamn.php