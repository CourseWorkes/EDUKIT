use `iep`;

DROP PROCEDURE IF EXISTS addScheduleEntry;
DROP PROCEDURE IF EXISTS getScheduleGroup;
DROP PROCEDURE IF EXISTS getAllScheduleGroup;
DROP PROCEDURE IF EXISTS changePair;

DELIMITER //

CREATE PROCEDURE IF NOT EXISTS addScheduleEntry(grp int, d int, pair int, subject int)
BEGIN
  INSERT INTO `schedule` (`id_grp`, `day`, `pair`, `subject`) VALUES (grp, d, pair, subject);
END;

CREATE PROCEDURE IF NOT EXISTS getScheduleGroup(grp int)
BEGIN
  SELECT s.day, 
		 g.description as 'group', 
         s.pair, 
         sb.description as 'subject'
  FROM `schedule` s
	INNER JOIN `groups` g ON s.id_grp=g.grp
	INNER JOIN `subjects` sb ON s.subject=sb.id_subject
  WHERE s.id_grp=grp
  ORDER BY s.pair;
END;

CREATE PROCEDURE IF NOT EXISTS getAllScheduleGroup()
BEGIN
  SELECT s.day, 
		 g.description as 'group',
         s.pair, 
         sb.description as 'subject'
  FROM `schedule` s
	INNER JOIN `groups` g ON s.id_grp=g.grp
	INNER JOIN `subjects` sb ON s.subject=sb.id_subject
  ORDER BY s.day, s.pair;
END;

CREATE PROCEDURE IF NOT EXISTS changePair(grp char(10), d int, pair int, subj int)
BEGIN
	UPDATE `schedule` s
    SET s.subject=subj
    WHERE s.id_grp=getGroupId(grp) AND s.pair=pair AND s.day=day;
END;

//

DELIMITER ;