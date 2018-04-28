import Clipboard from "clipboard";
import Tooltip from "tooltip.js";

const clipboard = new Clipboard("button");

clipboard.on("success", (e) => {
    const instance = new Tooltip(e.trigger, {
        title: "Copied!",
        placement: "top",
        trigger: "manual",
    });
    instance.show();

    setTimeout(() => instance.dispose(), 2000);
});