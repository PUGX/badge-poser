import Clipboard from "clipboard";
import tippy from "tippy.js";

const clipboard = new Clipboard("button");

clipboard.on("success", (e) => {
  const instance = tippy(e.trigger, {
    content: "Copied!",
    placement: "top",
    showOnCreate: true,
    trigger: "manual",
    theme: "translucent",
  });

  setTimeout(instance.destroy, 2000);
});
