--
-- PostgreSQL database dump
--

-- Dumped from database version 10.1
-- Dumped by pg_dump version 10.1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: intercom; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE intercom (
    id bigint NOT NULL,
    message character varying(255) DEFAULT ''::character varying NOT NULL,
    type character varying(7) DEFAULT 'info'::character varying NOT NULL,
    dt_start timestamp without time zone,
    dt_end timestamp without time zone,
    lang character varying(2) DEFAULT 'en'::character varying NOT NULL
);


ALTER TABLE intercom OWNER TO postgres;

--
-- Name: intercom_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE intercom_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE intercom_id_seq OWNER TO postgres;

--
-- Name: intercom_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE intercom_id_seq OWNED BY intercom.id;


--
-- Name: intercom id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY intercom ALTER COLUMN id SET DEFAULT nextval('intercom_id_seq'::regclass);


--
-- Name: intercom idx_16617_primary; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY intercom
    ADD CONSTRAINT idx_16617_primary PRIMARY KEY (id);


--
-- Name: idx_16617_idx_dt_lang; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_16617_idx_dt_lang ON intercom USING btree (dt_start, dt_end, lang);


--
-- Name: intercom; Type: ACL; Schema: public; Owner: postgres
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE intercom TO public;


--
-- Name: intercom_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT USAGE ON SEQUENCE intercom_id_seq TO public;


--
-- PostgreSQL database dump complete
--

