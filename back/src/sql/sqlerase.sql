USE dev_db;

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM booking;
DELETE FROM availability_slot;
DELETE FROM completed_work_media;
DELETE FROM completed_work;
DELETE FROM provider_skill;
DELETE FROM provided_service;
DELETE FROM provider_diploma;
DELETE FROM review;
DELETE FROM request;
DELETE FROM notification;
DELETE FROM client;
DELETE FROM provider;
DELETE FROM skill;

ALTER TABLE booking AUTO_INCREMENT = 1;
ALTER TABLE availability_slot AUTO_INCREMENT = 1;
ALTER TABLE completed_work_media AUTO_INCREMENT = 1;
ALTER TABLE completed_work AUTO_INCREMENT = 1;
ALTER TABLE provider_skill AUTO_INCREMENT = 1;
ALTER TABLE provided_service AUTO_INCREMENT = 1;
ALTER TABLE provider_diploma AUTO_INCREMENT = 1;
ALTER TABLE review AUTO_INCREMENT = 1;
ALTER TABLE request AUTO_INCREMENT = 1;
ALTER TABLE notification AUTO_INCREMENT = 1;
ALTER TABLE client AUTO_INCREMENT = 1;
ALTER TABLE provider AUTO_INCREMENT = 1;
ALTER TABLE skill AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;
