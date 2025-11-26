const preloadLoaderImage = new Image();
preloadLoaderImage.src = "/images/cava-logo2.png";

function showPageLoader(message = "Please wait...") {
    $.blockUI({
        message: `
            <div class="blockui-wrapper text-center">
                <img src="/images/cava-logo2.png" alt="Loading" class="loading-logo"/>
                <h5 class="loader-text">${message}</h5>
            </div>
        `,
        css: {
            border: "none",
            backgroundColor: "transparent",
            color: "#fff",
            top: "40%",
            left: "50%",
            transform: "translate(-50%, -50%)",
            width: "auto",
            zIndex: 99999
        },
        overlayCSS: {
            backgroundColor: "#000",
            opacity: 0.6,
            cursor: "wait",
            zIndex: 99998
        }
    });
}

function hidePageLoader() {
    $.unblockUI();
}
