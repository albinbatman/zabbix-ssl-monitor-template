# Zabbix: Web SSL Certificate Monitoring Template

This template allows you to turn web monitoring steps from Zabbix into an LLD to also monitor them for certificate expiration. It requires two PHP files, two UserParameters, an SQL user with SELECT permissions and this template to get up and running.

# Requirements

1. PHP 7.x + mysqli module
2. SQL user with SELECT permissions (do not use root please)
3. Some changes to zabbix_agentd.conf

## Installation

1. Run the following command:

```bash
mkdir /etc/zabbix/scripts && git clone https://github.com/albinbatman/zabbix-ssl-monitor-template /etc/zabbix/scripts
cd /etc/zabbix/scripts/zabbix-ssl-monitor-template
```
2. Open up SQL.php and adjust the following lines
```php
private $host   = "dns or ip to database";
private $user   = "your username for sql";
private $pass   = "your user sql password";
private $db     = "zabbix";
private $table  = "httptest"; // keep this as it is
```

3. Edit zabbix_agentd.conf with the following:
```bash
UserParameter=get.urls,php /etc/zabbix/scripts/zabbix-ssl-monitor-template/scripts/SQL.php
UserParameter=get.ssl[*],php /etc/zabbix/scripts/zabbix-ssl-monitor-template/scripts/SSL.php $1
```

## Usage

1. I recommend creating a new host with a Zabbix agent (e.g Zabbix server).
2. Add template **Template Web SSL Certificate Monitor** to host from above.
   1. Add a new web scenario (**name** must be a valid full URL, like https://example.com)
   2. SQL.php only sends back URLs with https:// in it to the template.
3. Enjoy!


## Tip
Since the items will go into unsupported state if an error with the script occurs (such as can't verify certificate), please follow this documentation to setup alerting for it: [Receiving notification on unsupported items [Zabbix Documentation 5.0]](https://www.zabbix.com/documentation/current/manual/config/notifications/unsupported_item)

Another tip is to only use this template once per database, ie do not use this template on two hosts that have access to the same database because it will create duplicates.

## License
[MIT](https://choosealicense.com/licenses/mit/)
