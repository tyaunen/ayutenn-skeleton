document.addEventListener('DOMContentLoaded', () => {
    // 全ての .popover 内の .link にクリックイベントを設定
    document.querySelectorAll('.popover .link').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.stopPropagation();
            const container = this.closest('.popover');
            container.classList.toggle('active');
        });
    });

    // ドキュメント全体でクリックを監視して、他のポップオーバーを閉じる
    document.addEventListener('click', function (e) {
        document.querySelectorAll('.popover').forEach(function (container) {
            if (!container.contains(e.target)) {
                container.classList.remove('active');
            }
        });
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const containers = document.querySelectorAll('.tooltip-container');

    containers.forEach(container => {
        const tooltip = container.querySelector('.tooltip');
        let touchTimeout;

        // タップイベントの処理
        container.addEventListener('touchstart', function(e) {
            // 長押し検出用のタイマー
            touchTimeout = setTimeout(() => {
                tooltip.style.visibility = 'visible';
                tooltip.style.opacity = '1';
            }, 200); // 200ms の長押しで表示
        });

        container.addEventListener('touchend', function(e) {
            clearTimeout(touchTimeout);
            // タップ終了後、少し待ってから非表示
            setTimeout(() => {
                tooltip.style.visibility = 'hidden';
                tooltip.style.opacity = '0';
            }, 1000);
        });

        // タッチムーブのキャンセル
        container.addEventListener('touchmove', function(e) {
            clearTimeout(touchTimeout);
            tooltip.style.visibility = 'hidden';
            tooltip.style.opacity = '0';
        });
    });


    const images = document.querySelectorAll('[data-info-text]');

    images.forEach(img => {
        // 既にラップされている場合はスキップ
        if (img.parentElement.classList.contains('tooltip-container')) return;

        const container = document.createElement('div');
        container.className = 'tooltip-container';

        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = img.dataset.infoText;

        img.parentNode.insertBefore(container, img);
        container.appendChild(img);
        container.appendChild(tooltip);
    });
});
