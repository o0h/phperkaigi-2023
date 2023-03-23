CREATE TABLE user
(
    id         INTEGER auto_increment,
    user_name  VARCHAR(20) NOT NULL,
    password   VARCHAR(40) NOT NULL,
    created_at DATETIME,
    PRIMARY KEY (id),
    UNIQUE user_name_index (user_name)
)
    engine = innodb;

CREATE TABLE following
(
    user_id      INTEGER,
    following_id INTEGER,
    PRIMARY KEY (user_id, following_id)
)
    engine = innodb;

CREATE TABLE status
(
    id         INTEGER auto_increment,
    user_id    INTEGER NOT NULL,
    body       VARCHAR(255),
    created_at DATETIME,
    PRIMARY KEY (id),
    INDEX user_id_index (user_id)
)
    engine = innodb;

ALTER TABLE following
    ADD FOREIGN KEY (user_id) REFERENCES user (id);

ALTER TABLE following
    ADD FOREIGN KEY (following_id) REFERENCES user (id);

ALTER TABLE status
    ADD FOREIGN KEY (user_id) REFERENCES user (id);