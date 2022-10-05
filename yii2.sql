--
-- PostgreSQL database dump
--

-- Dumped from database version 13.3
-- Dumped by pg_dump version 13.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: auth_assignment; Type: TABLE; Schema: public; Owner: yii2
--

CREATE TABLE public.auth_assignment (
    item_name character varying(64) NOT NULL,
    user_id integer NOT NULL,
    created_at integer
);


ALTER TABLE public.auth_assignment OWNER TO yii2;

--
-- Name: auth_item; Type: TABLE; Schema: public; Owner: yii2
--

CREATE TABLE public.auth_item (
    name character varying(64) NOT NULL,
    type smallint NOT NULL,
    description text,
    rule_name character varying(64),
    data bytea,
    created_at integer,
    updated_at integer
);


ALTER TABLE public.auth_item OWNER TO yii2;

--
-- Name: auth_item_child; Type: TABLE; Schema: public; Owner: yii2
--

CREATE TABLE public.auth_item_child (
    parent character varying(64) NOT NULL,
    child character varying(64) NOT NULL
);


ALTER TABLE public.auth_item_child OWNER TO yii2;

--
-- Name: auth_rule; Type: TABLE; Schema: public; Owner: yii2
--

CREATE TABLE public.auth_rule (
    name character varying(64) NOT NULL,
    data bytea,
    created_at integer,
    updated_at integer
);


ALTER TABLE public.auth_rule OWNER TO yii2;

--
-- Name: migration; Type: TABLE; Schema: public; Owner: yii2
--

CREATE TABLE public.migration (
    version character varying(180) NOT NULL,
    apply_time integer
);


ALTER TABLE public.migration OWNER TO yii2;

--
-- Data for Name: auth_assignment; Type: TABLE DATA; Schema: public; Owner: yii2
--

COPY public.auth_assignment (item_name, user_id, created_at) FROM stdin;
superadmin	1	1664904966
\.


--
-- Data for Name: auth_item; Type: TABLE DATA; Schema: public; Owner: yii2
--

COPY public.auth_item (name, type, description, rule_name, data, created_at, updated_at) FROM stdin;
clear_cache	2	Очистка кеша	\N	\N	1595495016	1595495016
change_user	2	Изменение данных пользователей	\N	\N	1590644951	1660910775
superadmin	1	Суперадмин	\N	\N	1590644951	1661191916
user	1	Пользователь	\N	\N	1590645254	1661193890
dashboard	2	Админ панель	\N	\N	1590644951	1661432451
admin	1	Админ	\N	\N	1592929324	1664899679
moderator	1	Модератор	\N	\N	1590645297	1664899716
\.


--
-- Data for Name: auth_item_child; Type: TABLE DATA; Schema: public; Owner: yii2
--

COPY public.auth_item_child (parent, child) FROM stdin;
admin	clear_cache
admin	change_user
admin	dashboard
moderator	dashboard
\.


--
-- Data for Name: auth_rule; Type: TABLE DATA; Schema: public; Owner: yii2
--

COPY public.auth_rule (name, data, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: migration; Type: TABLE DATA; Schema: public; Owner: yii2
--

COPY public.migration (version, apply_time) FROM stdin;
m000000_000000_base	1590395361
\.


--
-- Name: auth_assignment auth_assignment_pkey; Type: CONSTRAINT; Schema: public; Owner: yii2
--

ALTER TABLE ONLY public.auth_assignment
    ADD CONSTRAINT auth_assignment_pkey PRIMARY KEY (item_name, user_id);


--
-- Name: auth_item_child auth_item_child_pkey; Type: CONSTRAINT; Schema: public; Owner: yii2
--

ALTER TABLE ONLY public.auth_item_child
    ADD CONSTRAINT auth_item_child_pkey PRIMARY KEY (parent, child);


--
-- Name: auth_item auth_item_pkey; Type: CONSTRAINT; Schema: public; Owner: yii2
--

ALTER TABLE ONLY public.auth_item
    ADD CONSTRAINT auth_item_pkey PRIMARY KEY (name);


--
-- Name: auth_rule auth_rule_pkey; Type: CONSTRAINT; Schema: public; Owner: yii2
--

ALTER TABLE ONLY public.auth_rule
    ADD CONSTRAINT auth_rule_pkey PRIMARY KEY (name);


--
-- Name: migration migration_pkey; Type: CONSTRAINT; Schema: public; Owner: yii2
--

ALTER TABLE ONLY public.migration
    ADD CONSTRAINT migration_pkey PRIMARY KEY (version);


--
-- Name: idx-auth_assignment-user_id; Type: INDEX; Schema: public; Owner: yii2
--

CREATE INDEX "idx-auth_assignment-user_id" ON public.auth_assignment USING btree (user_id);


--
-- Name: idx-auth_item-type; Type: INDEX; Schema: public; Owner: yii2
--

CREATE INDEX "idx-auth_item-type" ON public.auth_item USING btree (type);


--
-- Name: auth_assignment auth_assignment_item_name_fkey; Type: FK CONSTRAINT; Schema: public; Owner: yii2
--

ALTER TABLE ONLY public.auth_assignment
    ADD CONSTRAINT auth_assignment_item_name_fkey FOREIGN KEY (item_name) REFERENCES public.auth_item(name) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: auth_item_child auth_item_child_child_fkey; Type: FK CONSTRAINT; Schema: public; Owner: yii2
--

ALTER TABLE ONLY public.auth_item_child
    ADD CONSTRAINT auth_item_child_child_fkey FOREIGN KEY (child) REFERENCES public.auth_item(name) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: auth_item_child auth_item_child_parent_fkey; Type: FK CONSTRAINT; Schema: public; Owner: yii2
--

ALTER TABLE ONLY public.auth_item_child
    ADD CONSTRAINT auth_item_child_parent_fkey FOREIGN KEY (parent) REFERENCES public.auth_item(name) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: auth_item auth_item_rule_name_fkey; Type: FK CONSTRAINT; Schema: public; Owner: yii2
--

ALTER TABLE ONLY public.auth_item
    ADD CONSTRAINT auth_item_rule_name_fkey FOREIGN KEY (rule_name) REFERENCES public.auth_rule(name) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

