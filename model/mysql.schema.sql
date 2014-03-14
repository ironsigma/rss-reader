-- Users
CREATE TABLE user (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(30) UNIQUE,
    password VARCHAR(64) NOT NULL,
    salt VARCHAR(3) NOT NULL
);

-- Folders
CREATE TABLE folder (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    per_page     SMALLINT UNSIGNED NOT NULL,
    newest_first BOOLEAN NOT NULL,
    user_id     BIGINT,

    FOREIGN KEY (user_id) REFERENCES user (id)
        ON DELETE CASCADE
);

-- feeds
CREATE TABLE feed (
    id          BIGINT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(255) NOT NULL,
    url         VARCHAR(2083) NOT NULL,
    active      BOOLEAN NOT NULL,
    newest_first    BOOLEAN NOT NULL,
    update_freq INTEGER NOT NULL,
    per_page    SMALLINT UNSIGNED NOT NULL,
    folder_id   BIGINT,
    user_id     BIGINT,

    FOREIGN KEY (folder_id) REFERENCES folder (id)
        ON DELETE SET NULL,

    FOREIGN KEY (user_id) REFERENCES user (id)
        ON DELETE CASCADE
);

-- posts
CREATE TABLE post (
    id        BIGINT PRIMARY KEY AUTO_INCREMENT,
    title     VARCHAR(255) NOT NULL,
    published DATETIME NOT NULL,
    text      TEXT NOT NULL,
    link      VARCHAR(2083) NOT NULL,
    guid      VARCHAR(32) NOT NULL,
    feed_id   BIGINT NOT NULL,

    FOREIGN KEY (feed_id) REFERENCES feed (id)
        ON DELETE CASCADE
);

CREATE TABLE post_state (
    id        BIGINT PRIMARY KEY AUTO_INCREMENT,
    `read`    BOOLEAN NOT NULL,
    stared    BOOLEAN NOT NULL,
    post_id   BIGINT NOT NULL,
    user_id   BIGINT NOT NULL,

    FOREIGN KEY (post_id) REFERENCES post (id)
        ON DELETE CASCADE,

    FOREIGN KEY (user_id) REFERENCES user (id)
        ON DELETE CASCADE
);

-- feed updates
CREATE TABLE update_log (
    id          BIGINT PRIMARY KEY AUTO_INCREMENT,
    updated     DATETIME NOT NULL,
    total_count INT UNSIGNED NOT NULL,
    new_count   INT UNSIGNED NOT NULL,
    feed_id     BIGINT NOT NULL,

    FOREIGN KEY (feed_id) REFERENCES feed (id)
        ON DELETE CASCADE
);

CREATE INDEX post_stared_idx ON post (stared);
CREATE INDEX post_guid_idx ON post (guid);
CREATE INDEX post_date_idx ON post (published DESC);
