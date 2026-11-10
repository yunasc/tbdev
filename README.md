# TBDev Yuna Scatari Edition

Run this version on PHP 8.5 with the existing Windows-1251 application and CP1251 database. Enable `mysqli`, `mbstring`, `iconv`, and `openssl`. Enable `gd` for CAPTCHA and images.

## Local setup

1. For a fresh installation, create an empty database with `CHARACTER SET cp1251 COLLATE cp1251_general_ci`, then import `install/database.sql`. The file contains `DROP TABLE` statements and is for empty databases only.
2. Set `$mysql_host`, `$mysql_user`, `$mysql_pass` and `$mysql_db` in `include/secrets.local.php`. For a Unix socket use `localhost:/path/to/mysql.sock`. The connection uses `cp1251`.
3. Set a random `$_COOKIE_SALT` and `$site_url = 'http://127.0.0.1:8585';` in `include/config.local.php`. Give the PHP process write access to `torrents/`.
4. Start the local server from the checkout with `php -S 127.0.0.1:8585 -t . dev/router.php`.

For an existing installation, test this version with a copy of its database and `torrents/` directory before upgrading PHP.

## Compatibility check

Use a dedicated MariaDB instance and an empty CP1251 database named `tbdev_legacy` loaded from the upstream schema at `46c3c6c`. When loading that schema, use client charset `cp1251` and session SQL mode `NO_ENGINE_SUBSTITUTION`. Point the local PHP server at that database. The MariaDB client in the test connects as the current local user through its Unix socket.

```sh
python3 tests/legacy_tracker.py prepare --socket /path/to/mysql.sock
python3 tests/legacy_tracker.py verify --socket /path/to/mysql.sock
```

The test writes fixture data. It checks login, the info bytes of old downloads, traffic accounting with three download modes, peer updates and the CP1251 upload and RSS path.
