#!/usr/bin/env bash
#
# eco-scid2pgnparser.sh - prepare a better (?) eco file for pgn-extract.
#
# Usage: eco-scid2pgnparser.sh from.pgn to.pgn
#
# Convert scid eco file (converted with scid's eco2pgn.py) to a file suitable for
# pgn-extract by removing the [Result] lines and replacing [Variation] with
# [Opening].
# By default, scid ECO codes are kept (they contain additional letters, such as
# A00a), the "-e" option will remove these additional letters.

CMD="${0##*/}"

force=f                                           # dest file overwrite
pureeco=f                                         # convert scid ECO to correct ones

usage() {
    printf "Usage: %s [-ef] from-pgn-file to-pgn-file\n" "$CMD"
    return 0
}

while getopts efh todo; do
    case "$todo" in
        e)
            pureeco=t
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

(( $# != 2 )) && usage && exit 1

from="$1"
to="$2"

if [[ ! -e "$from" ]]; then
    printf "%s: No such file or directory. Exiting.\n" "$from"
    usage
    exit 1
fi
if [[ -e "$to" && ! "$force" == t ]]; then
    printf "%s exists and no -f flag. Exiting.\n" "$to"
    usage
    exit 1
fi

declare -a purge=()

if [[ $pureeco == "t" ]]; then
    purge=(-e "s/\"([A-Z][0-9]{2}).\"/\"\1\"/")
fi

sed -E -e "/^\[Result/d" -e "s/^\[Variation/\[Opening/" "${purge[@]}" < "$from" > "$to"
