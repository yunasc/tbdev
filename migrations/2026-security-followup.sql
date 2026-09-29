-- Apply after 2026-security-auth.sql, before deploying the matching code.

-- Password-reset and email-change links expire after 24 hours (recover.php, confirmemail.php).
-- Links issued before this migration carry 0 and are rejected; users request a new one.
ALTER TABLE users ADD COLUMN editsecret_added INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER editsecret;

-- Failed-login throttle (takelogin.php): 10 failures per IP per 15 minutes.
CREATE TABLE IF NOT EXISTS login_failures (
  ip VARCHAR(64) NOT NULL,
  added INT(10) UNSIGNED NOT NULL,
  KEY ip_added (ip, added),
  KEY added (added)
) ENGINE=MyISAM;
