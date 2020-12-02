export function makeElement(htmlString) {
    let div = document.createElement("div");
    div.innerHTML = htmlString.trim();

    return div.firstChild;
}

export function removeChildren(node){
    while( node.firstChild ) {
        node.removeChild( node.firstChild );
    }
}

export function makeCopyAllBadgesButtonElement(badgesAsMarkdown) {
    let div = document.createElement("div");
    div.innerHTML = `<button className="big" id="all-badges" data-clipboard-text="`+ badgesAsMarkdown.trim() + `">
        Copy All Badges
    </button>`;

    return div.firstChild;
}
