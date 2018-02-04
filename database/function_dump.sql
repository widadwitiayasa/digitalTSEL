DELIMITER $$

CREATE FUNCTION countActual(mytype VARCHAR(10), mytarget INT, date_post DATE, date_now DATE, target2 INT, output VARCHAR(10)) RETURNS DOUBLE
	DETERMINISTIC
	BEGIN
		DECLARE actual DOUBLE;
		IF output = 'L1' THEN
			IF mytype = 'cluster' THEN
				SELECT sum(r.REVENUE) INTO actual from revenue r, cluster c where r.id_cluster = c.id
				and c.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			ELSEIF mytype = 'branch' THEN
				SELECT sum(r.REVENUE) INTO actual from revenue r, cluster c, branch b where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and b.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			ELSEIF mytype = 'regional' THEN
				SELECT sum(r.REVENUE) INTO actual from revenue r, cluster c, branch b, regional re where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and b.ID_REGIONAL = re.id and re.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			ELSEIF mytype = 'area' THEN
				SELECT sum(r.REVENUE) INTO actual from revenue r, cluster c, branch b, regional re, area a where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and b.ID_REGIONAL = re.id and re.ID_AREA = a.id and a.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			END IF;
		ELSEIF (output = 'L3' OR output = 'TOP5') THEN
			IF mytype = 'cluster' THEN
				SELECT sum(r.REVENUE) INTO actual from revenue r, cluster c where r.id_cluster = c.id
				and c.id = mytarget and r.ID_SERVICE = target2 and r.DATE >= date_post and r.DATE <= date_now;
			ELSEIF mytype = 'branch' THEN
				SELECT sum(r.REVENUE) INTO actual from revenue r, cluster c, branch b where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and r.ID_SERVICE = target2 and b.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			ELSEIF mytype = 'regional' THEN
				SELECT sum(r.REVENUE) INTO actual from revenue r, cluster c, branch b, regional re where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and r.ID_SERVICE = target2 and b.ID_REGIONAL = re.id and re.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			ELSEIF mytype = 'area' THEN
				SELECT sum(r.REVENUE) INTO actual from revenue r, cluster c, branch b, regional re, area a where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and r.ID_SERVICE = target2 and b.ID_REGIONAL = re.id and re.ID_AREA = a.id and a.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			END IF;
		END IF;
	RETURN (actual);
END

DELIMITER $$

CREATE FUNCTION countMom(mytype VARCHAR(10), mytarget INT, date_post DATE, date_now DATE, date_mom1 DATE, date_mom2 DATE, target2 INT, output VARCHAR(10)) RETURNS DOUBLE
	DETERMINISTIC
	BEGIN
		DECLARE mom1 DOUBLE;
		DECLARE mom2 DOUBLE;
		DECLARE result DOUBLE;

		IF output = 'L1' THEN
			IF mytype = 'cluster' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r, cluster c where r.id_cluster = c.id
				and c.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r, cluster c where r.id_cluster = c.id
				and c.id = mytarget and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			ELSEIF mytype = 'branch' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r, cluster c, branch b where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and b.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r, cluster c, branch b where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and b.id = mytarget and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			ELSEIF mytype = 'regional' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r, cluster c, branch b, regional re where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and b.ID_REGIONAL = re.id and re.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r, cluster c, branch b, regional re where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and b.ID_REGIONAL = re.id and re.id = mytarget and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			ELSEIF mytype = 'area' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r, cluster c, branch b, regional re, area a where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and b.ID_REGIONAL = re.id and re.ID_AREA = a.id and a.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
								SELECT sum(r.REVENUE) INTO mom2 from revenue r, cluster c, branch b, regional re, area a where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and b.ID_REGIONAL = re.id and re.ID_AREA = a.id and a.id = mytarget and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			END IF;
		ELSEIF (output = 'L3' OR output = 'TOP5') THEN
			IF mytype = 'cluster' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r, cluster c where r.id_cluster = c.id
				and c.id = mytarget and r.ID_SERVICE = target2 and r.DATE >= date_post and r.DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r, cluster c where r.id_cluster = c.id
				and c.id = mytarget and r.ID_SERVICE = target2 and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			ELSEIF mytype = 'branch' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r, cluster c, branch b where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and r.ID_SERVICE = target2 and b.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r, cluster c, branch b where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and r.ID_SERVICE = target2 and b.id = mytarget and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			ELSEIF mytype = 'regional' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r, cluster c, branch b, regional re where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and r.ID_SERVICE = target2 and b.ID_REGIONAL = re.id and re.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r, cluster c, branch b, regional re where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and r.ID_SERVICE = target2 and b.ID_REGIONAL = re.id and re.id = mytarget and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			ELSEIF mytype = 'area' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r, cluster c, branch b, regional re, area a where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and r.ID_SERVICE = target2 and b.ID_REGIONAL = re.id and re.ID_AREA = a.id and a.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r, cluster c, branch b, regional re, area a where r.id_cluster = c.id
				and c.ID_BRANCH = b.id and r.ID_SERVICE = target2 and b.ID_REGIONAL = re.id and re.ID_AREA = a.id and a.id = mytarget and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			END IF;
		END IF;
		IF mom2 = 0 THEN
			SET result = 0;
		ELSEIF mom2 != 0 THEN	
			SET result = ROUND(((mom1/mom2)-1)*100,2);
		END IF;
	RETURN (result);
END

DELIMITER $$

CREATE FUNCTION countYoy(mytype VARCHAR(10), mytarget INT, date_post DATE, date_now DATE, date_post2 DATE, date_now2 DATE) RETURNS DOUBLE
	DETERMINISTIC
	BEGIN
		DECLARE yoy1 DOUBLE;
		DECLARE yoy2 DOUBLE;
		DECLARE result DOUBLE;

		IF mytype = 'service' THEN
			SELECT sum(r.REVENUE) INTO yoy1 from revenue r, cluster c where r.id_cluster = c.id
			and r.id_service = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			SELECT sum(r.REVENUE) INTO yoy2 from revenue r, cluster c where r.id_cluster = c.id
			and r.id_service = mytarget and r.DATE >= date_post2 and r.DATE <= date_now2;
		ELSEIF mytype = 'cluster' THEN
			SELECT sum(r.REVENUE) INTO yoy1 from revenue r, cluster c where r.id_cluster = c.id
			and c.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			SELECT sum(r.REVENUE) INTO yoy2 from revenue r, cluster c where r.id_cluster = c.id
			and c.id = mytarget and r.DATE >= date_post2 and r.DATE <= date_now2;
		ELSEIF mytype = 'branch' THEN
			SELECT sum(r.REVENUE) INTO yoy1 from revenue r, cluster c, branch b where r.id_cluster = c.id
			and c.ID_BRANCH = b.id and b.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			SELECT sum(r.REVENUE) INTO yoy2 from revenue r, cluster c, branch b where r.id_cluster = c.id
			and c.ID_BRANCH = b.id and b.id = mytarget and r.DATE >= date_post2 and r.DATE <= date_now2;
		ELSEIF mytype = 'regional' THEN
			SELECT sum(r.REVENUE) INTO yoy1 from revenue r, cluster c, branch b, regional re where r.id_cluster = c.id
			and c.ID_BRANCH = b.id and b.ID_REGIONAL = re.id and re.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			SELECT sum(r.REVENUE) INTO yoy2 from revenue r, cluster c, branch b, regional re where r.id_cluster = c.id
			and c.ID_BRANCH = b.id and b.ID_REGIONAL = re.id and re.id = mytarget and r.DATE >= date_post2 and r.DATE <= date_now2;
		ELSEIF mytype = 'area' THEN
			SELECT sum(r.REVENUE) INTO yoy1 from revenue r, cluster c, branch b, regional re, area a where r.id_cluster = c.id
			and c.ID_BRANCH = b.id and b.ID_REGIONAL = re.id and re.ID_AREA = a.id and a.id = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			SELECT sum(r.REVENUE) INTO yoy2 from revenue r, cluster c, branch b, regional re, area a where r.id_cluster = c.id
			and c.ID_BRANCH = b.id and b.ID_REGIONAL = re.id and re.ID_AREA = a.id and a.id = mytarget and r.DATE >= date_post2 and r.DATE <= date_now2;
		END IF;
		IF yoy2 = 0 THEN
			SET result = 0;
		ELSEIF yoy2 != 0 THEN	
			SET result = ROUND(((yoy1/yoy2)-1)*100,2);
		END IF;
	RETURN (result);
END