// resources/js/echo.js
import Echo from "laravel-echo";
import axios from "axios";
window.Pusher = (await import("pusher-js")).default;

axios.defaults.withCredentials = true;

const token = document.querySelector('meta[name="csrf-token"]')?.content;

window.Echo = new Echo({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: Number(import.meta.env.VITE_REVERB_PORT),
    wssPort: Number(import.meta.env.VITE_REVERB_PORT),
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === "https",
    enabledTransports: ["ws", "wss"],
    authorizer: (channel) => ({
        authorize: (socketId, callback) => {
            axios
                .post(
                    "/broadcasting/auth",
                    { socket_id: socketId, channel_name: channel.name },
                    {
                        headers: {
                            "X-CSRF-TOKEN": token,
                            "X-Requested-With": "XMLHttpRequest",
                        },
                        withCredentials: true,
                    }
                )
                .then((res) => callback(false, res.data))
                .catch((err) => {
                    console.error(
                        "Broadcast auth error:",
                        err?.response?.status,
                        err?.response?.data || err
                    );
                    callback(true, err);
                });
        },
    }),
});

// INFO: log koneksi & subscription dari sisi client
try {
    const p = window.Echo.connector.pusher;
    p.connection.bind("connected", () => console.log("[Echo] connected"));
    p.connection.bind("error", (e) => console.warn("[Echo] conn error", e));
} catch (_) {}

window.dispatchEvent(new Event("echo:ready")); // ← penting, ditunggu oleh cashier.js
console.log("[Echo] ready");
