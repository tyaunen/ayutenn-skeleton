modal = {
    open(modal) {
        if (modal == null) return;
        modal.classList.add("active");
    },
    close(modal) {
        if (modal == null) return;
        modal.classList.remove("active");
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // モーダルを開くボタンを取得
    const openModalButtons = document.querySelectorAll("[data-modal-target]");
    // モーダルを閉じるボタンを取得
    const closeModalButtons = document.querySelectorAll("[data-close-button]");
    // モーダルオーバーレイを取得
    const overlay = document.querySelectorAll(".modal");

    // モーダルを開くイベント
    openModalButtons.forEach(button => {
        button.addEventListener("click", () => {
            const targetModal = document.querySelector(button.dataset.modalTarget);
            modal.open(targetModal);
        });
    });

    // モーダルを閉じるイベント
    closeModalButtons.forEach(button => {
        button.addEventListener("click", () => {
            const targetModal = button.closest(".modal");
            modal.close(targetModal);
        });
    });

    // 背景クリックでモーダルを閉じる
    overlay.forEach(overlay => {
        overlay.addEventListener("click", (event) => {
            if (event.target === overlay) { // 背景のみクリック時に閉じる
                modal.close(overlay);
            }
        });
    });
});
