import Raven from "raven-js";
import Awesomplete from "awesomplete";
import {debounce} from "./debounce";
import "./clipboard";
import {makeElement, removeChildren} from "./elements-utils";
import Promise from "promise-polyfill";
import "whatwg-fetch";

if (!window.Promise) {
    window.Promise = Promise;
}

if (window.globalVars.APP_DEBUG !== "1") {
    Raven
        .config("https://1435e86eef3d46c5a39525e9dd7a0dab@sentry.io/295017")
        .install();
}

function changePackage(packageName) {
    document.getElementById("package-name").innerText = packageName;
    document.getElementById("permalink").setAttribute("href", `/show/${packageName}`);
}

const renderBadge = ({img}) => `
    <img class="badge" src="${img}">`;

const renderBadgeContainer = ({label, img, name, markdown}) =>
    `<div class="col-12 col-md-6">
        <h4>${label}</h4>
        ${renderBadge({img})}
        <input
            class="badge-input"
            data-badge="${name}"
            readonly
            type="text"
            value="${markdown}" title="${label}">
        <button data-clipboard-target=".badge-input[data-badge='${name}']">Copy</button>
    </div>`;

function renderBadges(badges) {
    let featuredBadges = document.querySelector(".featured-badges");

    removeChildren(featuredBadges);
    badges.badges.filter((badge) => badge.featured).forEach((badge) => {
        featuredBadges.appendChild(makeElement(renderBadge(badge)));
    });

    let badgesContainer = document.getElementById("badges-container");
    removeChildren(badgesContainer);
    badges.badges.forEach((badge) => {
        badgesContainer.appendChild(makeElement(renderBadgeContainer(badge)));
    });
}

const searchInput = document.getElementById("search-package");
const awesomplete = new Awesomplete(searchInput, {
    minChars: 3,
    autoFirst: true,
    item: (item) => {
        const [title, description] = item.label;

        const node = document.createElement("li");
        node.classList.add("search-result");

        const titleEl = document.createElement("span");
        titleEl.classList.add("search-result--title");
        titleEl.textContent = title;

        const descriptionEl = document.createElement("span");
        descriptionEl.classList.add("search-result--description");
        descriptionEl.textContent = description;

        node.appendChild(titleEl);
        node.appendChild(descriptionEl);

        return node;
    },
});

const onInputChange = debounce(({target: target}) => {
    const {value} = target;
    if (value.length < 3) {
        return;
    }

    target.parentNode.classList.add("loading");

    fetch(`/search_packagist?name=${value}`)
        .then((response) => response.json())
        .then((data) => data.map((repository) => ({
            label: [repository.id, repository.description],
            value: repository.id
        })))
        .then((list) => {
            awesomplete.list = list;
        })
        .finally(() => {
            target.parentNode.classList.remove("loading");
        });
}, 250);

searchInput.addEventListener("input", onInputChange);

const readmeContainer = document.querySelector(".readme .content");
const dimmer = makeElement("<div class='dimmer'></div>");

searchInput.addEventListener("awesomplete-selectcomplete", function (e) {
    const {value: packageName} = e.text;

    readmeContainer.appendChild(dimmer)

    fetch(`/snippet/all/?repository=${packageName}`)
        .then((res) => {
            changePackage(packageName);
            res.json().then(renderBadges);
        })
        .finally(()=> {
            readmeContainer.removeChild(dimmer);
        });
}, false);
