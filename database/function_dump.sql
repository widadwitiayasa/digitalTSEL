DELIMITER $$

CREATE FUNCTION countActual(mytype VARCHAR(10), mytarget INT, date_post DATE, date_now DATE, target2 INT, output VARCHAR(10)) RETURNS DOUBLE
	DETERMINISTIC
	BEGIN
		DECLARE actual DOUBLE;
		IF output = 'L1' THEN
			IF mytype = 'cluster' THEN
				SELECT sum(REVENUE) INTO actual from revenue
					where id_cluster = mytarget and DATE >= date_post and DATE <= date_now;
			ELSEIF mytype = 'branch' THEN
				SELECT sum(REVENUE) INTO actual from revenue
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
					where id_branch = mytarget and DATE >= date_post and DATE <= date_now;
			ELSEIF mytype = 'regional' THEN
				SELECT sum(REVENUE) INTO actual from revenue
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
					LEFT JOIN branch on cluster.id_branch = branch.id 
					where id_regional = mytarget and DATE >= date_post and DATE <= date_now;
			ELSEIF mytype = 'area' THEN
				SELECT sum(REVENUE) INTO actual from revenue
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
					LEFT JOIN branch on cluster.id_branch = branch.id
					LEFT JOIN regional on branch.id_regional = regional.id
					where id_area = mytarget and DATE >= date_post and DATE <= date_now;
			END IF;
		ELSEIF (output = 'L3' OR output = 'TOP5') THEN
			IF mytype = 'cluster' THEN
				SELECT sum(REVENUE) INTO actual from revenue
					where id_cluster = mytarget and id_service = target2 and DATE >= date_post and DATE <= date_now;
			ELSEIF mytype = 'branch' THEN
				SELECT sum(REVENUE) INTO actual from revenue
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
					where id_branch = mytarget and id_service = target2 and DATE >= date_post and DATE <= date_now;
			ELSEIF mytype = 'regional' THEN
				SELECT sum(REVENUE) INTO actual from revenue
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
					LEFT JOIN branch on cluster.id_branch = branch.id 
					where id_regional = mytarget and id_service = target2 and DATE >= date_post and DATE <= date_now;
			ELSEIF mytype = 'area' THEN
				SELECT sum(REVENUE) INTO actual from revenue
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
					LEFT JOIN branch on cluster.id_branch = branch.id
					LEFT JOIN regional on branch.id_regional = regional.id
					where id_area = mytarget and id_service = target2 and DATE >= date_post and DATE <= date_now;
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
				SELECT sum(r.REVENUE) INTO mom1 from revenue r
				where id_cluster = mytarget and DATE >= date_post and DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r
				where id_cluster = mytarget and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			ELSEIF mytype = 'branch' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
				where ID_BRANCH = mytarget and r.DATE >= date_post and r.DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
				where ID_BRANCH = mytarget and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			ELSEIF mytype = 'regional' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
					LEFT JOIN branch on cluster.id_branch = branch.id
				where ID_REGIONAL = mytarget and r.DATE >= date_post and r.DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
					LEFT JOIN branch on cluster.id_branch = branch.id
				where ID_REGIONAL = mytarget and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			ELSEIF mytype = 'area' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r
					LEFT JOIN clu ster on ID_CLUSTER = cluster.id
					LEFT JOIN branch on cluster.id_branch = branch.id
					LEFT JOIN regional on branch.id_regional = regional.id
				where ID_AREA = mytarget and r.DATE >= date_post and r.DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
					LEFT JOIN branch on cluster.id_branch = branch.id
					LEFT JOIN regional on branch.id_regional = regional.id
				where ID_AREA = mytarget and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			END IF;
		ELSEIF (output = 'L3' OR output = 'TOP5') THEN
			IF mytype = 'cluster' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r
				where id_cluster = mytarget and id_service = target2 and DATE >= date_post and DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r
				where id_cluster = mytarget and id_service = target2 and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			ELSEIF mytype = 'branch' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
				where ID_BRANCH = mytarget and id_service = target2 and r.DATE >= date_post and r.DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
				where ID_BRANCH = mytarget and id_service = target2 and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			ELSEIF mytype = 'regional' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
					LEFT JOIN branch on cluster.id_branch = branch.id
				where ID_REGIONAL = mytarget and id_service = target2 and r.DATE >= date_post and r.DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
					LEFT JOIN branch on cluster.id_branch = branch.id
				where ID_REGIONAL = mytarget and id_service = target2 and r.DATE >= date_mom1 and r.DATE <= date_mom2;
			ELSEIF mytype = 'area' THEN
				SELECT sum(r.REVENUE) INTO mom1 from revenue r
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
					LEFT JOIN branch on cluster.id_branch = branch.id
					LEFT JOIN regional on branch.id_regional = regional.id
				where ID_AREA = mytarget and id_service = target2 and r.DATE >= date_post and r.DATE <= date_now;
				SELECT sum(r.REVENUE) INTO mom2 from revenue r
					LEFT JOIN cluster on ID_CLUSTER = cluster.id
					LEFT JOIN branch on cluster.id_branch = branch.id
					LEFT JOIN regional on branch.id_regional = regional.id
				where ID_AREA = mytarget and id_service = target2 and r.DATE >= date_mom1 and r.DATE <= date_mom2;
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
			SELECT sum(r.REVENUE) INTO yoy1 from revenue r
			where id_service = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			SELECT sum(r.REVENUE) INTO yoy2 from revenue r
			where id_service = mytarget and r.DATE >= date_post2 and r.DATE <= date_now2;
		ELSEIF mytype = 'cluster' THEN
			SELECT sum(r.REVENUE) INTO yoy1 from revenue r
			where id_cluster = mytarget and DATE >= date_post and DATE <= date_now;
			SELECT sum(r.REVENUE) INTO yoy2 from revenue r
			where id_cluster = mytarget and r.DATE >= date_post2 and r.DATE <= date_now2;
		ELSEIF mytype = 'branch' THEN
			SELECT sum(r.REVENUE) INTO yoy1 from revenue r
				LEFT JOIN cluster on ID_CLUSTER = cluster.id
			where ID_BRANCH = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			SELECT sum(r.REVENUE) INTO yoy2 from revenue r
				LEFT JOIN cluster on ID_CLUSTER = cluster.id
			where ID_BRANCH = mytarget and r.DATE >= date_post2 and r.DATE <= date_now2;
		ELSEIF mytype = 'regional' THEN
			SELECT sum(r.REVENUE) INTO yoy1 from revenue r
				LEFT JOIN cluster on ID_CLUSTER = cluster.id
				LEFT JOIN branch on cluster.id_branch = branch.id
			where ID_REGIONAL = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			SELECT sum(r.REVENUE) INTO yoy2 from revenue r
				LEFT JOIN cluster on ID_CLUSTER = cluster.id
				LEFT JOIN branch on cluster.id_branch = branch.id
			where ID_REGIONAL = mytarget and r.DATE >= date_post2 and r.DATE <= date_now2;
		ELSEIF mytype = 'area' THEN
			SELECT sum(r.REVENUE) INTO yoy1 from revenue r
				LEFT JOIN cluster on ID_CLUSTER = cluster.id
				LEFT JOIN branch on cluster.id_branch = branch.id
				LEFT JOIN regional on branch.id_regional = regional.id
			where ID_AREA = mytarget and r.DATE >= date_post and r.DATE <= date_now;
			SELECT sum(r.REVENUE) INTO yoy2 from revenue r
				LEFT JOIN cluster on ID_CLUSTER = cluster.id
				LEFT JOIN branch on cluster.id_branch = branch.id
				LEFT JOIN regional on branch.id_regional = regional.id
			where ID_AREA = mytarget and r.DATE >= date_post2 and r.DATE <= date_now2;
		END IF;
		IF yoy2 = 0 THEN
			SET result = 0;
		ELSEIF yoy2 != 0 THEN	
			SET result = ROUND(((yoy1/yoy2)-1)*100,2);
		END IF;
	RETURN (result);
END

DELIMITER $$

CREATE FUNCTION countYtd(mytype VARCHAR(10), mytarget INT, date_ytd DATE, date_now DATE, date_ytd2 DATE, date_now2 DATE) RETURNS DOUBLE
	DETERMINISTIC
	BEGIN
		DECLARE ytd1 DOUBLE;
		DECLARE ytd2 DOUBLE;
		DECLARE result DOUBLE;

		IF mytype = 'service' THEN
			SELECT sum(r.REVENUE) INTO ytd1 from revenue r
			where id_service = mytarget and r.DATE >= date_ytd and r.DATE <= date_now;
			SELECT sum(r.REVENUE) INTO ytd2 from revenue r
			where id_service = mytarget and r.DATE >= date_ytd2 and r.DATE <= date_now2;
		ELSEIF mytype = 'cluster' THEN
			SELECT sum(r.REVENUE) INTO ytd1 from revenue r
			where id_cluster = mytarget and DATE >= date_ytd and DATE <= date_now;
			SELECT sum(r.REVENUE) INTO ytd2 from revenue r
			where id_cluster = mytarget and r.DATE >= date_ytd2 and r.DATE <= date_now2;
		ELSEIF mytype = 'branch' THEN
			SELECT sum(r.REVENUE) INTO ytd1 from revenue r
				LEFT JOIN cluster on ID_CLUSTER = cluster.id
			where ID_BRANCH = mytarget and r.DATE >= date_ytd and r.DATE <= date_now;
			SELECT sum(r.REVENUE) INTO ytd2 from revenue r
				LEFT JOIN cluster on ID_CLUSTER = cluster.id
			where ID_BRANCH = mytarget and r.DATE >= date_ytd2 and r.DATE <= date_now2;
		ELSEIF mytype = 'regional' THEN
			SELECT sum(r.REVENUE) INTO ytd1 from revenue r
				LEFT JOIN cluster on ID_CLUSTER = cluster.id
				LEFT JOIN branch on cluster.id_branch = branch.id
			where ID_REGIONAL = mytarget and r.DATE >= date_ytd and r.DATE <= date_now;
			SELECT sum(r.REVENUE) INTO ytd2 from revenue r
				LEFT JOIN cluster on ID_CLUSTER = cluster.id
				LEFT JOIN branch on cluster.id_branch = branch.id
			where ID_REGIONAL = mytarget and r.DATE >= date_ytd2 and r.DATE <= date_now2;
		ELSEIF mytype = 'area' THEN
			SELECT sum(r.REVENUE) INTO ytd1 from revenue r
				LEFT JOIN cluster on ID_CLUSTER = cluster.id
				LEFT JOIN branch on cluster.id_branch = branch.id
				LEFT JOIN regional on branch.id_regional = regional.id
			where ID_AREA = mytarget and r.DATE >= date_ytd and r.DATE <= date_now;
			SELECT sum(r.REVENUE) INTO ytd2 from revenue r
				LEFT JOIN cluster on ID_CLUSTER = cluster.id
				LEFT JOIN branch on cluster.id_branch = branch.id
				LEFT JOIN regional on branch.id_regional = regional.id
			where ID_AREA = mytarget and r.DATE >= date_ytd2 and r.DATE <= date_now2;
		END IF;
		IF ytd2 = 0 THEN
			SET result = 0;
		ELSEIF ytd2 != 0 THEN	
			SET result = ROUND(((ytd1/ytd2)-1)*100,2);
		END IF;
	RETURN (result);
END