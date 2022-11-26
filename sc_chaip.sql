CREATE TABLE `sc_chaip_mark` (
  `id` int(11) AUTO_INCREMENT COMMENT '序号',
  PRIMARY KEY (`id`)
) COMMENT = '简陋·查ip工具·鱼钩序号';

CREATE TABLE `sc_chaip_cool` (
  `ip` VARCHAR(50) COMMENT '使用ip',
  `datetime` DATETIME COMMENT '冷却日期',
  PRIMARY KEY (`ip`)
) COMMENT = '简陋·查ip工具·冷却ip列表';

CREATE TABLE `sc_chaip_data` (
  `mark_0` bigint(20) UNSIGNED COMMENT '哈希标记的前半部分',
  `mark_1` bigint(20) UNSIGNED COMMENT '哈希标记的后半部分',
  `datetime` DATETIME COMMENT '上钩时间',
  `via` VARCHAR(200),
  `remote_addr` VARCHAR(50),
  `x_forwarded_for` VARCHAR(200),
  `user_agent` VARCHAR(200),
  PRIMARY KEY (`mark_0`, `mark_1`, `datetime`)
) COMMENT = '简陋·查ip工具·访问数据表';