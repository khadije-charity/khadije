ALTER TABLE `mokebusers` ADD `forceexit` bit(1) NULL DEFAULT NULL;
ALTER TABLE `mokebusers` ADD `forceexitdate` datetime NULL DEFAULT NULL;
ALTER TABLE `mokebusers` ADD `oldposition` int(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `mokebusers` ADD `noposition` bit(1) NULL DEFAULT NULL;

