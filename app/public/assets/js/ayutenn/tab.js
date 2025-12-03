document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(targetTabButton => {
        targetTabButton.addEventListener('click', () => {
            const group = targetTabButton.getAttribute('data-tab-group');
            const targetId = targetTabButton.getAttribute('data-tab-target');

            // 同じグループのタブとコンテンツをすべて非アクティブ化
            document.querySelectorAll(`.tab[data-tab-group="${group}"]`).forEach(t => t.classList.remove('active'));
            document.querySelectorAll(`.tab-content[data-tab-group="${group}"]`).forEach(content => content.classList.remove('show'));

            // クリックされたタブとそのコンテンツを表示
            targetTabButton.classList.add('active');
            document.querySelector(`.tab-content[data-tab-group="${group}"][data-tab-id="${targetId}"]`).classList.add('show');
        });
    });
});
