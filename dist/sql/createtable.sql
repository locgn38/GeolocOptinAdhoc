CREATE SEQUENCE utilisateurs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE utilisateurs (
    id integer DEFAULT nextval('utilisateurs_id_seq'::regclass) NOT NULL,
    code character varying(8) NOT NULL,
    nom character varying(64) NOT NULL,
    email character varying(64) NOT NULL,
    pwd character varying(16) NOT NULL,
    geom geometry DEFAULT st_geomfromtext('POINT(5.92585 45.188416)'::text, 4326),
    zoom integer DEFAULT 12 NOT NULL
);

CREATE SEQUENCE smsloc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE smsloc (
    id integer DEFAULT nextval('smsloc_id_seq'::regclass)NOT NULL,
    datetime timestamp without time zone DEFAULT now() NOT NULL,
    code character varying(8) NOT NULL,
    msg character varying(12) NOT NULL,
    lang character varying(12) NOT NULL,
    tel character varying(18) NOT NULL,
    val numeric(2,0) NOT NULL,
    statut character varying(12) DEFAULT 'init'::character varying NOT NULL,
    hash character varying(16)
);

CREATE SEQUENCE geoloc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE TABLE geoloc (
    id integer DEFAULT nextval('geoloc_id_seq'::regclass) NOT NULL,
    datetime timestamp without time zone DEFAULT now(),
    unite character varying,
    precision integer,
    useragent character varying,
    geom geometry,
    addr inet,
    orig numeric(6,0),
    tel character varying,
    com character varying,
    CONSTRAINT enforce_dims_geom CHECK ((st_ndims(geom) = 2)),
    CONSTRAINT enforce_geotype_geom CHECK (((geometrytype(geom) = 'POINT'::text) OR (geom IS NULL))),
    CONSTRAINT enforce_srid_geom CHECK ((st_srid(geom) = 4326))
);



CREATE VIEW pos_jour AS
    SELECT geoloc.id, geoloc.addr, geoloc.geom, geoloc.unite, geoloc.datetime, geoloc.precision, geoloc.useragent, geoloc.orig, geoloc.tel, geoloc.com FROM geoloc WHERE (((geoloc.datetime)::text)::date = ('now'::text)::date);
