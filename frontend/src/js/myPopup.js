const popupcss = `
      .popup-title {
        font-size: 30px;
      }
      .popup-body {
        max-height: 60vh;
        overflow: scroll;
      }
      .popup-body p {
        font-size: 20px;
        line-height: 1.4;
        white-space: pre-wrap;
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