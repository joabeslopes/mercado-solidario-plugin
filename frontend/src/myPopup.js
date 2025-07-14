const popupcss = `
        .popup-title {
          font-size: 30px;
        }
        .popup-body p {
          font-size: 20px;
        }
    `;

export default function showPopup(title, message){

    new Popup({
        id: "my-popup",
        title: title,
        content: message,
        showImmediately: true,
        hideCallback: () => {
            document.querySelectorAll(".my-popup").forEach(e => e.remove());
        },
        css: popupcss
    });

};