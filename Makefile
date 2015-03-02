# Get root dir of project -- only works in GNU :(
#PROJECT_DIR := $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))

PROJECT_DIR := .
LOG_DIR := $(PROJECT_DIR)/log

all:
	env php --version
	make lint
	make phpcs
#	make phpmd
#	make unittest

unittest:
	$(PROJECT_DIR)/vendor/bin/phpunit                         \
		--configuration   $(PROJECT_DIR)/unit/phpunit.xml \
		--log-junit       $(LOG_DIR)/unittest_report.xml  \
		--coverage-html   $(LOG_DIR)/unittest_coverage    \
		--coverage-clover $(LOG_DIR)/unittest_coverage.xml

phpmd:
#	$(PROJECT_DIR)/vendor/bin/phpmd $(PROJECT_DIR)/src,$(PROJECT_DIR)/unit text codesize,cleancode
	$(PROJECT_DIR)/vendor/bin/phpmd $(PROJECT_DIR)/src text codesize,cleancode

phpcs:
#	$(PROJECT_DIR)/vendor/bin/phpcs --standard=PSR1 $(PROJECT_DIR)/src $(PROJECT_DIR)/unit
#	$(PROJECT_DIR)/vendor/bin/phpcs --standard=PSR2 $(PROJECT_DIR)/src $(PROJECT_DIR)/unit
	$(PROJECT_DIR)/vendor/bin/phpcs --standard=PSR1 $(PROJECT_DIR)/src
	$(PROJECT_DIR)/vendor/bin/phpcs --standard=PSR2 $(PROJECT_DIR)/src

lint:
#	$(PROJECT_DIR)/vendor/bin/parallel-lint $(PROJECT_DIR)/src $(PROJECT_DIR)/unit
	$(PROJECT_DIR)/vendor/bin/parallel-lint $(PROJECT_DIR)/src

clean:
	rm -r $(LOG_DIR)/*
