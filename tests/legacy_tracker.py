"""Integration check for an isolated local database restored from the legacy schema.

See README.md for the prepare/migrate/verify sequence. This script writes test data.
"""

import argparse
import hashlib
import http.cookiejar
import re
import uuid
import subprocess
import urllib.parse
import urllib.request
import xml.etree.ElementTree as ET
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
USERNAME = "tbdev_fixture"
PASSWORD = "Тестовый пароль!"
PASSKEY = hashlib.md5(b"tbdev fixture passkey").hexdigest()
# Include bytes that cannot travel as UTF-8, NUL, a quote and a backslash.
PEER_ID = b"-TB8500-" + bytes.fromhex("ff00275c80c3a91a3b3f2b20")
BASE_URL = "http://127.0.0.1:8585"
MODES = {9001: ("no", 2500), 9002: ("yes", 0), 9003: ("silver", 1250)}


def encode(value):
    if isinstance(value, bytes):
        return str(len(value)).encode() + b":" + value
    if isinstance(value, int):
        return b"i" + str(value).encode() + b"e"
    if isinstance(value, list):
        return b"l" + b"".join(map(encode, value)) + b"e"
    return b"d" + b"".join(encode(k) + encode(v) for k, v in sorted(value.items())) + b"e"


def decode(data, offset=0, spans=None):
    tag = data[offset:offset + 1]
    if tag == b"i":
        end = data.index(b"e", offset)
        return int(data[offset + 1:end]), end + 1
    if tag == b"l":
        offset += 1
        values = []
        while data[offset] != ord("e"):
            value, offset = decode(data, offset)
            values.append(value)
        return values, offset + 1
    if tag == b"d":
        offset += 1
        values = {}
        while data[offset] != ord("e"):
            key, start = decode(data, offset)
            value, offset = decode(data, start)
            if spans is not None:
                spans[key] = data[start:offset]
            values[key] = value
        return values, offset + 1
    end = data.index(b":", offset)
    length = int(data[offset:end])
    return data[end + 1:end + 1 + length], end + 1 + length


def metainfo(tid):
    text = f"Старая раздача {tid}"
    name = text.encode("cp1251") + b".bin"
    piece_hash = hashlib.sha1(bytes(range(256)) * 20).digest()
    info = {b"length": 5000, b"name": name, b"piece length": 16384,
            b"pieces": piece_hash, b"private": 1}
    torrent = {b"announce": b"http://legacy.example.invalid/announce.php", b"info": info}
    if tid == 9003:
        torrent[b"announce-list"] = [[torrent[b"announce"]]]
    return text, torrent, hashlib.sha1(encode(info)).digest()


def database(socket, statement):
    return subprocess.check_output(
        ["mariadb", "--no-defaults", f"--socket={socket}", "--default-character-set=utf8mb4",
         "--batch", "--skip-column-names", "tbdev_legacy"],
        input=("SET SESSION sql_mode='NO_ENGINE_SUBSTITUTION';\n" + statement).encode(),
    ).decode().strip()


def legacy_text(value):
    return "CONVERT(X'" + value.encode("cp1251").hex() + "' USING cp1251)"


def prepare(socket):
    assert database(socket, "SELECT @@character_set_database") == "cp1251"
    assert database(socket, "SELECT COUNT(*) FROM users") == "0", "Use a clean test DB"
    password_hash = subprocess.check_output(
        ["php", "-r", 'echo password_hash(trim(fgets(STDIN)), PASSWORD_BCRYPT);'],
        input=PASSWORD.encode("cp1251"),
    ).decode()
    database(socket, f"""
        INSERT INTO users(id, username, passhash, passkey, class, status, added, last_access,
                          uploaded, downloaded)
        VALUES (1, '{USERNAME}', '{password_hash}', '{PASSKEY}', 6, 'confirmed',
                NOW(), NOW(), 10000000, 20000000);
    """)
    for tid, (mode, _) in MODES.items():
        title, torrent, info_hash = metainfo(tid)
        (ROOT / "torrents" / f"{tid}.torrent").write_bytes(encode(torrent))
        database(socket, f"""
            INSERT INTO torrents(id, name, filename, info_hash, owner, category, size, numfiles,
                                 type, visible, free, seeders, leechers, added, last_action)
            VALUES ({tid}, {legacy_text(title)}, {legacy_text(title + '.torrent')},
                    '{info_hash.hex()}', 1, 1, 5000, 1, 'single', 'yes', '{mode}', 0, 1, NOW(), NOW());
            INSERT INTO files(torrent, filename, size)
            VALUES ({tid}, {legacy_text(title + '.bin')}, 5000);
            INSERT INTO peers(torrent, peer_id, ip, port, uploaded, downloaded, to_go, userid,
                              passkey, connectable, started, last_action, prev_action)
            VALUES ({tid}, X'{PEER_ID.hex()}', '127.0.0.1', 51413, 40, 100, 4900, 1,
                    '{PASSKEY}', 'no', NOW(), NOW(), NOW() - INTERVAL 30 SECOND);
            INSERT INTO snatched(torrent, userid, uploaded, downloaded, to_go, startdat, last_action)
            VALUES ({tid}, 1, 40, 100, 4900, NOW(), NOW());
        """)
        assert database(socket, f"SELECT HEX(peer_id) FROM peers WHERE torrent={tid}") == PEER_ID.hex().upper()
    print("Prepared 3 CP1251 torrents, binary legacy peers and non-zero traffic counters")


def fetch(opener, route, data=None, headers=None):
    request = urllib.request.Request(BASE_URL + route, data=data, headers={
        "User-Agent": "TBDev local regression",
        **({"Origin": BASE_URL} if data is not None else {}),
        **(headers or {}),
    })
    with opener.open(request, timeout=15) as response:
        return response.headers, response.read()


def tracker(route, **params):
    query = urllib.parse.urlencode(params, quote_via=urllib.parse.quote)
    _, body = fetch(urllib.request.build_opener(), f"/{route}.php?{query}")
    value, end = decode(body)
    assert end == len(body), f"{route}: extra bytes in response"
    assert b"failure reason" not in value, value.get(b"failure reason")
    return value


def verify(socket):
    assert database(socket, "SELECT @@character_set_database") == "utf8mb4"
    assert database(socket, "SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE "
                           "TABLE_SCHEMA=DATABASE() AND TABLE_NAME='peers' AND COLUMN_NAME='peer_id'") == "varbinary"
    assert database(socket, "SELECT uploaded, downloaded FROM users WHERE id=1") == "10000000\t20000000"
    assert database(socket, "SELECT HEX(peer_id), uploaded, downloaded FROM peers ORDER BY torrent").splitlines() == [
        f"{PEER_ID.hex().upper()}\t40\t100"] * 3
    browser = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
    headers, login = fetch(browser, "/login.php")
    assert headers.get_content_charset() == "utf-8"
    assert "Пользователь:" in login.decode("utf-8")
    old_hash = database(socket, "SELECT passhash FROM users WHERE id=1")
    fetch(browser, "/takelogin.php", urllib.parse.urlencode({"username": USERNAME, "password": PASSWORD}).encode())
    assert database(socket, "SELECT passhash FROM users WHERE id=1") != old_hash
    assert database(socket, "SELECT COUNT(*) FROM auth_tokens WHERE uid=1") != "0"

    uploaded_total, downloaded_total = 10000000, 20000000
    for tid, (mode, expected_download) in MODES.items():
        title, torrent, info_hash = metainfo(tid)
        assert database(socket, f"SELECT name, info_hash FROM torrents WHERE id={tid}") == f"{title}\t{info_hash.hex()}"
        assert database(socket, f"SELECT filename FROM files WHERE torrent={tid}") == title + ".bin"
        headers, body = fetch(browser, f"/download.php?id={tid}")
        assert headers.get_content_type() == "application/x-bittorrent"
        spans = {}
        decoded, end = decode(body, spans=spans)
        assert end == len(body) and spans[b"info"] == encode(torrent[b"info"])
        assert hashlib.sha1(spans[b"info"]).digest() == info_hash
        announce_url = (BASE_URL + "/announce.php?passkey=" + PASSKEY).encode()
        if b"announce-list" in torrent:
            assert decoded[b"announce-list"] == torrent[b"announce-list"] + [[announce_url]]
        else:
            assert decoded[b"announce"] == announce_url

        params = dict(passkey=PASSKEY, info_hash=info_hash, peer_id=PEER_ID, port=51413, compact=1)
        def announce(event, uploaded, downloaded, left):
            database(socket, f"UPDATE peers SET prev_action=NOW()-INTERVAL 30 SECOND WHERE torrent={tid}")
            reply = tracker("announce", **params, event=event, uploaded=uploaded, downloaded=downloaded, left=left)
            assert isinstance(reply[b"interval"], int) and isinstance(reply[b"peers"], bytes)

        announce("started", 40, 100, 4900)
        announce("", 1040, 2600, 2400)
        announce("", 1040, 2600, 2400)
        uploaded_total += 1000
        downloaded_total += expected_download
        assert database(socket, "SELECT uploaded, downloaded FROM users WHERE id=1") == f"{uploaded_total}\t{downloaded_total}"
        announce("completed", 1040, 2600, 0)
        stats = tracker("scrape", info_hash=info_hash)[b"files"][info_hash]
        assert (stats[b"complete"], stats[b"incomplete"], stats[b"downloaded"]) == (1, 0, 1)
        announce("stopped", 1040, 2600, 0)
        assert database(socket, f"SELECT COUNT(*) FROM peers WHERE torrent={tid}") == "0"
        assert database(socket, f"SELECT seeders, leechers, times_completed FROM torrents WHERE id={tid}") == "0\t0\t1"
        assert database(socket, f"SELECT uploaded, downloaded, finished FROM snatched WHERE torrent={tid}") == "1040\t2600\tyes"
        assert database(socket, "SELECT uploaded, downloaded FROM users WHERE id=1") == f"{uploaded_total}\t{downloaded_total}"
        print(f"{mode}: exact legacy info hash, downloaded bytes +{expected_download}, uploaded bytes +1000")

    # Exercise the new-peer INSERT path as well as the migrated row update path.
    tid = 9001
    fresh_peer = PEER_ID[:-1] + b"!"
    params = dict(passkey=PASSKEY, info_hash=metainfo(tid)[2], peer_id=fresh_peer,
                  port=51413, compact=0, uploaded=0, downloaded=0, left=5000)
    assert tracker("announce", **params, event="started")[b"peers"] == []
    assert database(socket, f"SELECT HEX(peer_id) FROM peers WHERE torrent={tid}") == fresh_peer.hex().upper()
    tracker("announce", **params, event="stopped")
    assert database(socket, f"SELECT COUNT(*) FROM peers WHERE torrent={tid}") == "0"
    upload(socket, browser)
    print("Legacy DB rows, HTTP download, announce/scrape, traffic and utf8mb4 round trip passed")


def upload(socket, browser):
    _, form = fetch(browser, "/upload.php")
    token = re.search(rb'<form name="upload".*?name="csrf_token" value="([^"]+)"', form).group(1).decode()
    name = "Проверка Unicode 🧪"
    legacy_name = "Старое имя.bin".encode("cp1251")
    info = {b"length": 400, b"name": legacy_name, b"piece length": 16384,
            b"pieces": hashlib.sha1(b"z" * 400).digest()}
    torrent = encode({b"announce": b"http://legacy.example.invalid/announce.php", b"info": info})

    boundary = uuid.uuid4().hex
    fields = {"csrf_token": token, "name": name, "descr": name, "type": "1"}
    body = b"".join(
        f'--{boundary}\r\nContent-Disposition: form-data; name="{key}"\r\n\r\n{value}\r\n'.encode()
        for key, value in fields.items()
    )
    body += (
        f'--{boundary}\r\nContent-Disposition: form-data; name="tfile"; filename="unicode.torrent"\r\n'
        "Content-Type: application/x-bittorrent\r\n\r\n"
    ).encode() + torrent + f"\r\n--{boundary}--\r\n".encode()
    fetch(browser, "/takeupload.php", body, {"Content-Type": f"multipart/form-data; boundary={boundary}"})
    row = database(socket, "SELECT id, info_hash, name FROM torrents WHERE filename='unicode.torrent'")
    assert row, "Multipart upload did not create a torrent"
    tid, info_hash, title = row.split("\t")
    assert title == name
    assert database(socket, f"SELECT filename FROM files WHERE torrent={int(tid)}") == legacy_name.decode("cp1251")
    headers, body = fetch(browser, f"/download.php?id={tid}")
    assert headers.get_content_type() == "application/x-bittorrent"
    spans = {}
    decoded, end = decode(body, spans=spans)
    assert end == len(body) and hashlib.sha1(spans[b"info"]).hexdigest() == info_hash
    assert decoded[b"info"][b"name"] == legacy_name and decoded[b"info"][b"pieces"] == info[b"pieces"]
    database(socket, f"UPDATE torrents SET visible='yes' WHERE id={int(tid)}")
    headers, rss = fetch(browser, "/rss.php")
    assert headers.get_content_charset() == "utf-8"
    assert name in [node.text for node in ET.fromstring(rss).findall("./channel/item/title")]
    print("Multipart upload preserved legacy metainfo bytes and displayed an emoji title in RSS")



if __name__ == "__main__":
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("phase", choices=("prepare", "verify"))
    parser.add_argument("--socket", required=True)
    args = parser.parse_args()
    (prepare if args.phase == "prepare" else verify)(args.socket)
