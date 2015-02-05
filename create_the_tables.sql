--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: log_upgrade_level(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION log_upgrade_level() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
	insert into level_update_log 
	(user_id, new_level, original_level, new_profit, 
	original_profit, new_first_purchase, original_first_purchase,
	original_basic_level, new_basic_level, original_turnover, new_turnover)
	values
	(NEW.id, NEW.level, OLD.level, NEW.profit,
	 OLD.profit, NEW.first_purchase, OLD.first_purchase,
	 OLD.basic_level, NEW.basic_level, OLD.turnover, NEW.turnover);
	RETURN NEW;
END;
$$;


ALTER FUNCTION public.log_upgrade_level() OWNER TO postgres;

--
-- Name: update_left_right(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION update_left_right(pic integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
    declare update_node int;
    declare current_root_id int;
    BEGIN
	select
		rgt, root_id
		into update_node, current_root_id
	from users where id =
		(select pid from users where id = pid)
	;
	update users set lft = case when lft >= update_node then lft + 2
                                      else lft end,
                         rgt = rgt + 2
		where rgt >= update_node - 1;
    END;
    $$;


ALTER FUNCTION public.update_left_right(pic integer) OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: address_books; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE address_books (
    id integer NOT NULL,
    user_id integer,
    contact character varying(10),
    province_id integer,
    city_id integer,
    address_info character varying,
    remark character varying,
    create_time timestamp without time zone DEFAULT now(),
    mobile character varying(20)
);


ALTER TABLE address_books OWNER TO postgres;

--
-- Name: address_books_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE address_books_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE address_books_id_seq OWNER TO postgres;

--
-- Name: address_books_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE address_books_id_seq OWNED BY address_books.id;


--
-- Name: amounts; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE amounts (
    id integer NOT NULL,
    amount money,
    order_id integer,
    level integer
);


ALTER TABLE amounts OWNER TO postgres;

--
-- Name: amounts_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE amounts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE amounts_id_seq OWNER TO postgres;

--
-- Name: amounts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE amounts_id_seq OWNED BY amounts.id;


--
-- Name: bills; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE bills (
    id integer NOT NULL,
    user_id integer,
    order_id integer,
    sub_user_id integer,
    volume money,
    type integer,
    reason integer,
    create_time timestamp without time zone DEFAULT now(),
    pay_amt_without_post_fee money DEFAULT 0
);


ALTER TABLE bills OWNER TO postgres;

--
-- Name: bills_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE bills_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE bills_id_seq OWNER TO postgres;

--
-- Name: bills_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE bills_id_seq OWNED BY bills.id;


--
-- Name: captcha; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE captcha (
    captcha_id integer NOT NULL,
    ip_address character varying(16) DEFAULT '0'::character varying NOT NULL,
    word character varying(20) NOT NULL,
    captcha_time integer
);


ALTER TABLE captcha OWNER TO postgres;

--
-- Name: captcha_captcha_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE captcha_captcha_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE captcha_captcha_id_seq OWNER TO postgres;

--
-- Name: captcha_captcha_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE captcha_captcha_id_seq OWNED BY captcha.captcha_id;


--
-- Name: finish_log; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE finish_log (
    order_id integer,
    pay_amt money,
    user_id integer,
    parent_user_id integer,
    is_root boolean,
    pay_amt_without_post_fee money,
    is_first boolean,
    id integer NOT NULL
);


ALTER TABLE finish_log OWNER TO postgres;

--
-- Name: finish_log_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE finish_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE finish_log_id_seq OWNER TO postgres;

--
-- Name: finish_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE finish_log_id_seq OWNED BY finish_log.id;


--
-- Name: forecasts; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE forecasts (
    id integer NOT NULL,
    content character varying,
    create_time timestamp without time zone DEFAULT now()
);


ALTER TABLE forecasts OWNER TO postgres;

--
-- Name: forecast_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE forecast_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE forecast_id_seq OWNER TO postgres;

--
-- Name: forecast_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE forecast_id_seq OWNED BY forecasts.id;


--
-- Name: level_update_log; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE level_update_log (
    id integer NOT NULL,
    user_id integer,
    new_level integer,
    upgrade_time timestamp without time zone DEFAULT now(),
    original_profit money,
    original_level integer,
    new_profit money,
    original_first_purchase money,
    new_first_purchase money,
    original_basic_level integer,
    new_basic_level integer,
    original_turnover money,
    new_turnover money
);


ALTER TABLE level_update_log OWNER TO postgres;

--
-- Name: level_update_log_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE level_update_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE level_update_log_id_seq OWNER TO postgres;

--
-- Name: level_update_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE level_update_log_id_seq OWNED BY level_update_log.id;


--
-- Name: orders; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE orders (
    id integer NOT NULL,
    user_id integer,
    create_time timestamp without time zone DEFAULT now(),
    update_time timestamp without time zone,
    product_id integer,
    is_pay boolean DEFAULT false,
    pay_amt money DEFAULT 0,
    is_correct boolean DEFAULT false,
    count integer,
    is_cancelled boolean DEFAULT false,
    is_deleted boolean DEFAULT false,
    level integer,
    parent_level integer,
    pay_time timestamp without time zone,
    address_book_id integer,
    is_post boolean,
    post_fee money,
    finish_time timestamp without time zone,
    is_pay_online boolean DEFAULT false,
    is_first boolean DEFAULT false,
    pay_amt_without_post_fee money
);


ALTER TABLE orders OWNER TO postgres;

--
-- Name: orders_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE orders_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE orders_id_seq OWNER TO postgres;

--
-- Name: orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE orders_id_seq OWNED BY orders.id;


--
-- Name: price; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE price (
    id integer NOT NULL,
    level integer,
    product_id integer,
    price money DEFAULT 0.00
);


ALTER TABLE price OWNER TO postgres;

--
-- Name: price_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE price_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE price_id_seq OWNER TO postgres;

--
-- Name: price_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE price_id_seq OWNED BY price.id;


--
-- Name: products; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE products (
    id integer NOT NULL,
    title character varying,
    properties character varying,
    feature character varying,
    usage_method character varying,
    ingredient character varying,
    img character varying,
    create_time timestamp without time zone DEFAULT now(),
    sale_time timestamp without time zone,
    off_sale_time timestamp without time zone,
    is_valid boolean
);


ALTER TABLE products OWNER TO postgres;

--
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE products_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE products_id_seq OWNER TO postgres;

--
-- Name: products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE products_id_seq OWNED BY products.id;


--
-- Name: root_ids; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE root_ids (
    create_time timestamp without time zone,
    id integer NOT NULL
);


ALTER TABLE root_ids OWNER TO postgres;

--
-- Name: root_ids_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE root_ids_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE root_ids_id_seq OWNER TO postgres;

--
-- Name: root_ids_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE root_ids_id_seq OWNED BY root_ids.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE users (
    username character varying(16),
    password character(32),
    create_time timestamp without time zone DEFAULT now(),
    update_time timestamp without time zone,
    is_valid boolean,
    level integer DEFAULT 0,
    name character varying(10),
    citizen_id character varying(20),
    mobile_no character varying(15),
    wechat_id character varying(30),
    qq_no character varying(20),
    property money,
    lft integer NOT NULL,
    rgt integer NOT NULL,
    pid integer,
    root_id integer,
    profit money DEFAULT 0.00,
    is_admin boolean DEFAULT false,
    id integer NOT NULL,
    is_root boolean DEFAULT false,
    first_purchase money DEFAULT 0,
    basic_level integer DEFAULT 0,
    turnover money DEFAULT 0,
    CONSTRAINT users_check CHECK ((lft < rgt)),
    CONSTRAINT users_lft_check CHECK ((lft > 0)),
    CONSTRAINT users_rgt_check CHECK ((rgt > 1))
);


ALTER TABLE users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: zents_bills; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE zents_bills (
    user_id integer,
    id integer NOT NULL,
    order_id integer,
    income_without_post_fee money,
    income_with_post_fee money,
    create_time timestamp without time zone DEFAULT now()
);


ALTER TABLE zents_bills OWNER TO postgres;

--
-- Name: zents_bills_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE zents_bills_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE zents_bills_id_seq OWNER TO postgres;

--
-- Name: zents_bills_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE zents_bills_id_seq OWNED BY zents_bills.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY address_books ALTER COLUMN id SET DEFAULT nextval('address_books_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY amounts ALTER COLUMN id SET DEFAULT nextval('amounts_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY bills ALTER COLUMN id SET DEFAULT nextval('bills_id_seq'::regclass);


--
-- Name: captcha_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY captcha ALTER COLUMN captcha_id SET DEFAULT nextval('captcha_captcha_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY finish_log ALTER COLUMN id SET DEFAULT nextval('finish_log_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY forecasts ALTER COLUMN id SET DEFAULT nextval('forecast_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY level_update_log ALTER COLUMN id SET DEFAULT nextval('level_update_log_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY orders ALTER COLUMN id SET DEFAULT nextval('orders_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY price ALTER COLUMN id SET DEFAULT nextval('price_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY products ALTER COLUMN id SET DEFAULT nextval('products_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY root_ids ALTER COLUMN id SET DEFAULT nextval('root_ids_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY zents_bills ALTER COLUMN id SET DEFAULT nextval('zents_bills_id_seq'::regclass);


--
-- Data for Name: address_books; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY address_books (id, user_id, contact, province_id, city_id, address_info, remark, create_time, mobile) FROM stdin;
14	2	王祖藍	1	2	花都区屌你老味	0	2015-02-01 03:42:48.338158	13234535466
24	8	王祖藍	1	2	花都区屌你老味	0	2015-02-02 16:17:19.181142	13322232322
25	8	sdf	1	2	sdf	0	2015-02-04 02:02:22.256967	sdf
26	8	王祖藍	1	2	花都区屌你老味	0	2015-02-04 03:32:27.94735	13234535466
\.


--
-- Name: address_books_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('address_books_id_seq', 26, true);


--
-- Data for Name: amounts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY amounts (id, amount, order_id, level) FROM stdin;
4	$3.10	5	1
5	$4.10	5	2
7	$5.00	5	0
6	$4.50	5	3
8	$400.00	7	1
9	$500.00	7	2
10	$600.00	7	3
11	$700.00	7	0
12	$3.10	8	1
13	$4.10	8	2
14	$4.50	8	3
15	$5.00	8	0
16	$30.00	9	1
17	$40.00	9	2
18	$50.00	9	3
19	$60.00	9	0
\.


--
-- Name: amounts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('amounts_id_seq', 19, true);


--
-- Data for Name: bills; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY bills (id, user_id, order_id, sub_user_id, volume, type, reason, create_time, pay_amt_without_post_fee) FROM stdin;
4	2	7	8	$10,000.00	2	2	2015-02-02 17:02:23.636264	$0.00
3	2	7	8	$70,000.00	1	1	2015-02-02 17:02:23.636264	$70,000.00
5	8	9	\N	$60,000.00	1	1	2015-02-04 03:33:04.824669	$60,000.00
6	2	9	8	$10,000.00	2	2	2015-02-04 03:33:04.824669	$0.00
\.


--
-- Name: bills_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('bills_id_seq', 6, true);


--
-- Data for Name: captcha; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY captcha (captcha_id, ip_address, word, captcha_time) FROM stdin;
71	127.0.0.1	QEg6U	1423005012
72	127.0.0.1	BjG0r	1423006606
73	127.0.0.1	x2Ghd	1423006613
74	127.0.0.1	KPS6f	1423007531
75	127.0.0.1	X9Zpm	1423007538
76	127.0.0.1	h2H0b	1423007635
77	127.0.0.1	A9RDV	1423007796
78	127.0.0.1	dZMv4	1423007801
\.


--
-- Name: captcha_captcha_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('captcha_captcha_id_seq', 78, true);


--
-- Data for Name: finish_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY finish_log (order_id, pay_amt, user_id, parent_user_id, is_root, pay_amt_without_post_fee, is_first, id) FROM stdin;
5	$0.00	2	1	t	$46.50	\N	1
5	$46.50	2	1	t	$46.50	\N	2
7	$70,000.00	8	2	f	$70,000.00	t	3
9	$60,000.00	8	2	f	$60,000.00	f	4
\.


--
-- Name: finish_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('finish_log_id_seq', 4, true);


--
-- Name: forecast_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('forecast_id_seq', 1, false);


--
-- Data for Name: forecasts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY forecasts (id, content, create_time) FROM stdin;
1	hsdkjhwkejtlwjeltkjwlket	2015-01-27 23:46:04.924766
\.


--
-- Data for Name: level_update_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY level_update_log (id, user_id, new_level, upgrade_time, original_profit, original_level, new_profit, original_first_purchase, new_first_purchase, original_basic_level, new_basic_level, original_turnover, new_turnover) FROM stdin;
1	12	1	2015-02-02 03:57:49.188399	\N	0	\N	$0.00	$0.00	\N	\N	\N	\N
2	12	0	2015-02-02 11:50:35.251152	\N	1	\N	$0.00	$0.00	0	0	$0.00	$0.00
3	8	0	2015-02-02 15:46:28.91399	\N	0	\N	$0.00	$0.00	0	0	$0.00	$0.00
9	2	3	2015-02-02 16:29:23.98303	\N	0	\N	$0.00	$0.00	0	3	$0.00	$0.00
15	8	0	2015-02-02 16:29:59.21852	\N	0	\N	$0.00	$0.00	0	0	$0.00	$0.00
16	8	0	2015-02-02 16:29:59.21852	\N	0	\N	$0.00	$0.00	0	0	$0.00	$0.00
17	8	0	2015-02-02 16:29:59.21852	\N	0	\N	$0.00	$70,000.00	0	0	$0.00	$0.00
18	8	0	2015-02-02 16:29:59.21852	\N	0	\N	$70,000.00	$70,000.00	0	0	$0.00	$70,000.00
19	8	0	2015-02-02 16:29:59.21852	\N	0	\N	$70,000.00	$70,000.00	0	0	$70,000.00	$70,000.00
20	8	0	2015-02-02 16:29:59.21852	\N	0	\N	$70,000.00	$70,000.00	0	0	$70,000.00	$70,000.00
21	2	3	2015-02-02 16:29:59.21852	\N	3	\N	$0.00	$0.00	3	3	$0.00	$0.00
22	8	0	2015-02-02 16:37:17.435867	\N	0	\N	$70,000.00	$70,000.00	0	0	$70,000.00	$70,000.00
23	2	3	2015-02-02 16:44:30.326207	\N	3	\N	$0.00	$0.00	3	3	$0.00	$0.00
24	1	0	2015-02-02 16:47:40.506044	\N	0	$0.00	$0.00	$0.00	0	0	$0.00	$0.00
25	2	3	2015-02-02 16:47:41.574782	\N	3	$0.00	$0.00	$0.00	3	3	$0.00	$0.00
26	8	0	2015-02-02 16:47:42.621001	\N	0	$0.00	$70,000.00	$70,000.00	0	0	$70,000.00	$70,000.00
27	11	0	2015-02-02 16:47:45.229745	\N	0	$0.00	$0.00	$0.00	0	0	$0.00	$0.00
28	12	0	2015-02-02 16:49:02.933152	\N	0	$0.00	$0.00	$0.00	0	0	$0.00	$0.00
29	2	3	2015-02-04 03:03:40.207007	$0.00	3	$0.00	$0.00	$0.00	3	3	$0.00	$0.00
30	8	0	2015-02-04 03:03:40.207007	$0.00	0	$0.00	$70,000.00	$70,000.00	0	0	$70,000.00	$70,000.00
31	11	0	2015-02-04 03:03:40.207007	$0.00	0	$0.00	$0.00	$0.00	0	0	$0.00	$0.00
32	12	0	2015-02-04 03:03:40.207007	$0.00	0	$0.00	$0.00	$0.00	0	0	$0.00	$0.00
33	8	0	2015-02-04 03:33:04.824669	$0.00	0	$0.00	$70,000.00	$70,000.00	0	0	$70,000.00	$130,000.00
34	8	2	2015-02-04 03:33:04.824669	$0.00	0	$0.00	$70,000.00	$70,000.00	0	0	$130,000.00	$130,000.00
35	2	3	2015-02-04 03:33:04.824669	$0.00	3	$10,000.00	$0.00	$0.00	3	3	$0.00	$0.00
36	2	3	2015-02-04 03:33:04.824669	$10,000.00	3	$10,000.00	$0.00	$0.00	3	3	$0.00	$0.00
37	8	2	2015-02-04 03:39:16.14921	$0.00	2	$0.00	$70,000.00	$70,000.00	0	0	$130,000.00	$130,000.00
38	11	0	2015-02-04 03:39:20.042514	$0.00	0	$0.00	$0.00	$0.00	0	0	$0.00	$0.00
39	12	0	2015-02-04 03:39:22.274765	$0.00	0	$0.00	$0.00	$0.00	0	0	$0.00	$0.00
40	13	0	2015-02-04 03:39:25.431634	$0.00	0	$0.00	$0.00	$0.00	0	0	$0.00	$0.00
41	8	2	2015-02-04 07:23:24.061087	$0.00	2	$0.00	$70,000.00	$70,000.00	0	0	$130,000.00	$130,000.00
42	8	2	2015-02-04 07:23:30.199588	$0.00	2	$0.00	$70,000.00	$70,000.00	0	0	$130,000.00	$130,000.00
43	8	2	2015-02-04 07:24:29.096004	$0.00	2	$0.00	$70,000.00	$70,000.00	0	0	$130,000.00	$130,000.00
44	1	0	2015-02-04 07:53:52.653634	$0.00	0	$0.00	$0.00	$0.00	0	0	$0.00	$0.00
\.


--
-- Name: level_update_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('level_update_log_id_seq', 44, true);


--
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY orders (id, user_id, create_time, update_time, product_id, is_pay, pay_amt, is_correct, count, is_cancelled, is_deleted, level, parent_level, pay_time, address_book_id, is_post, post_fee, finish_time, is_pay_online, is_first, pay_amt_without_post_fee) FROM stdin;
5	2	2015-02-01 03:42:48.338158	2015-02-02 04:13:25	8	t	$46.50	t	15	f	f	1	1	\N	14	f	$0.00	2015-02-02 04:13:25	\N	\N	$46.50
7	8	2015-02-02 16:17:19.181142	2015-02-02 16:29:59	13	t	$70,000.00	t	100	f	f	0	3	\N	24	f	$0.00	2015-02-02 16:29:59	f	t	$70,000.00
8	8	2015-02-04 02:02:22.256967	\N	8	f	$0.00	f	1	f	f	0	3	\N	25	f	$0.00	\N	f	f	\N
9	8	2015-02-04 03:32:27.94735	2015-02-04 03:33:04	9	t	$60,000.00	t	1000	f	f	0	3	\N	26	f	$0.00	2015-02-04 03:33:04	f	f	$60,000.00
\.


--
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('orders_id_seq', 9, true);


--
-- Data for Name: price; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY price (id, level, product_id, price) FROM stdin;
16	1	8	$3.10
17	2	8	$4.10
19	1	9	$30.00
20	2	9	$40.00
21	3	9	$50.00
22	1	10	$1.00
23	2	10	$2.00
24	3	10	$3.00
25	1	11	$1.00
26	2	11	$2.00
27	3	11	$3.00
28	1	12	$1.00
29	2	12	$2.00
30	3	12	$3.00
31	0	8	$5.00
18	3	8	$4.50
32	0	9	$60.00
33	0	10	$4.00
34	0	11	$4.00
35	0	12	$4.00
36	0	13	$700.00
37	1	13	$400.00
38	2	13	$500.00
39	3	13	$600.00
\.


--
-- Name: price_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('price_id_seq', 39, true);


--
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY products (id, title, properties, feature, usage_method, ingredient, img, create_time, sale_time, off_sale_time, is_valid) FROM stdin;
8	How can I get the row number of results querying with DQL(Doctrine)					0	2015-01-29 02:32:23.746856	\N	\N	t
9	士大夫	士大夫	feature	method	ingredient	bg.jpg	2015-02-01 14:45:58.769059	\N	\N	t
10	士大夫	4564	54646	465456	465465	54cdda1a56575.jpg	2015-02-01 15:47:38.365638	\N	\N	t
11	玩兒					54cdda98d1891.jpg	2015-02-01 15:49:44.865262	\N	\N	t
12	士大夫sss					54cddb199194e.jpg	2015-02-01 15:51:53.626914	\N	\N	t
13	爛面面膜	1盒1億張	睇親爛面	潛移默化	空氣	54cf2aefe7585.jpg	2015-02-02 15:44:48.012194	\N	\N	f
\.


--
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('products_id_seq', 13, true);


--
-- Data for Name: root_ids; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY root_ids (create_time, id) FROM stdin;
2015-01-31 21:52:06	8
\.


--
-- Name: root_ids_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('root_ids_id_seq', 8, true);


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY users (username, password, create_time, update_time, is_valid, level, name, citizen_id, mobile_no, wechat_id, qq_no, property, lft, rgt, pid, root_id, profit, is_admin, id, is_root, first_purchase, basic_level, turnover) FROM stdin;
sdffwer	8e8a359d605a815dc118db3877c22b0e	2015-02-01 01:24:47.842887	\N	t	0	werwe	ewrkwhetkhk	hkjhwwtwety	hwtwket	34795834	\N	8	9	2	8	$0.00	f	12	f	$0.00	0	$0.00
abcde	e8dc4081b13434b45189a720b77b6818	2015-02-04 03:03:40.207007	\N	t	0	abcde	abcdeabcde	18722267182	abcde	abcde	\N	3	4	8	8	$0.00	f	13	f	$0.00	0	$0.00
subuser	21232f297a57a5a743894a0e4a801fc3	2015-02-01 00:20:04.60615	\N	t	2	家家酒	237498237958723	12381274892	尸杰222	23589235	\N	2	5	2	8	$0.00	f	8	f	$70,000.00	0	$130,000.00
zentssuperadmin	f6fdffe48c908deb0f4c3bd36c032e72	2015-01-27 23:00:46.60982	\N	t	0	管理员	8888888888	8888888	888888888	23008600	$88,888,888,888.00	1	2	\N	\N	$0.00	t	1	f	$0.00	0	$0.00
testuser	05a671c66aefea124cc08b76ea6d30bb	2015-01-31 21:52:06.22617	\N	t	3	家家酒	4401010101010101	12381274891	尸杰尸杰	87495	\N	1	10	1	8	$10,000.00	f	2	t	$0.00	3	$0.00
subuser2	e8408cb7570728580e2cb66f1a4b1ee4	2015-02-01 00:24:36.284857	\N	t	0	yweuir	jkljluioiwer	66672234223	33445333	73589273	\N	6	7	2	8	$0.00	f	11	f	$0.00	0	$0.00
\.


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('users_id_seq', 13, true);


--
-- Data for Name: zents_bills; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY zents_bills (user_id, id, order_id, income_without_post_fee, income_with_post_fee, create_time) FROM stdin;
2	3	7	$70,000.00	$70,000.00	2015-02-02 17:01:48.055231
2	4	9	$60,000.00	$60,000.00	2015-02-04 03:33:04.824669
\.


--
-- Name: zents_bills_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('zents_bills_id_seq', 4, true);


--
-- Name: amounts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY amounts
    ADD CONSTRAINT amounts_pkey PRIMARY KEY (id);


--
-- Name: bills_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY bills
    ADD CONSTRAINT bills_pkey PRIMARY KEY (id);


--
-- Name: captcha_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY captcha
    ADD CONSTRAINT captcha_id PRIMARY KEY (captcha_id);


--
-- Name: finish_log_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY finish_log
    ADD CONSTRAINT finish_log_pkey PRIMARY KEY (id);


--
-- Name: forecast_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY forecasts
    ADD CONSTRAINT forecast_pkey PRIMARY KEY (id);


--
-- Name: level_update_log_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY level_update_log
    ADD CONSTRAINT level_update_log_pkey PRIMARY KEY (id);


--
-- Name: orders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- Name: price_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY price
    ADD CONSTRAINT price_pkey PRIMARY KEY (id);


--
-- Name: products_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- Name: root_id_lft_check; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT root_id_lft_check UNIQUE (lft, root_id) DEFERRABLE;


--
-- Name: root_id_rgt_check; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT root_id_rgt_check UNIQUE (rgt, root_id) DEFERRABLE;


--
-- Name: root_ids_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY root_ids
    ADD CONSTRAINT root_ids_pkey PRIMARY KEY (id);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: zents_bills_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY zents_bills
    ADD CONSTRAINT zents_bills_pkey PRIMARY KEY (id);


--
-- Name: fki_d｜木; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX "fki_d｜木" ON price USING btree (product_id);


--
-- Name: fki_orders_product_id_products_id; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX fki_orders_product_id_products_id ON orders USING btree (product_id);


--
-- Name: fki_orders_user_id_users_id; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX fki_orders_user_id_users_id ON orders USING btree (user_id);


--
-- Name: users_lft_root_id_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX users_lft_root_id_idx ON users USING btree (lft, root_id);


--
-- Name: users_pid_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX users_pid_idx ON users USING btree (pid);


--
-- Name: users_rgt_root_id_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX users_rgt_root_id_idx ON users USING btree (rgt, root_id);


--
-- Name: last_name_changes; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER last_name_changes AFTER UPDATE ON users FOR EACH ROW EXECUTE PROCEDURE log_upgrade_level();


--
-- Name: fk_orders_product_id_products_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY orders
    ADD CONSTRAINT fk_orders_product_id_products_id FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: orders_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY orders
    ADD CONSTRAINT orders_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id);


--
-- Name: price_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY price
    ADD CONSTRAINT price_product_id_fkey FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

