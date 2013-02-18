-- Users
CREATE TABLE IF NOT EXISTS user (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(30) UNIQUE,
    password VARCHAR(64) NOT NULL,
    salt VARCHAR(3) NOT NULL
);

-- Folders
CREATE TABLE IF NOT EXISTS folder (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100),
    sort_dir VARCHAR(3)
);

INSERT INTO "folder" VALUES(1,'GW2', 'asc');
INSERT INTO "folder" VALUES(2,'Tech', 'asc');
INSERT INTO "folder" VALUES(3,'Blog', 'asc');
INSERT INTO "folder" VALUES(4,'Photography', 'asc');
INSERT INTO "folder" VALUES(5,'Artists', 'asc');
INSERT INTO "folder" VALUES(6,'365 Project', 'asc');
INSERT INTO "folder" VALUES(7,'Comics', 'asc');

-- feeds
CREATE TABLE IF NOT EXISTS feed (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    name        VARCHAR(255),
    url         VARCHAR(255),
    sort_dir    VARCHAR(3),
    update_freq INTEGER, -- minutes
    folder_id   INTEGER,

    FOREIGN KEY (folder_id)
        REFERENCES folder (id)
        ON DELETE SET NULL
);

-- default feeds
INSERT INTO "feed" VALUES(1,'365 Day Series','http://api.flickr.com/services/feeds/groups_pool.gne?id=684800@N21&lang=en-us&format=rss_200','asc',60,6);
INSERT INTO "feed" VALUES(2,'365 Pool','http://api.flickr.com/services/feeds/groups_pool.gne?id=1386211@N23&lang=en-us&format=rss_200','asc',60,6);
INSERT INTO "feed" VALUES(3,'A Mighty Girl','http://www.amightygirl.com/blog/feed','asc',1440,3);
INSERT INTO "feed" VALUES(4,'Ars Technica','http://feeds.arstechnica.com/arstechnica/everything','asc',60,2);
INSERT INTO "feed" VALUES(5,'BGR','http://feeds.feedburner.com/TheBoyGeniusReport','asc',60,2);
INSERT INTO "feed" VALUES(6,'Chase Jarvis','http://feeds.feedburner.com/ChaseJarvis','asc',1440,4);
INSERT INTO "feed" VALUES(7,'CNET News','http://feeds.feedburner.com/cnet/tcoc','asc',30,2);
INSERT INTO "feed" VALUES(8,'Dark Roasted Blend','http://feeds.feedburner.com/TheThrillingWonderStory','asc',1440,3);
INSERT INTO "feed" VALUES(9,'Digital Photography School','http://feeds.feedburner.com/DigitalPhotographySchool','asc',1440,4);
INSERT INTO "feed" VALUES(10,'DPS How I took It','http://digital-photography-school.com/forum/external.php?type=RSS2&forumids=25','asc',1440,4);
INSERT INTO "feed" VALUES(11,'Dilbert','http://feeds.feedburner.com/DilbertDailyStrip','asc',1440,7);
INSERT INTO "feed" VALUES(12,'Dulfy GW2','http://dulfy.net/category/gw2/feed/','asc',1440,1);
INSERT INTO "feed" VALUES(13,'eBooks','http://www.wowebook.org/feed','asc',1440,NULL);
INSERT INTO "feed" VALUES(14,'Galaxy S3 Root','http://galaxys3root.com/feed/','asc',1440,NULL);
INSERT INTO "feed" VALUES(15,'GW2 News','https://forum-en.guildwars2.com/forum/info/news.rss','asc',1440,1);
INSERT INTO "feed" VALUES(16,'GW2 Reddit','http://www.reddit.com/r/Guildwars2.rss?limit=1000','asc',60,1);
INSERT INTO "feed" VALUES(17,'Notcot','http://www.notcot.org/atom.php','asc',60,3);
INSERT INTO "feed" VALUES(18,'Photojojo','http://feeds2.feedburner.com/Photojojo','asc',1440,4);
INSERT INTO "feed" VALUES(19,'Photoshop Tutorials','http://feeds.feedburner.com/photoshoptutorials/new','asc',1440,4);
INSERT INTO "feed" VALUES(20,'PopPhoto','http://feeds.popphoto.com/c/34565/f/637826/index.rss','asc',1440,4);
INSERT INTO "feed" VALUES(21,'Rekha','http://api.flickr.com/services/feeds/photos_public.gne?id=28387478@N00&lang=en-us&format=rss_200','asc',1440,5);
INSERT INTO "feed" VALUES(22,'Rekha Favorites','http://api.flickr.com/services/feeds/photos_faves.gne?nsid=28387478@N00&lang=en-us&format=rss_200','asc',1440,5);
INSERT INTO "feed" VALUES(23,'Smitten Kitchen','http://feeds.feedburner.com/smittenkitchen','asc',1440,NULL);
INSERT INTO "feed" VALUES(24,'Techmeme','http://www.techmeme.com/feed.xml','asc',30,2);
INSERT INTO "feed" VALUES(25,'Technorati','http://feeds09.technorati.com/tr-technology','asc',1440,2);
INSERT INTO "feed" VALUES(26,'Telegraph','http://www.telegraph.co.uk/technology/rss','asc',60,2);
INSERT INTO "feed" VALUES(27,'Hitomi','http://api.flickr.com/services/feeds/photos_public.gne?id=49460796@N03&lang=en-us&format=rss_200','asc',1440,5);
INSERT INTO "feed" VALUES(28,'ILoveStrawberries','http://api.flickr.com/services/feeds/photos_public.gne?id=27261901@N08&lang=en-us&format=rss_200','asc',1440,5);
INSERT INTO "feed" VALUES(29,'Lyss Nichole','http://api.flickr.com/services/feeds/photos_public.gne?id=56326689@N05&lang=en-us&format=rss_200','asc',1440,5);
INSERT INTO "feed" VALUES(30,'xkcd','http://xkcd.com/rss.xml','asc',1440,7);

-- feed updates
CREATE TABLE IF NOT EXISTS update_log (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    ts          INTEGER,
    count       INTEGER,
    new         INTEGER,
    feed_id     INTEGER,

    FOREIGN KEY (feed_id)
        REFERENCES feed (id)
        ON DELETE SET NULL
);

-- posts
CREATE TABLE IF NOT EXISTS post (
    id      INTEGER PRIMARY KEY AUTOINCREMENT,
    title   VARCHAR(255),
    ts      INTEGER,
    text    TEXT,
    link    TEXT,
    guid    TEXT,
    read    BOOLEAN,
    stared  BOOLEAN,
    feed_id INTEGER,

    FOREIGN KEY (feed_id)
        REFERENCES feed (id)
        ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS post_guid_idx ON post (guid);
CREATE INDEX IF NOT EXISTS post_ts_idx ON post (ts DESC);
