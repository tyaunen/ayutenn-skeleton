accordion = {
    close(ele){
        ele.style.transition = 'none';
        ele.style.height = ele.scrollHeight + 'px';
        ele.style.maxHeight = ele.scrollHeight + 'px';

        requestAnimationFrame(() => {
            ele.style.transition = 'all 0.3s';
            ele.style.height = "0px";
            ele.style.maxHeight = "";
            ele.classList.remove("open");
        });
    },
    open(ele){
        ele.style.maxHeight = ele.scrollHeight + 'px';
        ele.style.height = ele.scrollHeight + 'px';
        setTimeout(() => {
            ele.classList.add("open");
            ele.style.height = "auto";
            ele.style.maxHeight = "";
        }, 300);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // アコーディオンを開閉するイベントたち
    const accordionButtons = document.querySelectorAll("[data-accordion-target]");
    accordionButtons.forEach(button => {
        button.addEventListener("click", () => {
            const targetAccordion = document.querySelector(button.dataset.accordionTarget);

            // ターゲットのアコーディオンを閉じる
            if (targetAccordion.classList.contains('open')) {
                accordion.close(targetAccordion);
            } else {
                accordion.open(targetAccordion);
            }

            // グループの設定があったら、他のアコーディオンをすべて閉じる
            if (targetAccordion.hasAttribute('data-accordion-group')) {
                const group = document.querySelectorAll(`[data-accordion-group="${targetAccordion.dataset.accordionGroup}"]`);
                group.forEach(ele => {
                    if(targetAccordion.dataset.accordionId !== ele.dataset.accordionId){
                        if (targetAccordion.classList.contains('open')) {
                            accordion.close(ele);
                        }
                    }
                });

            }
        });
    });
});