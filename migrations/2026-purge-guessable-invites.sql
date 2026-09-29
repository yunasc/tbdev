-- Run only once the fixed takeinvite.php (codes from mksecret()) is deployed.
-- Codes issued before that were md5(mt_rand(1, 1000000)) and can be enumerated.
-- Refund every outstanding code to its owner, then delete them; owners can issue fresh codes.
UPDATE users AS u
  JOIN (SELECT inviter, COUNT(*) AS n FROM invites GROUP BY inviter) AS i ON i.inviter = u.id
  SET u.invites = u.invites + i.n;
DELETE FROM invites;
