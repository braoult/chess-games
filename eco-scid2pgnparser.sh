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
# A00a) with the tag "ECOs", and the official ECO code (without the additional
# letters) are set with the "ECO" tag.
#"the "-e" option will remove "ECOs" from the output.

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

#declare -a purge=()

#if [[ $pureeco == "t" ]]; then
#    purge=(-e "s/\"([A-Z][0-9]{2}).\"/\"\1\"/")
#fi

while read -r line; do
    if [[ "$line" =~ ^\[Result\ .* ]]; then
        #echo Result skipped.
        continue
    elif [[ "$line" =~ ^\[Variation\ .* ]]; then
        #echo -n "Variation "
        line="${line/#[Variation/[Opening}"
        #echo "$line"
    elif [[ $line =~ ^\[ECO\ .*([A-Z][0-9]{2}).*\] ]]; then
        eco=${BASH_REMATCH[1]}
        printf -v line1 "[ECO \"%s\"]" "$eco"
        if [[ $pureeco == t ]]; then
            printf -v line "%s" "$line1"
        else
            line2="${line/#[ECO/[ECOs}"
            printf -v line "%s\n%s" "$line1" "$line2"
        fi
    fi
    printf "%s\n" "$line"
done < "$from" > "$to"

#sed -E -e "/^\[Result/d" -e "s/^\[Variation/\[Opening/" "${purge[@]}" < "$from" > "$to"
