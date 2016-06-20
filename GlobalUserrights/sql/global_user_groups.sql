CREATE TABLE /*_*/global_user_groups (
  gug_user INT UNSIGNED PRIMARY KEY NOT NULL,
  gug_group VARBINARY(255) NOT NULL,
) /*$wgDBTableOptions*/;
