#创建数据库
drop database if exists DistributerManager;
create database DistributerManager;
use DistributerManager;

#创建ci的session表
create table ci_sessions (
  session_id char(40) default '0' not null,
  ip_address char(45) default '0' not null,
  user_agent char(120) not null,
  last_activity int(10) unsigned default 0 not null,
  user_data text default '' not null,
  primary key (session_id),
  key `last_activity_idx` (`last_activity`)
);

#创建用户表
create table t_user(
	userId integer not null auto_increment,
	name varchar(32) not null,
	password char(48) not null,
	company varchar(128) not null,
	phone varchar(11) not null,
	type integer not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user add index nameIndex(name,password);

#创建用户权限表
create table t_user_permission(
	userPermissionId integer not null auto_increment,
	userId integer not null,
	permissionId integer not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userPermissionId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user_permission add index userIdIndex(userId);

#创建代理商的客户表
create table t_user_client(
	userClientId integer not null auto_increment,
	userId integer not null,
	clientUserId integer not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userClientId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user_client add index userIdIndex(userId);

#创建公司文章模板表
create table t_company_template(
	companyTemplateId integer not null auto_increment,
	title varchar(128) not null,
	url varchar(256) not null,
	remark varchar(256) not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( companyTemplateId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

#创建公司文章模板分类表
create table t_company_template_classify(
	companyTemplateClassifyId integer not null auto_increment,
	companyTemplateId integer not null,
	title varchar(128) not null,
	remark varchar(256) not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( companyTemplateClassifyId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_company_template add index companyTemplateIdIndex(companyTemplateId);

#创建用户公司模板表
create table t_user_company_template(
	userCompanyTemplateId integer not null auto_increment,
	userId integer not null,
	companyTemplateId integer not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userCompanyTemplateId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user_company_template add index userIdIndex(userId);

#创建用户公司文章表
create table t_user_company_article(
	userCompanyArticleId integer not null auto_increment,
	userId integer not null,
	title varchar(128) not null,
	remark varchar(256) not null,
	content text not null,
	companyTemplateClassifyId integer not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userCompanyArticleId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user_company_article add index userIdIndex(userId);

#创建用户公司广告表
create table t_user_company_banner(
	userCompanyBannerId integer not null auto_increment,
	userId integer not null,
	sort integer not null,
	image varchar(256) not null,
	url varchar(256) not null,
	remark varchar(256) not null,
	createTime timestamp not null default CURRENT_TIMESTAMP,
	modifyTime timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
	primary key( userCompanyBannerId )
)engine=innodb default charset=utf8mb4 auto_increment = 10001;

alter table t_user_company_banner add index userIdIndex(userId);

#建立初始数据
insert into t_user(userId,name,password,company,phone,type) values
(10001,"fish",SHA1("123456"),'烘焙帮信息科技有限公司','15018749403',1),
(10002,"fish2",SHA1("123456"),'烘焙帮信息科技有限公司','15018749403',1);

#显示初始数据
select * from t_user;
select * from t_user_permission;
select * from t_user_client;
select * from t_company_template;
select * from t_company_template_classify;
select * from t_user_company_template;
select * from t_user_company_article;
select * from t_user_company_banner;