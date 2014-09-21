CREATE TABLE IF NOT EXISTS bnt_news (
  news_id integer NOT NULL DEFAULT nextval('bnt_news_news_id_seq'),
  headline character varying(100) NOT NULL,
  newstext text,
  user_id integer DEFAULT NULL,
  "date" timestamp without time zone DEFAULT NULL,
  news_type character varying(10) DEFAULT NULL,
  PRIMARY KEY (news_id)
);
