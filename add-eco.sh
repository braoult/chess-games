#!/usr/bin/env bash
#
# add-eco.sh - add ECO and openings to a PGN file.
#
# Usage: add-eco.sh [-e ecofile] [fromdir] [todir]
#        add-eco.sh [-e ecofile] [fromdir]

CMD="${0##*/}"

PATH=$PATH:./pgn-extract

force=f                                           # dest file overwrite
ecodir=./eco
ecofile=eco.pgn
from=./pgn-orig
to=./pgn

usage() {
    printf "Usage: %s [-ef] from-dir to-dir\n" "$CMD"
    return 0
}

while getopts "e:fh" todo; do
    case "$todo" in
        e)
            ecofile="$OPTARG"
            ;;
        f)
            force=t
            ;;
        h)
            usage
            exit 0
            ;;
        *)
            usage
            ;;
    esac
done

# Now check remaining arguments
shift $((OPTIND - 1))

(( $# > 2 )) && usage && exit 1
(( $# > 0 )) && from="$1"
(( $# > 1 )) && to="$2"
echo "from=$from to=$to eco=$ecodir/$ecofile"

if ! [[ -d "$from" ]]; then
    printf "%s: Source directory missing. Exiting.\n" "$from"
    usage
    exit 1
elif ! [[ -d "$to" ]]; then
    printf "%s: Destination directory missing. Exiting.\n" "$to"
    usage
    exit 1
fi

for if in "$from"/*.pgn; do
    fn=$(basename "$if")
    of="$to/$fn"
    if [[ -e $of && $force != t ]]; then
        printf "Skipping existing destination file %s already exists, skipping...\n" "$of"
        continue
    else
        printf "Converting %s to %s\n" "$if" "$of"
        pgn-extract --allownullmoves -e"$ecodir/$ecofile" "$if" -o"$of"
    fi
done
