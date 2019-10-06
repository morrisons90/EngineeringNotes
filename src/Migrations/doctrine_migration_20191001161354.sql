-- Doctrine Migration File Generated on 2019-10-01 16:13:54

-- Version 20191001141045
ALTER TABLE note_topic CHANGE ref ref VARCHAR(10) NOT NULL;
INSERT INTO migration_versions (version, executed_at) VALUES ('20191001141045', CURRENT_TIMESTAMP);

-- Version 20191001141220
ALTER TABLE note_topic ADD ref VARCHAR(10) NOT NULL;
INSERT INTO migration_versions (version, executed_at) VALUES ('20191001141220', CURRENT_TIMESTAMP);

-- Version 20191001141225
ALTER TABLE note_topic ADD ref VARCHAR(10) NOT NULL;
INSERT INTO migration_versions (version, executed_at) VALUES ('20191001141225', CURRENT_TIMESTAMP);

-- Version 20191001141314
ALTER TABLE note_topic ADD ref VARCHAR(10) NOT NULL;
INSERT INTO migration_versions (version, executed_at) VALUES ('20191001141314', CURRENT_TIMESTAMP);
