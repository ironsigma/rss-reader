-- Users
CREATE TABLE user (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(30) UNIQUE,
    password VARCHAR(64) NOT NULL,
    salt VARCHAR(3) NOT NULL
);

-- Folders
CREATE TABLE folder (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    per_page    INTEGER NOT NULL,
    newest_first VARCHAR(3) NOT NULL CHECK(newest_first IN(0, 1)),
    user_id     INTEGER,

    FOREIGN KEY (user_id) REFERENCES user (id)
        ON DELETE CASCADE
);

-- feeds
CREATE TABLE feed (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    name        VARCHAR(255) NOT NULL,
    url         VARCHAR(255) NOT NULL,
    newest_first    VARCHAR(3) NOT NULL CHECK(newest_first IN(0, 1)),
    update_freq INTEGER NOT NULL,
    per_page    INTEGER NOT NULL,
    folder_id   INTEGER,
    user_id     INTEGER,

    FOREIGN KEY (folder_id) REFERENCES folder (id)
        ON DELETE SET NULL,

    FOREIGN KEY (user_id) REFERENCES user (id)
        ON DELETE CASCADE
);

-- posts
CREATE TABLE post (
    id        INTEGER PRIMARY KEY AUTOINCREMENT,
    title     VARCHAR(255) NOT NULL,
    published INTEGER NOT NULL,
    text      TEXT NOT NULL,
    link      TEXT NOT NULL,
    guid      TEXT NOT NULL,
    read      INTEGER NOT NULL CHECK(read IN(0, 1)),
    stared    INTEGER NOT NULL CHECK(stared IN(0, 1)),
    feed_id   INTEGER NOT NULL,

    FOREIGN KEY (feed_id) REFERENCES feed (id)
        ON DELETE CASCADE
);

-- feed updates
CREATE TABLE update_log (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    updated     INTEGER NOT NULL,
    total_count INTEGER NOT NULL,
    new_count   INTEGER NOT NULL,
    feed_id     INTEGER NOT NULL,

    FOREIGN KEY (feed_id) REFERENCES feed (id)
        ON DELETE SET NULL
);

CREATE INDEX post_stared_idx ON post (stared);
CREATE INDEX post_guid_idx ON post (guid);
CREATE INDEX post_date_idx ON post (published DESC);
