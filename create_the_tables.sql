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
	insert into level_update_log (user_id, new_level, original_level, new_profit, original_profit, new_first_purchase, original_first_purchase)
	values
	(NEW.id, NEW.level, OLD.level, NEW.profit, OLD.profit, NEW.first_purchase, OLD.first_purchase);
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
    new_first_purchase money
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
    profit money,
    is_admin boolean DEFAULT false,
    id integer NOT NULL,
    is_root boolean DEFAULT false,
    first_purchase money DEFAULT 0,
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
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY address_books ALTER COLUMN id SET DEFAULT nextval('address_books_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY amounts ALTER COLUMN id SET DEFAULT nextval('amounts_id_seq'::regclass);


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
-- Data for Name: address_books; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY address_books (id, user_id, contact, province_id, city_id, address_info, remark, create_time, mobile) FROM stdin;
14	2	王祖藍	1	2	花都区屌你老味	0	2015-02-01 03:42:48.338158	13234535466
\.


--
-- Name: address_books_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('address_books_id_seq', 14, true);


--
-- Data for Name: amounts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY amounts (id, amount, order_id, level) FROM stdin;
4	$3.10	5	1
5	$4.10	5	2
6	$3.00	5	3
\.


--
-- Name: amounts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('amounts_id_seq', 6, true);


--
-- Data for Name: captcha; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY captcha (captcha_id, ip_address, word, captcha_time) FROM stdin;
49	127.0.0.1	kd9xK	1422781697
\.


--
-- Name: captcha_captcha_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('captcha_captcha_id_seq', 49, true);


--
-- Data for Name: finish_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY finish_log (order_id, pay_amt, user_id, parent_user_id, is_root, pay_amt_without_post_fee, is_first, id) FROM stdin;
5	$0.00	2	1	t	$46.50	\N	1
5	$46.50	2	1	t	$46.50	\N	2
\.


--
-- Name: finish_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('finish_log_id_seq', 2, true);


--
-- Name: forecast_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('forecast_id_seq', 1, false);


--
-- Data for Name: forecasts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY forecasts (id, content, create_time) FROM stdin;
1	老味你	2015-01-27 23:46:04.924766
\.


--
-- Data for Name: level_update_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY level_update_log (id, user_id, new_level, upgrade_time, original_profit, original_level, new_profit, original_first_purchase, new_first_purchase) FROM stdin;
1	12	1	2015-02-02 03:57:49.188399	\N	0	\N	$0.00	$0.00
\.


--
-- Name: level_update_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('level_update_log_id_seq', 1, true);


--
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY orders (id, user_id, create_time, update_time, product_id, is_pay, pay_amt, is_correct, count, is_cancelled, is_deleted, level, parent_level, pay_time, address_book_id, is_post, post_fee, finish_time, is_pay_online, is_first, pay_amt_without_post_fee) FROM stdin;
5	2	2015-02-01 03:42:48.338158	2015-02-02 04:13:25	8	t	$46.50	t	15	f	f	1	1	\N	14	f	$0.00	2015-02-02 04:13:25	\N	\N	$46.50
\.


--
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('orders_id_seq', 5, true);


--
-- Data for Name: price; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY price (id, level, product_id, price) FROM stdin;
16	1	8	$3.10
18	3	8	$3.00
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
\.


--
-- Name: price_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('price_id_seq', 30, true);


--
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY products (id, title, properties, feature, usage_method, ingredient, img, create_time, sale_time, off_sale_time, is_valid) FROM stdin;
8	How can I get the row number of results querying with DQL(Doctrine)					0	2015-01-29 02:32:23.746856	\N	\N	t
9	士大夫	士大夫	feature	method	ingredient	bg.jpg	2015-02-01 14:45:58.769059	\N	\N	t
10	士大夫	4564	54646	465456	465465	54cdda1a56575.jpg	2015-02-01 15:47:38.365638	\N	\N	t
11	玩兒					54cdda98d1891.jpg	2015-02-01 15:49:44.865262	\N	\N	t
12	士大夫sss					54cddb199194e.jpg	2015-02-01 15:51:53.626914	\N	\N	t
\.


--
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('products_id_seq', 12, true);


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

COPY users (username, password, create_time, update_time, is_valid, level, name, citizen_id, mobile_no, wechat_id, qq_no, property, lft, rgt, pid, root_id, profit, is_admin, id, is_root, first_purchase) FROM stdin;
zentssuperadmin	21232f297a57a5a743894a0e4a801fc3	2015-01-27 23:00:46.60982	\N	t	0	管理员	8888888888	8888888	888888888	23008600	$88,888,888,888.00	1	2	\N	\N	\N	t	1	f	$0.00
testuser	05a671c66aefea124cc08b76ea6d30bb	2015-01-31 21:52:06.22617	\N	t	0	家家酒	4401010101010101	12381274891	尸杰尸杰	87495	\N	1	8	1	8	\N	f	2	t	$0.00
subuser	73a90acaae2b1ccc0e969709665bc62f	2015-02-01 00:20:04.60615	\N	\N	0	家家酒	237498237958723	12381274892	尸杰尸杰	23589235	\N	2	3	2	8	\N	f	8	f	$0.00
subuser2	e8408cb7570728580e2cb66f1a4b1ee4	2015-02-01 00:24:36.284857	\N	\N	0	yweuir	jkljluioiwer	66672234223	33445333	73589273	\N	4	5	2	8	\N	f	11	f	$0.00
sdffwer	8e8a359d605a815dc118db3877c22b0e	2015-02-01 01:24:47.842887	\N	\N	1	werwe	ewrkwhetkhk	hkjhwwtwety	hwtwket	34795834	\N	6	7	2	8	\N	f	12	f	$0.00
\.


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('users_id_seq', 12, true);


--
-- Name: amounts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY amounts
    ADD CONSTRAINT amounts_pkey PRIMARY KEY (id);


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
-- Name: users_root_id_lft_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_root_id_lft_key UNIQUE (root_id, lft);


--
-- Name: users_root_id_rgt_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_root_id_rgt_key UNIQUE (root_id, rgt);


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

