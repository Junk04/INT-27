CREATE USER connection_user WITH PASSWORD '123';

CREATE TABLE users (
    username VARCHAR(20) PRIMARY KEY,
    password VARCHAR(20) NOT NULL
);

INSERT INTO users (username, password) VALUES
('user1', 'user1_password'),
('user2', 'user2_password');

CREATE TABLE information (
    column1 VARCHAR(100),
    column2 VARCHAR(100),
    column3 VARCHAR(100)
);

INSERT INTO information (column1, column2, column3) VALUES
('data1', 'data2', 'data3'),
('info1', 'info2', 'info3');

GRANT ALL PRIVILEGES ON DATABASE main TO connection_user;
GRANT ALL PRIVILEGES ON TABLE users TO connection_user;
GRANT ALL PRIVILEGES ON TABLE information TO connection_user;
