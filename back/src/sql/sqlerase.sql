USE dev_db;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS completed_work_media;
DROP TABLE IF EXISTS completed_work;
DROP TABLE IF EXISTS review;
DROP TABLE IF EXISTS request;
DROP TABLE IF EXISTS notification;
DROP TABLE IF EXISTS booking;
DROP TABLE IF EXISTS availability_slot;

DROP TABLE IF EXISTS provider_language;
DROP TABLE IF EXISTS provider_job;
DROP TABLE IF EXISTS job;
DROP TABLE IF EXISTS provider_hard_skills;
DROP TABLE IF EXISTS provider_soft_skills;
DROP TABLE IF EXISTS hard_skill;
DROP TABLE IF EXISTS soft_skill;

DROP TABLE IF EXISTS article_image;
DROP TABLE IF EXISTS article_content;
DROP TABLE IF EXISTS article_section;
DROP TABLE IF EXISTS article;

DROP TABLE IF EXISTS service_image;
DROP TABLE IF EXISTS service_content;
DROP TABLE IF EXISTS service_section;
DROP TABLE IF EXISTS service;

DROP TABLE IF EXISTS experience;
DROP TABLE IF EXISTS education;
DROP TABLE IF EXISTS social_link;
DROP TABLE IF EXISTS location;

DROP TABLE IF EXISTS client;
DROP TABLE IF EXISTS provider;
DROP TABLE IF EXISTS language;
DROP TABLE IF EXISTS country;

SET FOREIGN_KEY_CHECKS = 1;
