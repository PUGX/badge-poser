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
