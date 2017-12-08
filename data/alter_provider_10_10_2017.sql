ALTER TABLE `provider`
	ADD COLUMN `field_of_activity` TEXT NOT NULL COMMENT '����� ������������' AFTER `name`,
	ADD COLUMN `offered_goods` TEXT NOT NULL COMMENT '������������ ������' AFTER `field_of_activity`,
	ADD COLUMN `legal_address` VARCHAR(255) NOT NULL COMMENT '����������� �����' AFTER `offered_goods`,
	ADD COLUMN `snils` CHAR(11) NOT NULL COMMENT '�����' AFTER `legal_address`,
	ADD COLUMN `ogrn` CHAR(13) NOT NULL COMMENT '����' AFTER `snils`,
	ADD COLUMN `site` VARCHAR(100) NULL DEFAULT NULL COMMENT '���� ��������' AFTER `ogrn`,
	ADD COLUMN `description` TEXT NULL DEFAULT NULL COMMENT '�������� �����������' AFTER `site`;