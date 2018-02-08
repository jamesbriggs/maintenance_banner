-- Table: public.intercom

-- DROP TABLE public.intercom;

CREATE TABLE public.intercom
(
  id integer NOT NULL DEFAULT nextval('intercom_id_seq'::regclass),
  dt_start timestamp without time zone,
  dt_end timestamp without time zone,
  message character(255) NOT NULL,
  type character(10) NOT NULL,
  lang character(2) NOT NULL DEFAULT 'en'::bpchar,
  CONSTRAINT intercom_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.intercom
  OWNER TO postgres;
