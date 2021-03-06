# Maintenance Banner

Maintenance Banner (mx_banner.php) is a single-file 300-line PHP administrative web application to update banner settings in a database for your user web application to SELECT and display.

It is useful when managing production web applications to inform users of scheduled maintenance by showing a banner to users immediately upon login or homepage refresh.

From an SRE perspective, as you achieve availability levels above 3x9's, it becomes more important to inform users of even brief interruptions and to coordinate maintenance windows.

mx_banner.php is also a minimal but non-trivial CRUD application that illustrates secure programming with PHP using database placeholders (?) and strip_tags().

## Screenshots

![mx_banner list of events](https://github.com/jamesbriggs/maintenance_banner/blob/master/docs/mx_banner.png)
![user application displaying event](https://github.com/jamesbriggs/maintenance_banner/blob/master/docs/mx_banner_user.png)

## Dependencies:

Requires the PHP PDO database library for either MySQL or Postgresql:

   On CentOS as root:

```bash
yum install php-pgsql
yum install php-mysql
service httpd restart
```

   On Ubuntu or Debian as root:

```bash
apt-get update
apt-get install php-pgsql
apt-get install php-mysql
/etc/init.d/apache2 restart
```


## Admin Installation:

1. copy mx_banner.php into a directory with PHP enabled and preferably SSL with a password, like Basic Authentication. (The directory should be for internal use only.)
2. set permissions:

```bash
chown root:root mx_banner.php
chmod 755 mx_banner.php
```

3. edit mx_banner.php and configure the user settings for your database, which can be local or remote.
4. create the intercom table schema using mx_banner_mysql.sql or mx_banner_pgsql.sql

   For MySQL, schema creation is as simple as:

```bash
mysql -h host_name -u root -p database_name < mx_banner_mysql.sql
```

   For Postgresql, schema creation is more complicated since sequences and roles are required.

   I'd recommend asking your DBA to do the schema creation, or use pgloader, or pgAdmin and manually do the setup while looking at mx_banner_pgsql.sql.

5. if it doesn't seem to work and no errors are displayed, edit /etc/php.ini and set `display_errors=on` and restart your web server.

## Troubleshooting:

a. To check for program syntax errors from the command line, type:

```bash
php -l mx_banner.php
No syntax errors detected in mx_banner.php
```

b. To manually insert test data into the database:
```sql
insert into intercom (dt_start, dt_end, message, type) values ('2018-01-11 14:17:03', '2018-02-27 00:00:00', 'Test message.', 'notice');
```

## User Application Installation:

1. Add an HTML div somewhere on your post-login page
2. Have your application make the following SQL query (using any programming language, not only PHP) and write the results into the div from step 1:

```sql
select id, dt_start, dt_end, message, type from intercom where dt_start <= now() and dt_end >= now() order by dt_start;
```

   In a PHP or Perl here-document:

```html
<div class="$type">
<p>$message</p>
<p>$dt_start - $dt_end</p>
</div>
```

3. You can use the type column as the CSS class to set each banner's color and icon. There are 4 values: `notice, warning, success and error.`

## Upgrades

mx_banner.php is a single-file application, so future upgrades are very simple - just copy the new version over the old file and adjust the permissions as required.

## License

Apache 2.0 License

## Copyright

James Briggs, USA 2018.

You may use Maintenance Banner on a non-exclusive basis and re-distribute in your own work. I provide no guarantee and assume no liability for your use of this program.

## Contact

Please create a Github issue with any improvements or security issues.

## Todo

- JavaScript input validation could be fancier.
- Security is always evolving in web programs.
- The PHP PDO Postgresql date range error exception is not raised for some reason.
- The Postgresql create table script could be simplified.
- Actual sample code for user application integration in several programming languages.
- Could include pre-made CSS and icons for quicker deployment setup, like the Clarity icons: https://vmware.github.io/clarity/icons/icon-sets#core-shapes

