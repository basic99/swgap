CREATE OR REPLACE FUNCTION steward() returns void as $$
DECLARE
    i record;
    j record;
    k record;
    num integer;
    num_recs integer;
    loop_cnt integer;
    my_geom geometry;
    my_area numeric(16,3);
BEGIN
   FOR i IN  select distinct  owner_c from sw_manage_temp LOOP
     FOR k IN  select distinct state_fips from sw_manage_temp LOOP
      select into num count(*) from sw_manage_temp where owner_c = i.owner_c and state_fips = k.state_fips;  
         my_geom := NULL;
         RAISE NOTICE 'gapown code is %,  and state is % and count is % ', i.owner_c, k.state_fips, num;
         num_recs := 0;
         loop_cnt := 0;
          FOR j IN SELECT wkb_geometry, ogc_fid FROM sw_manage_temp WHERE owner_c = i.owner_c and state_fips = k.state_fips LOOP
            
            if my_geom IS NULL
               THEN
               my_geom := j.wkb_geometry;
            END IF;
            SELECT INTO my_geom multi((geomunion(my_geom, j.wkb_geometry)));
            num_recs := num_recs + 1;
            RAISE NOTICE 'table sw_owner row updated, ogc_fid is % mun records %', j.ogc_fid, num_recs;    
          END LOOP;
         insert into sw_owner(owner_code, state_fips, wkb_geometry) values(i.owner_c,  k.state_fips, my_geom);
         --select into my_area area(my_geom);
         --RAISE NOTICE 'table sw_owner updated, area is %', my_area;
      
     END LOOP; 
   END LOOP;
   Return;
END;
$$ LANGUAGE plpgsql;
