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
    post_fee money
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
-- Name: users; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE users (
    id integer NOT NULL,
    username character varying(16),
    password character(32),
    create_time timestamp without time zone DEFAULT now(),
    update_time timestamp without time zone,
    is_valid boolean,
    level integer DEFAULT 1,
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
    CONSTRAINT users_check CHECK ((lft < rgt)),
    CONSTRAINT users_lft_check CHECK ((lft > 0)),
    CONSTRAINT users_rgt_check CHECK ((rgt > 1))
);


ALTER TABLE users OWNER TO postgres;

--
-- Name: unupdatable_user_view; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW unupdatable_user_view AS
 SELECT ((users.id * 3) + 100) AS id,
    users.qq_no,
    (users.lft + users.rgt) AS t
   FROM users;


ALTER TABLE unupdatable_user_view OWNER TO postgres;

--
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_id_seq OWNER TO postgres;

--
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE user_id_seq OWNED BY users.id;


--
-- Name: user_view; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW user_view AS
 SELECT users.id,
    users.qq_no,
    users.lft,
    users.rgt
   FROM users;


ALTER TABLE user_view OWNER TO postgres;

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

ALTER TABLE ONLY forecasts ALTER COLUMN id SET DEFAULT nextval('forecast_id_seq'::regclass);


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

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('user_id_seq'::regclass);


--
-- Data for Name: address_books; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY address_books (id, user_id, contact, province_id, city_id, address_info, remark, create_time, mobile) FROM stdin;
\.


--
-- Name: address_books_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('address_books_id_seq', 1, false);


--
-- Data for Name: amounts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY amounts (id, amount, order_id, level) FROM stdin;
\.


--
-- Name: amounts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('amounts_id_seq', 1, false);


--
-- Data for Name: captcha; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY captcha (captcha_id, ip_address, word, captcha_time) FROM stdin;
4	127.0.0.1	SA8ci	1422370525
5	127.0.0.1	2lM1Z	1422370569
6	127.0.0.1	6Wrby	1422370858
36	127.0.0.1	z1xhk	1422372872
37	127.0.0.1	bCpej	1422372883
\.


--
-- Name: captcha_captcha_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('captcha_captcha_id_seq', 37, true);


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
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY orders (id, user_id, create_time, update_time, product_id, is_pay, pay_amt, is_correct, count, is_cancelled, is_deleted, level, parent_level, pay_time, address_book_id, is_post, post_fee) FROM stdin;
\.


--
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('orders_id_seq', 1, false);


--
-- Data for Name: price; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY price (id, level, product_id, price) FROM stdin;
16	1	8	$3.10
18	3	8	$3.00
17	2	8	$4.10
\.


--
-- Name: price_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('price_id_seq', 18, true);


--
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY products (id, title, properties, feature, usage_method, ingredient, img, create_time, sale_time, off_sale_time, is_valid) FROM stdin;
8	How can I get the row number of results querying with DQL(Doctrine)					0	2015-01-29 02:32:23.746856	\N	\N	t
\.


--
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('products_id_seq', 8, true);


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('user_id_seq', 1, false);


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY users (id, username, password, create_time, update_time, is_valid, level, name, citizen_id, mobile_no, wechat_id, qq_no, property, lft, rgt, pid, root_id) FROM stdin;
1	zentssuperadmin	21232f297a57a5a743894a0e4a801fc3	2015-01-27 23:00:46.60982	\N	t	1	管理员	8888888888	8888888	888888888	23008600	$88,888,888,888.00	1	2	\N	\N
\.


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
-- Name: forecast_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY forecasts
    ADD CONSTRAINT forecast_pkey PRIMARY KEY (id);


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
-- Name: fk_orders_product_id_products_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY orders
    ADD CONSTRAINT fk_orders_product_id_products_id FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_orders_user_id_users_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY orders
    ADD CONSTRAINT fk_orders_user_id_users_id FOREIGN KEY (user_id) REFERENCES users(id);


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

