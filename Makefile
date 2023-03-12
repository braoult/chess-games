
SHELL       := /bin/bash
export PATH := $(PATH):./pgn-extract:.

PGN_EXTRACT := pgn-extract

ORIGDIR     := ./pgn-orig
UPDDIR      := ./pgn
DSTDIR      := $(HOME)/dev/www/com.raoult/devs/chess/pgn/
ECODIR      := ./eco
SCIDECOFILE := $(ECODIR)/eco-scid-orig.pgn
ECOFILE     := $(ECODIR)/eco.pgn

ECOFILE     := $(ECODIR)/eco.pgn
ORIG_PGN    := $(wildcard $(ORIGDIR)/*.pgn)
UPD_PGN     := $(patsubst $(ORIGDIR)%,$(UPDDIR)%,$(ORIG_PGN))
DST_PGN     := $(patsubst $(ORIGDIR)%,$(DSTDIR)%,$(ORIG_PGN))
UPD_BR      := $(UPDDIR)/br.pgn
DST_BR      := $(patsubst $(UPDDIR)%,$(DSTDIR)%,$(UPD_BR))

.PHONY: all eco ecofile sync

all: ecofile $(DST_PGN) $(DST_BR) sync

eco: $(UPD_PGN)

ecofile: $(ECOFILE)

$(ECOFILE): $(SCIDECOFILE)
	echo updating pgnparser eco file
	eco-scid2pgnparser.sh -ef $(SCIDECOFILE) $(ECOFILE)

$(UPD_BR): $(UPD_PGN)
	@echo "building $@... "
	@build-br-from-parts.php
	@echo done

$(DSTDIR)/%: $(UPDDIR)/%
	@echo -n "Copying $< to $(DSTDIR)... "
	@cp -p $< $@
	@echo done.

$(UPDDIR)/%: $(ORIGDIR)/% $(ECOFILE) | $(UPDDIR)
	@echo Adding ECO to $< "->" $@
	@echo $(PGN_EXTRACT) --allownullmoves -e$(ECOFILE) $< -o$@
	@$(PGN_EXTRACT) --allownullmoves -e$(ECOFILE) $< -o$@

$(UPDDIR):
	@echo -n "Creating $@ directory... "
	@mkdir $@
	@echo done.

sync:
	@echo -n "Sending files to idril... "
	@sync-www-to-idril.sh -d > /dev/null
	@echo done.
#cp -p pgn/minis-tournament.pgn ~/dev/www/com.raoult/devs/chess/pgn/
