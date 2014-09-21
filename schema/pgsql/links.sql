CREATE TABLE IF NOT EXISTS bnt_links (
  link_id integer NOT NULL DEFAULT nextval('bnt_links_link_id_seq'),
  link_start integer NOT NULL DEFAULT '0',
  link_dest integer NOT NULL DEFAULT '0',
  PRIMARY KEY (link_id)
);
