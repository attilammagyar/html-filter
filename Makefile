COMPOSER = $(shell which composer)
DOXYGEN = $(shell which doxygen)

DOCS_DIR ?= docs
REPORTS_DIR = $(DOCS_DIR)/reports
APIDOCS_DIR = $(DOCS_DIR)/api


.PHONY: all
all: coverage docs
	build/generate_build_docs.sh >$(DOCS_DIR)/index.html


.PHONY: bootstrap
bootstrap:
	$(COMPOSER) install
	[ -d $(DOCS_DIR) ] || mkdir $(DOCS_DIR)
	[ -d $(REPORTS_DIR) ] || mkdir $(REPORTS_DIR)
	[ -d $(APIDOCS_DIR) ] || mkdir $(APIDOCS_DIR)

.PHONY: phpunitconfig
phpunitconfig:
	[ -f phpunit.xml ] || cp phpunit.xml.dist phpunit.xml

.PHONY: check
check: bootstrap phpunitconfig
	./vendor/bin/phpunit --configuration=phpunit.xml \
			--testdox-html $(REPORTS_DIR)/phpunit.html

.PHONY: coverage
coverage: bootstrap phpunitconfig
	./vendor/bin/phpunit --configuration=phpunit.xml \
			--coverage-clover $(REPORTS_DIR)/phpunit_clover.xml \
			--coverage-html $(REPORTS_DIR)/phpunit_coverage \
			--testdox-html $(REPORTS_DIR)/phpunit.html \
			--coverage-text \
		| grep -v '^\(\\\|\(  Methods: .*Lines: \)\)' \
		| tee $(REPORTS_DIR)/phpunit_coverage.txt

.PHONY: docs
docs: bootstrap
	$(DOXYGEN) Doxyfile
