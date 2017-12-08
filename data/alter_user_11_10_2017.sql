ALTER TABLE `user`
	ALTER `birthdate` DROP DEFAULT,
	ALTER `citizen` DROP DEFAULT,
	ALTER `registration` DROP DEFAULT,
	ALTER `passport` DROP DEFAULT,
	ALTER `passport_department` DROP DEFAULT;
ALTER TABLE `user`
	CHANGE COLUMN `birthdate` `birthdate` DATETIME NULL COMMENT '���� ��������' AFTER `access_token`,
	CHANGE COLUMN `citizen` `citizen` VARCHAR(50) NULL COMMENT '�����������' AFTER `birthdate`,
	CHANGE COLUMN `registration` `registration` VARCHAR(255) NULL COMMENT '����� �����������' AFTER `birth_area`,
	CHANGE COLUMN `passport` `passport` VARCHAR(30) NULL COMMENT '����� � ����� ��������' AFTER `residence`,
	CHANGE COLUMN `passport_date` `passport_date` TIMESTAMP NULL DEFAULT '0000-00-00 00:00:00' COMMENT '���� ������ ��������' AFTER `passport`,
	CHANGE COLUMN `passport_department` `passport_department` VARCHAR(255) NULL COMMENT '��� ����� �������' AFTER `passport_date`;
