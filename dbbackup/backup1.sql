--
-- PostgreSQL database dump
--

-- Dumped from database version 16.1
-- Dumped by pg_dump version 16.1

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

--
-- Name: author_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.author_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.author_id_seq OWNER TO postgres;

--
-- Name: pksequence; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pksequence
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 100000
    CACHE 1;


ALTER SEQUENCE public.pksequence OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: authors; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.authors (
    author_id integer DEFAULT nextval('public.pksequence'::regclass) NOT NULL,
    first_name character varying(100) NOT NULL,
    last_name character varying(100) NOT NULL,
    nationality character varying(50),
    biography text,
    gender character varying(10),
    email character varying(100),
    books_published integer DEFAULT 0,
    username character varying(50),
    phone character varying(20),
    user_id integer,
    date_created timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    socialmedia_handles character varying(255),
    address character varying(255),
    website character varying(255)
);


ALTER TABLE public.authors OWNER TO postgres;

--
-- Name: books_bookid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.books_bookid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.books_bookid_seq OWNER TO postgres;

--
-- Name: books; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.books (
    bookid integer DEFAULT nextval('public.books_bookid_seq'::regclass) NOT NULL,
    title character varying(255),
    author character varying(255),
    publisher character varying(255),
    price numeric(10,2),
    genre character varying(50),
    language character varying(50),
    grade character varying(10),
    details text,
    pages integer,
    covertype character varying(50),
    damaged character varying(50),
    isbn character varying(20),
    series character varying(255),
    subject character varying(255),
    edition character varying(50),
    bookrating numeric(3,2),
    seller_id integer,
    priceinbulk numeric(10,2),
    mininbulk integer,
    front_page_image character varying(255),
    back_page_image character varying(255),
    other_images character varying(255),
    sellercategory character varying(255),
    CONSTRAINT chk_rating CHECK (((bookrating >= (0)::numeric) AND (bookrating <= (5)::numeric)))
);


ALTER TABLE public.books OWNER TO postgres;

--
-- Name: client_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.client_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.client_id_seq OWNER TO postgres;

--
-- Name: clients; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.clients (
    client_id integer DEFAULT nextval('public.pksequence'::regclass) NOT NULL,
    client_type character varying(50) NOT NULL,
    first_name character varying(100),
    last_name character varying(100),
    organization_name character varying(150),
    email character varying(100) NOT NULL,
    phone character varying(20),
    address character varying(255),
    county character varying(100),
    date_created timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    user_id integer,
    points integer DEFAULT 0 NOT NULL,
    contact_first_name character varying(100),
    contact_last_name character varying(100),
    contact_email character varying(100),
    contact_phone character varying(20)
);


ALTER TABLE public.clients OWNER TO postgres;

--
-- Name: manufacturer_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.manufacturer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.manufacturer_id_seq OWNER TO postgres;

--
-- Name: manufacturers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.manufacturers (
    manufacturer_id integer DEFAULT nextval('public.pksequence'::regclass) NOT NULL,
    manufacturer_name character varying(150) NOT NULL,
    contact_first_name character varying(100) NOT NULL,
    contact_last_name character varying(100) NOT NULL,
    contact_email character varying(100) NOT NULL,
    contact_phone character varying(20),
    manufacturer_email character varying(100) NOT NULL,
    manufacturer_phone character varying(20),
    address character varying(255),
    website character varying(255),
    products_offered text[],
    date_created timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    user_id integer
);


ALTER TABLE public.manufacturers OWNER TO postgres;

--
-- Name: order_sequence; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.order_sequence
    START WITH 0
    INCREMENT BY 1
    MINVALUE 0
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.order_sequence OWNER TO postgres;

--
-- Name: orders; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.orders (
    order_id integer DEFAULT nextval('public.order_sequence'::regclass) NOT NULL,
    client_id integer NOT NULL,
    order_date date DEFAULT CURRENT_DATE,
    total_amount numeric(10,2) NOT NULL,
    status character varying(20) NOT NULL,
    payment_method character varying(50) NOT NULL,
    shipping_address character varying(255) NOT NULL,
    delivery_date date,
    product_type character varying(50) NOT NULL,
    product_id integer NOT NULL,
    unit_price numeric(10,2) NOT NULL,
    quantity integer NOT NULL,
    seller_id integer
);


ALTER TABLE public.orders OWNER TO postgres;

--
-- Name: publisher_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.publisher_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.publisher_id_seq OWNER TO postgres;

--
-- Name: publishers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.publishers (
    publisher_id integer DEFAULT nextval('public.pksequence'::regclass) NOT NULL,
    publisher_name character varying(150) NOT NULL,
    contact_first_name character varying(100) NOT NULL,
    contact_last_name character varying(100) NOT NULL,
    contact_email character varying(100) NOT NULL,
    contact_phone character varying(20),
    publisher_email character varying(100) NOT NULL,
    publisher_phone character varying(20),
    address character varying(255),
    website character varying(255),
    books_published integer DEFAULT 0,
    date_created timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    user_id integer
);


ALTER TABLE public.publishers OWNER TO postgres;

--
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_id_seq OWNER TO postgres;

--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    user_id integer DEFAULT nextval('public.pksequence'::regclass) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    role character varying(50),
    createdat timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    category character varying(255)
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Data for Name: authors; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.authors (author_id, first_name, last_name, nationality, biography, gender, email, books_published, username, phone, user_id, date_created, socialmedia_handles, address, website) FROM stdin;
366	Chelsea	Kibet	Kenyan	I am a passionate wordsmith, weaving tales that captivate the imagination and stir the soul. With each stroke of my pen, I breathe life into characters and landscapes, inviting readers into worlds of wonder and discovery. Through the power of storytelling, I explore the depths of human emotion and the complexities of the human experience. Join me on a journey through the realms of fiction, where dreams take flight and possibilities abound.\r\n\r\n\r\n\r\n\r\n	female	chelseakibet@gmail.com	0	chelsea_kibet	0712345678	365	2024-03-10 15:55:58.00084	@chelseakibet	Langata Road,Nairobi	www.chelseakibet.com
368	Adrian	Omondi	Barbudans	 456 Ngong Road, Nairobi	male	adrianomondi@gmail.com	0	adrian_omondi	0723456789	367	2024-03-10 15:57:15.851139	@adrianomondi, Adrian Omondi	 456 Ngong Road, Nairobi	www.adrianomondi.com
370	Kevin	Wanjohi	Kenyan	tetur adipiscing elit. Sed eget risus vel justo lacinia gravida.	male	kevinwanjohi@gmail.com	0	kevin_wanjohi	0734567890	369	2024-03-10 15:58:29.61452	@kevinwanjohi, Kevin Wanjohi	789 Mombasa Road, Nairobi	www.kevinwanjohi.com
376	Tracy	Kamau	Tanzanian	adipiscing 	female	tracykamau@gmail.com	0	tracy_kamau	0712345678	375	2024-03-10 16:00:28.445741	@tracykamau	123 Thika Road, Nairobi	www.tracykamau.com
378	Brian	Ochieng	Ugandan	sectetur adipiscing elit. Sed eget risus vel justo lacinia gravida.	male	brianochieng@gmail.com	0	brian_ochieng	0723456789	377	2024-03-10 16:01:39.056839	Brian Ochieng	456 Eldoret Road, Nairobi	 www.brianochieng.com
380	Duncan	Chege	Kenyan	olor sit amet, consectetur adipiscing elit. Sed eget risus vel justo lacinia gravida.	male	duncanchege@gmail.com	0	duncan_chege	0712345678	379	2024-03-10 16:04:16.929794		23 Nakuru Road, Nakuru 	www.duncanchege.com
382	Faith	Mwende	Afghan	456 Nyeri Road, Nairobi	male	faithmwende@gmail.com	0	faith_mwende	0723456789	381	2024-03-10 16:05:15.546068		456 Nyeri Road, Nyeri	
384	Kelvin	Otieno	Afghan	Meru 	male	kelvinotieno@gmail.com	0	kelvin_otieno	0734567890	383	2024-03-10 16:06:04.428345		789 Meru Road, Meru 	
386	Melissa	Atieno	Afghan	onsectetur adipiscing elit. Sed ege	female	melissaatieno@gmail.com	0	melissa_atieno	0712345678	385	2024-03-10 16:06:58.72206		23 Kitale Road, Kitale 	
388	Betanny	Wairimu	Afghan	sdwegrthfygkui	female	tiffahnick012@gmail.com	0	1042467	0786143603	387	2024-03-10 16:23:23.357282		P.O BOX 4085	mywebsite2002.com
434	Melissa	Kenneth	Kenyan	This is my dummy biography	female	melissakent@gmail.com	0	Melly	0786547897	433	2024-03-12 16:20:07.958557		Ngummo Newa	
\.


--
-- Data for Name: books; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.books (bookid, title, author, publisher, price, genre, language, grade, details, pages, covertype, damaged, isbn, series, subject, edition, bookrating, seller_id, priceinbulk, mininbulk, front_page_image, back_page_image, other_images, sellercategory) FROM stdin;
5	KLB Early Grade English Language Activities Grade 2	Not Mentioned	KLB	600.00	languages	english	grade_2	This Grade 2 English Language Activities Book uses research-based techniques to support pupils in developing English language skills. This book covers core competencies, pertinent and contemporary issues, and four literacy areas: listening, speaking, reading and writing, as well as all themes and values described in the revised curriculum. The book offers learners in Grade 2 a variety of learning experiences and assessments that support the expected learning outcomes in the key activity areas.	560	SoftCover	no	9789966656247	no	english language	1st Edition	3.50	433	590.00	70	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f0613d406747.62702249.jpg	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f0613d408ed8.45535879.jpg	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f0613d40ab05.05093723.jpg,D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f0613d40c7b1.19108559.jpg,D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f0613d40e641.60422646.png	Author
7	Enjoy Mathematical Activities Learners Book Grade 3	Paul Njoga, Lucy A. Ngode	Moran Publishers	365.00	mathematics	english	grade_3	Enjoy Mathematical Activities is a primary school course developed in line with the requirements of a competency based curriculum. The books employ an inquiry based approach to learning which equips learners with skills, knowledge, values and the quest to explore and discover by themselves. The course:\r\n\r\nemphasises on enquiry based approach and problem solving techniques \r\nhas carefully graded Mathematical language that enhances understanding \r\nhas a variety of hands-on activities which enable learners to explore their skills and immediate environment \r\nis learner centred with to - do tasks that make learning of Mathematical Activities enjoyable \r\ninvolves learners in aspects of observations, discussions and drawing conclusions from Mathematical Activities \r\nis written and developed by skilled educators with an apt experience in the competence based curriculum\r\nThe teachers guidebooks have been written in on easy to understand format. with guiding procedure on how to guide learners through the activities in each lesson. They address the competences. pertinent and contemporary issues, values and other requirements stipulated in the syllabus. 	540	SoftCover	no	9789966630360 	no	mathematics	1st Edition	3.50	433	355.00	35	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f066af6d87e1.44941597.jpg	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f066af6dc952.57823606.jpg	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f066af6e09d5.25281613.png,D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f066af6e56f3.56415615.jpg,D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f066af6e9459.37595065.jpg	Author
6	Spotlight Workbook Mathematics Act GD2	Not Mentioned	Spotlight Publishers	348.00	mathematics	english	grade_2	Spotlight Workbook Mathematics Activities Grade 2 has been uniquely designed to assist a Grade 2 learner in understanding \r\nthe Competency-Based Curriculum in an easy and simplified way.\r\nKey features of the book:\r\n-Conforms fully to the curriculum design. \r\n-Attractive full colour illustrations.\r\n-Well-researched end of term assessments. \r\n-Numerous learner-centred activities. \r\n-Answers to all questions	560	SoftCover	no	9789966571977 	no	mathematics	1st Edition	5.00	433	335.00	70	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f0627ac4a289.00822933.jpg	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f0627ac4c608.66399648.jpg	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f0627ac4e054.54385476.png,D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f0627ac4fcd2.13638082.jpg,D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f0627ac51555.31248622.jpg	Author
4	Spotlight English Leaners Book	Not Mentioned	Spotlight Publishers	500.00	languages	english	grade_4	Spotlight English Learners Book is meticulously crafted to cater specifically to the needs of English language learners, offering a tailored learning experience that fosters proficiency and confidence. This comprehensive resource is meticulously designed to provide learners with the tools they need to excel in their language acquisition journey.\r\n\r\nTailored Content:\r\nEvery aspect of Spotlight English Learners Book is thoughtfully tailored to meet the unique needs of English language learners. From carefully selected vocabulary to structured grammar exercises, each component of the book is designed to optimize learning outcomes.\r\n\r\nEngaging Activities:\r\nThe book features a diverse array of engaging activities that captivate learners attention and encourage active participation. From interactive dialogues to immersive reading passages, learners are immersed in language-rich content that stimulates their linguistic development.\r\n\r\nPractical Application:\r\nSpotlight English Learners Book emphasizes practical application, providing learners with opportunities to apply their language skills in real-life contexts. Through role-plays, discussions, and authentic materials, learners develop the confidence to communicate effectively in various situations.\r\n\r\nProgressive Approach:\r\nThe book follows a progressive approach, guiding learners through incremental stages of language acquisition. Starting with foundational concepts and gradually advancing to more complex linguistic structures, learners build a solid foundation for language proficiency.\r\n\r\nMultimedia Integration:\r\nIncorporating multimedia elements, Spotlight English Learners Book offers a dynamic learning experience that appeals to diverse learning styles. From audio recordings to online interactive exercises, learners benefit from a multimedia-rich environment that enhances comprehension and retention.\r\n\r\nExpert Guidance:\r\nAuthored by language experts and educators, Spotlight English Learners Book provides learners with expert guidance and support throughout their language learning journey. Clear explanations, helpful tips, and insightful feedback empower learners to overcome challenges and achieve success.\r\n\r\nAccessible Resources:\r\nWith supplementary resources and online support, Spotlight English Learners Book ensures accessibility for both learners and educators. Additional materials, such as teacher guides and digital resources, facilitate seamless integration into classroom instruction.	540	SoftCover	no	567298362823	no	english language	1st Edition	2.00	433	480.00	50	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f05e8877a176.33936811.jpg	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f05e8877d0d1.61811700.jpg	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f05e8877ee49.70864482.jpg,D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f05e88780949.28537888.jpg,D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f05e88782bb1.59567237.jpg	Author
8	Spotlight Physical and Health Education Grade 6	Not Mentioned	Spotlight Publishers	600.00	physical_health	english	grade_6	Spotlight Physical and Health Education Learners Book Grade 6 has been uniquely designed to equip the learner with skills, values and competencies that will enable him or her to navigate through the learning environment. The book will greatly help the learner to develop creativity and nurture sports talent.	560	SoftCover	no	9789966573049 	no	physical education	1st Edition	4.00	433	590.00	50	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f069aa2727f1.84020670.jpg	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f069aa2765a5.90761987.jpg	D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f069aa279586.10954897.jpg,D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f069aa27c573.77200842.png,D:\\xammp2\\htdocs\\BookStore2\\Images\\uploads/65f069aa27f7d8.63646560.jpg	Author
\.


--
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.clients (client_id, client_type, first_name, last_name, organization_name, email, phone, address, county, date_created, user_id, points, contact_first_name, contact_last_name, contact_email, contact_phone) FROM stdin;
318	Individual	John	Smith	\N	john.smith@example.com	254712345678	123 Main Street, Nairobi	123 Main Street, Nairobi\t	2024-03-10 14:16:59.835855	317	0	\N	\N	\N	\N
320	Organization	\N	\N	Sunshine Primary School\t	sunshine@example.com\t	0712345678\t	123 Main Street, Nairobi	123 Main Street, Nairobi\t	2024-03-10 14:17:08.259109	319	0	Betanny	Wairimu	wairimu@gmail.com	0710265678\t
322	Organization	\N	\N	Green Valley Academy\t	info@greenvalley.ac.ke\t	0723456789\t	456 Elm Street, Kisumu\t	Kisumu	2024-03-10 14:44:47.830097	321	0	James 	Omollo	James@gmail.com	0723456789
324	Individual	Jane	Kamau	\N	 jane.kamau@gmail.com	071092345678	456 Elm Street, Kisumu\t	Kisumu	2024-03-10 14:45:15.709046	323	0	\N	\N	\N	\N
326	Organization	\N	\N	Ocean View Academy\t	oceanview@gmail.com\t	0734567890	789 Mtumbuni Street, 	Mombasa\t	2024-03-10 14:49:23.983598	325	0	Sarah 	Wanjiku	sarah@gmail.com	0734567890
328	Individual	David	Ngigi	\N	david.ngigi@gmail.com	0725473455	789 Mtumbuni Street, 	Mombasa\t	2024-03-10 14:49:28.084743	327	0	\N	\N	\N	\N
330	Organization	\N	\N	 Moi Primary School 	info@moiprimaryschool.ke 	0700000201	123 Moi Road	Nairobi           	2024-03-10 15:06:45.685728	329	0	Alice	Wanjiru	alice@gmail.com	0734567345
332	Organization	\N	\N	Kibra Primary School	info@kibraprimaryschool.ke	0700000202	456 Kibra Street	Nairobi           	2024-03-10 15:08:30.097145	331	0	Emmanuel	Kimotho	emmanuelkimotho@gmail.com	0734567037
334	Organization	\N	\N	Kisumu Central Primary School	info@kisumucentralprimaryschool.ke	0700000203	789 Kisumu Central Road	Kisumu 	2024-03-10 15:09:40.971586	333	0	Martin	Kimotho	Martinkimotho@gmail.com	0710967037
336	Organization	\N	\N	Nakuru East Primary School	info@nakurueastprimaryschool.ke	0700000204	101 Nakuru East Lane	Nakuru 	2024-03-10 15:10:45.090374	335	0	Marina	Mwangi	Marina@gmail.com	0710967345
338	Organization	\N	\N	Eldoret Township Primary School	info@eldorettownshipprimaryschool.ke	0700000205	 234 Eldoret Township Close	Eldoret	2024-03-10 15:12:09.760254	337	0	Mary	Mwangina	MaryMwangina@gmail.com	0720967345
340	Organization	\N	\N	Thika Primary School	info@thikaprimaryprimaryschool.ke	0705600205	Makongeni street	Thika	2024-03-10 15:13:32.852228	339	0	Mary	Thagayo	MaryThagayo@gmail.com	0765437345
342	Organization	\N	\N	Nyeri Township Primary School	info@nyeritownshipprimaryschool.ke	0700000207	456 Nyeri Township Avenue	Nyeri 	2024-03-10 15:14:50.36193	341	0	Grace	Mugwanja	MugwanjaGrace@gmail.com	0765437234
344	Individual	John	Kipchoge	\N	 johnkipchoge@gmail.com	0725473456	456 Nyeri Township Avenue	Nyeri 	2024-03-10 15:22:25.023022	343	0	\N	\N	\N	\N
346	Individual	Mary	Wafula	\N	marywafula@yahoo.com	0723456789	456 Nyeri Township Avenue	Nyeri 	2024-03-10 15:23:13.625312	345	0	\N	\N	\N	\N
348	Individual	Peter	Kimani	\N	 peterkimani@gmail.com	0734567890	456 Nyeri Township Avenue	Nyeri 	2024-03-10 15:24:19.579392	347	0	\N	\N	\N	\N
350	Individual	Jane	Achieng	\N	 janeachieng@yahoo.com	0745678901	456 Nyeri Township Avenue	Nyeri 	2024-03-10 15:25:44.199385	349	0	\N	\N	\N	\N
352	Individual	David	Mutisya	\N	 davidmutisya@gmail.com	0756789012	456 Nyeri Township Avenue	Nyeri 	2024-03-10 15:26:36.567449	351	0	\N	\N	\N	\N
354	Individual	Grace	Muthoni	\N	gracemuthoni@yahoo.com	0767890123	456 Nyeri Township Avenue	Nyeri 	2024-03-10 15:27:21.584028	353	0	\N	\N	\N	\N
356	Individual	Patrick	Kiprop	\N	patrickkiprop@gmail.com	0778901234	456 Nyeri Township Avenue	Nyeri 	2024-03-10 15:28:10.057168	355	0	\N	\N	\N	\N
358	Individual	Mercy	Atieno	\N	mercyatieno@yahoo.com	0789012345	456 Nyeri Township Avenue	Nyeri 	2024-03-10 15:28:50.387066	357	0	\N	\N	\N	\N
364	Individual	Caroline	Wambui	\N	carolinewambui@yahoo.com	0701234567	456 Nyeri Township Avenue	Nyeri 	2024-03-10 15:30:43.378098	363	0	\N	\N	\N	\N
436	Individual	Geib	Lusenaka	\N	GLusenaka@gmail.com	0786143601	Ngumo Newa, 115\n	Nairobi	2024-03-13 16:56:37.970982	435	2550	\N	\N	\N	\N
439	Individual	Betanny	Wairimu	\N	beisymish56@gmail.com	0786143603			2024-03-14 21:25:54.417494	438	0	\N	\N	\N	\N
\.


--
-- Data for Name: manufacturers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.manufacturers (manufacturer_id, manufacturer_name, contact_first_name, contact_last_name, contact_email, contact_phone, manufacturer_email, manufacturer_phone, address, website, products_offered, date_created, user_id) FROM stdin;
414	TechSupplies Ltd	Isaac	Kimani	isaackimani@techsupplies.com	0712345678	info@techsupplies.com	0723456789	123 Enterprise Road, Nairobi	www.techsupplies.com	{"Classroom Supplies","Laboratory Equipment",Furniture,"Educational Toys and Games"}	2024-03-10 16:38:14.360974	413
416	PaperWorld Enterprises	Lucy	Nyambura	lucynyambura@paperworld.com	0734567890	info@paperworld.com	0712345678	456 Printers Street, Nairobi	www.paperworld.com	{"Classroom Supplies",Furniture}	2024-03-10 16:39:32.977718	415
418	InkSpot Ltd	Benson	Muturi	bensonmuturi@inkspot.com	0712345678	info@inkspot.com	0723456789	789 Toners Avenue, Nairobi	www.inkspot.com	{"Classroom Supplies",Furniture}	2024-03-10 16:40:51.524931	417
420	Pencils and Pens Co.	Grace	Mwende	gracemwende@pencilsandpens.com	0734567890	info@pencilsandpens.com	0712345678	123 Erasers Street, Nairobi	www.pencilsandpens.com	{"Classroom Supplies"}	2024-03-10 16:42:29.212658	419
422	BooksPlus Supplies Ltd	John	Karanja	johnkaranja@booksplus.com	0712345678	info@booksplus.com	0723456789	456 Stationery Road, Nairobi	www.booksplus.com	{"Classroom Supplies","Teaching Aids"}	2024-03-10 16:43:52.707846	421
424	PrintTech Solutions Ltd	Catherine	Akinyi	catherineakinyi@printtech.com	0734567890	 info@printtech.com	0712345678	789 Ink Street, Nairobi	www.printtech.com	{"Classroom Supplies","Teaching Aids","Laboratory Equipment","Educational Toys and Games"}	2024-03-10 16:45:26.809913	423
426	ArtCraft Supplies Ltd	David	Kariuki	davidkariuki@artcraft.com	0734567890	info@artcraft.com	0723456789	123 Crayons Street, Nairobi	 www.artcraft.com	{"Classroom Supplies","Teaching Aids","Laboratory Equipment","Educational Toys and Games"}	2024-03-10 16:46:48.130645	425
428	SmartPrint Co.	Sarah	Wambui	sarahwambui@smartprint.com	0734567890	info@smartprint.com	0712345678	56 Printer Cartridge Road, Nairobi	www.smartprint.com	{"Educational Technology","Laboratory Equipment","Educational Toys and Games"}	2024-03-10 16:48:06.578725	427
430	TechInk Enterprises	Michael	Otieno	michaelotieno@techink.com	0712345678	info@techink.com	0723456789	789 Inkjet Street, Nairobi	www.techink.com	{"Educational Technology","Laboratory Equipment","Educational Toys and Games"}	2024-03-10 16:49:14.215793	429
432	CraftMaster Supplies Ltd	Alice	Achieng	aliceachieng@craftmaster.com	0734567890	info@craftmaster.com	0712345678	123 Paintbrush Road, Nairobi	www.craftmaster.com	{"Educational Technology","Laboratory Equipment","Educational Toys and Games"}	2024-03-10 16:50:25.227714	431
\.


--
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.orders (order_id, client_id, order_date, total_amount, status, payment_method, shipping_address, delivery_date, product_type, product_id, unit_price, quantity, seller_id) FROM stdin;
0	436	2024-03-13	500.00	Pending	mpesa	Ngumo Newa, 115	2024-03-17	Book	4	500.00	3	433
1	436	2024-03-13	600.00	Delivered	mpesa	Ngumo Newa, 115	2024-03-13	Book	5	600.00	2	433
2	436	2024-03-13	348.00	Pending	mpesa	Ngumo Newa, 115	2024-03-17	Book	6	348.00	4	433
3	436	2024-03-13	365.00	Delivered	mpesa	Ngumo Newa, 115	2024-03-13	Book	7	365.00	1	433
4	436	2024-03-13	600.00	Pending	mpesa	Ngumo Newa, 115	2024-03-17	Book	8	600.00	5	433
5	318	2024-03-14	500.00	Pending	mpesa	123 Main Street, Nairobi	2024-03-17	Book	4	500.00	2	433
6	318	2024-03-14	600.00	Delivered	mpesa	123 Main Street, Nairobi	2024-03-13	Book	5	600.00	3	433
8	318	2024-03-14	365.00	Delivered	mpesa	123 Main Street, Nairobi	2024-03-13	Book	7	365.00	4	433
9	318	2024-03-14	600.00	Pending	mpesa	123 Main Street, Nairobi	2024-03-17	Book	8	600.00	5	433
10	324	2024-03-14	500.00	Pending	mpesa	456 Elm Street, Kisumu	2024-03-17	Book	4	500.00	2	433
11	324	2024-03-14	600.00	Delivered	mpesa	456 Elm Street, Kisumu	2024-03-13	Book	5	600.00	3	433
12	324	2024-03-14	348.00	Pending	mpesa	456 Elm Street, Kisumu	2024-03-17	Book	6	348.00	1	433
13	324	2024-03-14	365.00	Delivered	mpesa	456 Elm Street, Kisumu	2024-03-13	Book	7	365.00	4	433
14	324	2024-03-14	600.00	Pending	mpesa	456 Elm Street, Kisumu	2024-03-17	Book	8	600.00	5	433
16	328	2024-03-14	600.00	Delivered	mpesa	789 Mtumbuni Street, Mombasa	2024-03-13	Book	5	600.00	3	433
17	328	2024-03-14	348.00	Pending	mpesa	789 Mtumbuni Street, Mombasa	2024-03-17	Book	6	348.00	1	433
18	328	2024-03-14	365.00	Delivered	mpesa	789 Mtumbuni Street, Mombasa	2024-03-13	Book	7	365.00	4	433
19	328	2024-03-14	600.00	Pending	mpesa	789 Mtumbuni Street, Mombasa	2024-03-17	Book	8	600.00	5	433
20	344	2024-03-14	500.00	Pending	mpesa	456 Nyeri Township Avenue, Nyeri	2024-03-17	Book	4	500.00	2	433
21	344	2024-03-14	600.00	Delivered	mpesa	456 Nyeri Township Avenue, Nyeri	2024-03-13	Book	5	600.00	3	433
22	344	2024-03-14	348.00	Pending	mpesa	456 Nyeri Township Avenue, Nyeri	2024-03-17	Book	6	348.00	1	433
23	344	2024-03-14	365.00	Delivered	mpesa	456 Nyeri Township Avenue, Nyeri	2024-03-13	Book	7	365.00	4	433
24	344	2024-03-14	600.00	Pending	mpesa	456 Nyeri Township Avenue, Nyeri	2024-03-17	Book	8	600.00	5	433
30	320	2024-03-14	25000.00	Pending	mpesa	123 Main Street, Nairobi	2024-03-17	Book	4	500.00	50	433
31	320	2024-03-14	36000.00	Delivered	mpesa	123 Main Street, Nairobi	2024-03-13	Book	5	600.00	60	433
32	322	2024-03-14	15660.00	Pending	mpesa	456 Elm Street, Kisumu	2024-03-17	Book	6	348.00	45	433
33	326	2024-03-14	14600.00	Delivered	mpesa	789 Mtumbuni Street, Mombasa	2024-03-13	Book	7	365.00	40	433
34	330	2024-03-14	33000.00	Pending	mpesa	123 Moi Road, Nairobi	2024-03-17	Book	8	600.00	55	433
35	332	2024-03-14	22500.00	Delivered	mpesa	456 Kibra Street, Nairobi	2024-03-13	Book	4	500.00	45	433
7	318	2024-03-14	348.00	Pending	mpesa	123 Main Street, Nairobi	2024-03-19	Book	6	348.00	1	433
15	328	2024-03-14	500.00	Pending	mpesa	789 Mtumbuni Street, Mombasa	2024-03-18	Book	4	500.00	2	433
36	334	2024-03-14	36000.00	Pending	mpesa	789 Kisumu Central Road, Kisumu	2024-03-17	Book	5	600.00	60	433
37	336	2024-03-14	19140.00	Delivered	mpesa	101 Nakuru East Lane, Nakuru	2024-03-13	Book	6	348.00	55	433
38	338	2024-03-14	18250.00	Pending	mpesa	234 Eldoret Township Close, Eldoret	2024-03-17	Book	7	365.00	50	433
39	340	2024-03-14	36000.00	Delivered	mpesa	Makongeni street, Thika	2024-03-13	Book	8	600.00	60	433
40	342	2024-03-14	25000.00	Pending	mpesa	456 Nyeri Township Avenue, Nyeri	2024-03-17	Book	4	500.00	50	433
41	436	2024-03-14	33000.00	Delivered	mpesa	Ngumo Newa, 115, Nairobi	2024-03-13	Book	5	600.00	55	433
43	342	2024-03-14	25000.00	Declined	not applicable	not applicable	2024-03-20	Book\n	4	500.00	50	433
\.


--
-- Data for Name: publishers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.publishers (publisher_id, publisher_name, contact_first_name, contact_last_name, contact_email, contact_phone, publisher_email, publisher_phone, address, website, books_published, date_created, user_id) FROM stdin;
390	Nairobi Publications Ltd	Jane	Njeri	janenjeri@nairobipublications.com	0712345678	info@nairobipublications.com	0723456789	123 Koinange Street, Nairobi	www.nairobipublications.com	0	2024-03-10 16:23:28.309266	389
394	Kenya Books and Co.	Peter	Mwangi	 petermwangi@kenyabooks.co.ke	0734567890	info@kenyabooks.co.ke	0712345678	456 Moi Avenue, Nairobi	www.kenyabooks.co.ke	0	2024-03-10 16:25:55.001607	393
396	Mombasa Publishers Ltd	Grace	Kariuki	gracekariuki@mombasapublishers.com	0723456789	info@mombasapublishers.com	0734567890	 789 Digo Road, Mombasa		0	2024-03-10 16:27:01.366619	395
400	Rift Valley Books Ltd	David	Kamau	davidkamau@riftvalleybooks.com	0712345678	info@riftvalleybooks.com	0723456789	23 Nakuru Street, Nakuru	www.riftvalleybooks.com	0	2024-03-10 16:28:20.370759	399
402	Coastline Publications Ltd	Alice	Kipsang	alicekipsang@coastlinepublications.com	0734567890	info@coastlinepublications.com	0712345678	Malindi Road, Mombasa		0	2024-03-10 16:29:23.66052	401
404	Great Lakes Publishers Ltd	George	Mutua	georgemutua@greatlakespublishers.com	0712345678	info@greatlakespublishers.com	0723456789	Kisumu Road, Kisumu		0	2024-03-10 16:30:16.938945	403
406	Central Books Ltd	Susan	Kimani	susankimani@centralbooks.com	0734567890	info@centralbooks.com	0712345678	Nyeri Street, Nyeri	www.centralbooks.com	0	2024-03-10 16:31:14.718035	405
408	Highlands Publishers Ltd	Daniel	Ochieng	danielochieng@highlandspublishers.com	0712345678	info@highlandspublishers.com	0723456789	0723456789		0	2024-03-10 16:33:41.074827	407
410	Western Books Ltd	Mercy	Njoroge	mercynjoroge@westernbooks.com	0734567890	info@westernbooks.com	0712345678	456 Kakamega Road, Kakamega		0	2024-03-10 16:34:43.030918	409
412	Lakefront Publications Ltd	Michael	Gitau	michaelgitau@lakefrontpublications.com	0723456789	info@lakefrontpublications.com	0734567890	789 Kisii Street, Kisii	www.lakefrontpublications.com	0	2024-03-10 16:35:50.769517	411
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (user_id, email, password, role, createdat, category) FROM stdin;
317	john.smith@example.com	f6296bc45f9be94dc8bf8ca3346773a01932becd7688ffc3f8958ef3861d5a61	Client	2024-03-10 14:16:59.827582	Individual
319	sunshine@example.com\t	4fc66b57abf76d2a4d92660567d457e8e8afcaa821b41bcd8a0706a76aa353ff	Client	2024-03-10 14:17:08.256383	Organization
321	info@greenvalley.ac.ke\t	fdf2fe287e0e726ffb07fd5d6e6c0296ee7ddf9e52c459276efd34bea4ea0bad	Client	2024-03-10 14:44:47.826649	Organization
323	 jane.kamau@gmail.com	a9f49703a882a640688f4aa7fe2ee786bf18676e22f31781b9d10206d07154d5	Client	2024-03-10 14:45:15.706488	Individual
325	oceanview@gmail.com\t	935f6c5fa6fd6b3babf7a1f9cafaa330d1b0920bfd87d80f422b5a2aa5921e29	Client	2024-03-10 14:49:23.981581	Organization
327	david.ngigi@gmail.com	5d499a6b2dc9d5cb3382c64df517a992b8bbdf4bcbd015d91b1dbbe4cb7c82b2	Client	2024-03-10 14:49:28.08283	Individual
329	info@moiprimaryschool.ke 	bc68439b1a85fe76832cd1f5af08429b063896d9610a1f4be9326e201dc438a3	Client	2024-03-10 15:06:45.681398	Organization
331	info@kibraprimaryschool.ke	fc28692842cdd2c434129645e1c1d79c0931bf4e41a2fed5f5ff889626d90d82	Client	2024-03-10 15:08:30.094848	Organization
333	info@kisumucentralprimaryschool.ke	5287d9f1e65263bd82843c78d84368003b646036cf881e235abf3e15bb256e49	Client	2024-03-10 15:09:40.969324	Organization
335	info@nakurueastprimaryschool.ke	b0d7e173780fcec20f4f134f89c2fc6744b36ad56d33c79fc7215a273842c647	Client	2024-03-10 15:10:45.088135	Organization
337	info@eldorettownshipprimaryschool.ke	1e04f10ab611063ee2ec67c7bbced033a965fe51e0e86b45333df8bfd1f56e20	Client	2024-03-10 15:12:09.758417	Organization
339	info@thikaprimaryprimaryschool.ke	3460794b81350e07586d9e39a4d33931ee242a8f7cc1dbd9ae9d285130e56476	Client	2024-03-10 15:13:32.85035	Organization
341	info@nyeritownshipprimaryschool.ke	1bdde0643cbfcf10e56a31b0111b5b23ecbcaf89df17798d1f7add8eec9d94d7	Client	2024-03-10 15:14:50.359296	Organization
343	 johnkipchoge@gmail.com	e66860546f18cdbbcd86b35e18b525bffc67f772c650cedfe3ff7a0026fa1dee	Client	2024-03-10 15:22:25.020207	Individual
345	marywafula@yahoo.com	695bed4b7075ac1e441bf9925da7c7701bbfed9b82fa7f10628954128a381666	Client	2024-03-10 15:23:13.62351	Individual
347	 peterkimani@gmail.com	b51f40b9138fc522f3995a3ebd2ce08fab642233ae6039d3964abf22434ff4ac	Client	2024-03-10 15:24:19.577133	Individual
349	 janeachieng@yahoo.com	b03ddf3ca2e714a6548e7495e2a03f5e824eaac9837cd7f159c67b90fb4b7342	Client	2024-03-10 15:25:44.197065	Individual
351	 davidmutisya@gmail.com	3c89979fe44a785ee3912c3f8186ce714edc051e56f3586697668e2ab1efd925	Client	2024-03-10 15:26:36.565554	Individual
353	gracemuthoni@yahoo.com	64851b6779771362ebc52c67d082039ceda2e2d58827274bdda738056252b191	Client	2024-03-10 15:27:21.579923	Individual
355	patrickkiprop@gmail.com	97c94ebe5d767a353b77f3c0ce2d429741f2e8c99473c3c150e2faa3d14c9da6	Client	2024-03-10 15:28:10.055419	Individual
357	mercyatieno@yahoo.com	c9464bb3453762747fefca7742ef04b3588e3e60ab6f432e7379a42ecbb49bd3	Client	2024-03-10 15:28:50.385275	Individual
359	 samuelndungu@gmail.com	373732890407b247ea37d4fb1ffdb923448e91c1a5e0d5ecaa6184b4e28e85df	Client	2024-03-10 15:29:39.372753	Individual
363	carolinewambui@yahoo.com	6f661979b5169ed25b16b7ad657e288a32b71fb74be5ef4e193943d3cfcbe3c3	Client	2024-03-10 15:30:43.375652	Individual
365	chelseakibet@gmail.com	e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855	Dealer	2024-03-10 15:55:57.998396	Author
367	adrianomondi@gmail.com	e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855	Dealer	2024-03-10 15:57:15.848009	Author
369	kevinwanjohi@gmail.com	e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855	Dealer	2024-03-10 15:58:29.611956	Author
375	tracykamau@gmail.com	e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855	Dealer	2024-03-10 16:00:28.443447	Author
377	brianochieng@gmail.com	e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855	Dealer	2024-03-10 16:01:39.054594	Author
379	duncanchege@gmail.com	e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855	Dealer	2024-03-10 16:04:16.928043	Author
381	faithmwende@gmail.com	e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855	Dealer	2024-03-10 16:05:15.54403	Author
383	kelvinotieno@gmail.com	e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855	Dealer	2024-03-10 16:06:04.42673	Author
385	melissaatieno@gmail.com	e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855	Dealer	2024-03-10 16:06:58.719127	Author
387	tiffahnick012@gmail.com	455ab8a7375978f0cf5711387f3ba43c8732155912bafa4931b1de865917c098	Dealer	2024-03-10 16:23:23.336356	Author
389	info@nairobipublications.com	455ab8a7375978f0cf5711387f3ba43c8732155912bafa4931b1de865917c098	Dealer	2024-03-10 16:23:28.305487	Publisher
391	info@nairobipublications.com	455ab8a7375978f0cf5711387f3ba43c8732155912bafa4931b1de865917c098	Dealer	2024-03-10 16:24:32.669337	Publisher
393	info@kenyabooks.co.ke	1a783ec6cead594c6ca7497688e71e032cc86508c1ace8c245b050145fc39e09	Dealer	2024-03-10 16:25:54.997715	Publisher
395	info@mombasapublishers.com	84be38c91f0c418713ecf05e47ac2ec045058662a18bfc9b0f16c27d673e3345	Dealer	2024-03-10 16:27:01.36188	Publisher
399	info@riftvalleybooks.com	1950968c4febecdcac37126fc11c4e33af845ca74076280c86b838fa4baa6012	Dealer	2024-03-10 16:28:20.365207	Publisher
401	info@coastlinepublications.com	1f8a6063a59f816ca73f18bc257bb5aea81fea9bf40e9bd569729c26591468c2	Dealer	2024-03-10 16:29:23.655982	Publisher
403	info@greatlakespublishers.com	39e46dc4efbaf930a7dd57aa8263bf20550556d65ea20e0f59156f42b502ad71	Dealer	2024-03-10 16:30:16.936123	Publisher
405	info@centralbooks.com	0911cb22c6d132ef17aad41c781464f71011dd52e7a5d1654970c336b063278d	Dealer	2024-03-10 16:31:14.714206	Publisher
407	info@highlandspublishers.com	898b0842904741dbdc07f7897b4466a4a2bcbed2d1741ac7ba919159df497cb4	Dealer	2024-03-10 16:33:41.070747	Publisher
409	info@westernbooks.com	898b0842904741dbdc07f7897b4466a4a2bcbed2d1741ac7ba919159df497cb4	Dealer	2024-03-10 16:34:43.028535	Publisher
411	info@lakefrontpublications.com	4f9e90e7b304217ec7fa931fffd136660176a661409bda52b08ca87baeb7270d	Dealer	2024-03-10 16:35:50.76646	Publisher
413	info@techsupplies.com	ec473446cee19edbe3f5a561c3c70d47934cdf78e45c8a4db0e469214ec1e4db	Dealer	2024-03-10 16:38:14.358123	Manufacturer
415	info@paperworld.com	ec473446cee19edbe3f5a561c3c70d47934cdf78e45c8a4db0e469214ec1e4db	Dealer	2024-03-10 16:39:32.9745	Manufacturer
417	info@inkspot.com	fb6fc94565a318556f71602dcb52e8dca08d65a0e627651e710a37526e688ecc	Dealer	2024-03-10 16:40:51.521682	Manufacturer
419	info@pencilsandpens.com	989e63032098eb722de39d42f7b6e37ba37b7abbc50bfb20ba8694240bd243bd	Dealer	2024-03-10 16:42:29.206852	Manufacturer
421	info@booksplus.com	989e63032098eb722de39d42f7b6e37ba37b7abbc50bfb20ba8694240bd243bd	Dealer	2024-03-10 16:43:52.704525	Manufacturer
423	 info@printtech.com	80e3ec0521df98fcbb1580a140b6de0c69a420f88c6990b6e92a728ea0c79986	Dealer	2024-03-10 16:45:26.805901	Manufacturer
425	info@artcraft.com	6a0e7c0e8815c3d0b6428b05541054de295d2d94e4486ef5c0c596605b1ef18c	Dealer	2024-03-10 16:46:48.126793	Manufacturer
427	info@smartprint.com	8baea9beb6f13aab319095824fed8f0b4f3e6db65c2bebfe90d5c85c1eb47a7a	Dealer	2024-03-10 16:48:06.574897	Manufacturer
429	info@techink.com	68d84107de33d2d66281b3236fcdd2b57c82944017a07cab766a42e62d993782	Dealer	2024-03-10 16:49:14.21143	Manufacturer
431	info@craftmaster.com	fd2f44bd7208f33871f838b05426547a692943eda30e7eb78edcd78c035ff8cd	Dealer	2024-03-10 16:50:25.224614	Manufacturer
433	melissakent@gmail.com	8aa13eb636b40611dfc10d4b378a1caf3281a73ec1186c3c4b472d449cc2d68a	Dealer	2024-03-12 16:20:07.945455	Author
435	GLusenaka@gmail.com	97a714f2cce952bded92c251b5934c958260ff2afdf405f1f673e390859037e2	Client	2024-03-13 16:56:37.962442	Individual
438	beisymish56@gmail.com	ed56a320befa6e3c2667379337a079c0be04e7767592416e2a70d1f11824abc3	Client	2024-03-14 21:25:54.410424	Individual
437	admin@gmail.com	ed56a320befa6e3c2667379337a079c0be04e7767592416e2a70d1f11824abc3	Admin	2024-03-14 21:18:53.972517	Admin
\.


--
-- Name: author_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.author_id_seq', 25, true);


--
-- Name: books_bookid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.books_bookid_seq', 9, true);


--
-- Name: client_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.client_id_seq', 17, true);


--
-- Name: manufacturer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.manufacturer_id_seq', 3, true);


--
-- Name: order_sequence; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.order_sequence', 43, true);


--
-- Name: pksequence; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pksequence', 439, true);


--
-- Name: publisher_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.publisher_id_seq', 5, true);


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_id_seq', 104, true);


--
-- Name: authors authors_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.authors
    ADD CONSTRAINT authors_email_key UNIQUE (email);


--
-- Name: authors authors_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.authors
    ADD CONSTRAINT authors_pkey PRIMARY KEY (author_id);


--
-- Name: books books_isbn_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.books
    ADD CONSTRAINT books_isbn_key UNIQUE (isbn);


--
-- Name: books books_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.books
    ADD CONSTRAINT books_pkey PRIMARY KEY (bookid);


--
-- Name: clients clients_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (client_id);


--
-- Name: manufacturers manufacturers_contact_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.manufacturers
    ADD CONSTRAINT manufacturers_contact_email_key UNIQUE (contact_email);


--
-- Name: manufacturers manufacturers_manufacturer_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.manufacturers
    ADD CONSTRAINT manufacturers_manufacturer_email_key UNIQUE (manufacturer_email);


--
-- Name: manufacturers manufacturers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.manufacturers
    ADD CONSTRAINT manufacturers_pkey PRIMARY KEY (manufacturer_id);


--
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (order_id);


--
-- Name: publishers publishers_contact_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publishers
    ADD CONSTRAINT publishers_contact_email_key UNIQUE (contact_email);


--
-- Name: publishers publishers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publishers
    ADD CONSTRAINT publishers_pkey PRIMARY KEY (publisher_id);


--
-- Name: publishers publishers_publisher_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publishers
    ADD CONSTRAINT publishers_publisher_email_key UNIQUE (publisher_email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (user_id);


--
-- Name: authors authors_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.authors
    ADD CONSTRAINT authors_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON DELETE CASCADE;


--
-- Name: clients clients_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON DELETE CASCADE;


--
-- Name: orders fk_client; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT fk_client FOREIGN KEY (client_id) REFERENCES public.clients(client_id);


--
-- Name: books fk_sellerid; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.books
    ADD CONSTRAINT fk_sellerid FOREIGN KEY (seller_id) REFERENCES public.users(user_id);


--
-- Name: manufacturers manufacturers_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.manufacturers
    ADD CONSTRAINT manufacturers_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON DELETE CASCADE;


--
-- Name: publishers publishers_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.publishers
    ADD CONSTRAINT publishers_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

