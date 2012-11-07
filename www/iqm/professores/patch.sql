CREATE TABLE `iqm2010`.`frequencia` (
  `id_frequencia` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pos_graduacao` INTEGER UNSIGNED NOT NULL,
  `data_inicio` DATE NOT NULL,
  `data_fim` DATE NOT NULL,
  `ok` TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id_frequencia`)
)
ENGINE = InnoDB;