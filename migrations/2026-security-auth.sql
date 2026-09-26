ALTER TABLE users MODIFY COLUMN passhash VARCHAR(255) NOT NULL DEFAULT '';

CREATE TABLE IF NOT EXISTS auth_tokens (
  token_hash CHAR(64) NOT NULL,
  uid INT(10) NOT NULL,
  passhash_fingerprint CHAR(64) NOT NULL,
  expires INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (token_hash),
  KEY uid (uid),
  KEY expires (expires)
) ENGINE=MyISAM;
