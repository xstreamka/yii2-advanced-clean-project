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
-- Name: user; Type: TABLE; Schema: public; Owner: yii2
--

CREATE TABLE public."user" (
    id integer NOT NULL,
    username character varying(255) NOT NULL,
    auth_key character varying(32) NOT NULL,
    password_hash character varying(255) NOT NULL,
    password_reset_token character varying(255),
    email character varying(255) NOT NULL,
    status smallint DEFAULT 10 NOT NULL,
    created_at character varying(255) NOT NULL,
    updated_at character varying(255) DEFAULT NULL::character varying,
    verification_token character varying(255) DEFAULT NULL::character varying,
    last_name character varying(255)
);


ALTER TABLE public."user" OWNER TO yii2;

--
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: yii2
--

CREATE SEQUENCE public.user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_id_seq OWNER TO yii2;

--
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: yii2
--

ALTER SEQUENCE public.user_id_seq OWNED BY public."user".id;


--
-- Name: user id; Type: DEFAULT; Schema: public; Owner: yii2
--

ALTER TABLE ONLY public."user" ALTER COLUMN id SET DEFAULT nextval('public.user_id_seq'::regclass);


--
-- Data for Name: auth_assignment; Type: TABLE DATA; Schema: public; Owner: yii2
--

COPY public.auth_assignment (item_name, user_id, created_at) FROM stdin;
superadmin	1	1639402766
\.


--
-- Data for Name: auth_item; Type: TABLE DATA; Schema: public; Owner: yii2
--

COPY public.auth_item (name, type, description, rule_name, data, created_at, updated_at) FROM stdin;
dashboard	2	Админ панель	\N	\N	1590644951	1590644951
change_user	2	Изменение данных пользователей	\N	\N	1590644951	1590644951
moderator	1	Модератор	\N	\N	1590645297	1592931010
alert	2	Сервисные сообщения в шапке сайта (frontend)	\N	\N	1593702962	1594196282
clear_cache	2	Очистка кеша	\N	\N	1595495016	1595495016
log	2	Смотреть логи	\N	\N	1595927616	1595927616
user	1	Пользователь	\N	\N	1590645254	1602237187
yii_debug	2	Yii debug	\N	\N	1607671061	1607671061
admin	1	Админ	\N	\N	1592929324	1607671177
superadmin	1	Суперадмин	\N	\N	1590644951	1607671186
\.


--
-- Data for Name: auth_item_child; Type: TABLE DATA; Schema: public; Owner: yii2
--

COPY public.auth_item_child (parent, child) FROM stdin;
admin	dashboard
admin	change_user
admin	alert
admin	clear_cache
admin	log
admin	yii_debug
superadmin	dashboard
superadmin	change_user
superadmin	alert
superadmin	clear_cache
superadmin	log
superadmin	yii_debug
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
m130524_201442_init	1590395365
m190124_110200_add_verification_token_column_to_user_table	1590395365
\.


--
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: yii2
--

COPY public."user" (id, username, auth_key, password_hash, password_reset_token, email, status, created_at, updated_at, verification_token, last_name) FROM stdin;
1	Админ	AxZLbtNQ3sN5J0ZxW0OtXVXUZNcHbkaX	$2y$13$Vm9r4lu/8mhA451zy6nlNeX.QT.oSIy76iY8nj6QoJQkEWJNMT.nS	\N	admin@yiiframework.com	10	1970-01-01 00:00:00	\N	XRj0Bs2jyi-WNT4lkonaRHAc1LZ0ZRac_1596709776	Админов
\.


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: yii2
--

SELECT pg_catalog.setval('public.user_id_seq', 1, true);


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
-- Name: user user_email_key; Type: CONSTRAINT; Schema: public; Owner: yii2
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_email_key UNIQUE (email);


--
-- Name: user user_password_reset_token_key; Type: CONSTRAINT; Schema: public; Owner: yii2
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_password_reset_token_key UNIQUE (password_reset_token);


--
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: yii2
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


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

