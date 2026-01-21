import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

const csrf = document
  .querySelector('meta[name="csrf-token"]')
  ?.getAttribute("content");

window.Echo = new Echo({
  broadcaster: "pusher",
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "ap1",
  wsHost:
    import.meta.env.VITE_PUSHER_HOST ||
    `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
  wsPort: Number(import.meta.env.VITE_PUSHER_PORT || 80),
  wssPort: Number(import.meta.env.VITE_PUSHER_PORT || 443),
  forceTLS: (import.meta.env.VITE_PUSHER_SCHEME || "https") === "https",
  enabledTransports: ["ws", "wss"],

  // ⬇️ kunci: jangan pakai authEndpoint lama; pakai authorizer ini
  authorizer: (channel) => ({
    authorize(socketId, callback) {
      fetch("/broadcasting/auth", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          "X-CSRF-TOKEN": csrf,
        },
        credentials: "include", // wajib agar cookie guard employee ikut
        body: new URLSearchParams({
          socket_id: socketId,
          channel_name: channel.name, // ex: "private-partner.6.orders"
        }),
      })
        .then(async (r) => {
          const txt = await r.text();
          if (!r.ok)
            return callback(true, new Error(`Auth ${r.status}: ${txt || ""}`));
          callback(false, JSON.parse(txt)); // { auth: "key:signature" }
        })
        .catch((err) => callback(true, err));
    },
  }),
});

// opsional: beri sinyal siap

window.dispatchEvent(new Event("echo:ready"));
