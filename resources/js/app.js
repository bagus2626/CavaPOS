import $ from "jquery";
window.$ = $;
window.jQuery = $;

import "./bootstrap";
import "./echo";
import Alpine from "alpinejs";
import AOS from "aos";
import "aos/dist/aos.css";

AOS.init({
    once: true, // animasi hanya jalan sekali
});

window.Alpine = Alpine;

// Dark Mode Toggle using Alpine
document.addEventListener("alpine:init", () => {
    Alpine.store("darkMode", {
        on:
            localStorage.theme === "dark" ||
            (!("theme" in localStorage) &&
                window.matchMedia("(prefers-color-scheme: dark)").matches),

        toggle() {
            this.on = !this.on;
            localStorage.theme = this.on ? "dark" : "light";
            document.documentElement.classList.toggle("dark", this.on);
        },

        init() {
            document.documentElement.classList.toggle("dark", this.on);
        },
    });
});
// Alpine.store('darkMode').init();

Alpine.start();
