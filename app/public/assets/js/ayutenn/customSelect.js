
function setValueToCustomSelect(selectId, value) {
    const selectValueElement = document.querySelector(`#${selectId}>input`);
    const selectOptionElement = document.querySelector(`#${selectId} .select-option[data-value=${value}]`);
    const selectHolderTextElement = document.querySelector(`#${selectId} .select-holder-text`);

    if(selectOptionElement){
        // 設定値を、holderにセット
        const html = selectOptionElement.innerHTML.trim();
        selectValueElement.value = value;
        selectHolderTextElement.innerHTML = html; // XSSには気をつけようね
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const customSelect = document.querySelectorAll('.custom-select');
    customSelect.forEach(select => {

        const selectHolder = select.querySelector('.select-holder');
        const selectHolderText = select.querySelector('.select-holder-content');
        const selectOptions = select.querySelector('.select-options');
        const options = selectOptions.querySelectorAll('div');
        const inputElement = select.querySelector('input');


        // トリガーをクリックしてオプションを開閉
        selectHolder.addEventListener('click', () => {
            document.querySelectorAll('.select-options').forEach(opt => {
                if (opt !== options) opt.classList.remove('open');
            });

            selectOptions.classList.toggle('open');
        });

        options.forEach(option => {
            // 既定値があれば、holderにセット
            if(option.classList.contains("chosen")){
                const value = option.getAttribute('data-value');
                const html = option.innerHTML.trim();
                inputElement.value = value;
                selectHolderText.innerHTML = html; // XSSには気をつけようね
            }

            // オプションをクリックしたときのセットイベント
            option.addEventListener('click', () => {
                const value = option.getAttribute('data-value');
                const html = option.innerHTML.trim();
                inputElement.value = value;
                selectHolderText.innerHTML = html; // XSSには気をつけようね
                selectOptions.classList.remove('open');

                // オプション選択状態のセット・アンセット
                options.forEach(opt => {
                    opt.classList.remove('chosen');
                });
                option.classList.add('chosen');

                // onchange イベントを擬似的に発火
                const event = new Event("change", { bubbles: true });
                inputElement.dispatchEvent(event);
            });
        });

        // ドキュメント内をクリックしてオプションを閉じる
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.custom-select')) {
                document.querySelectorAll('.select-options').forEach(options => {
                    options.classList.remove('open');
                });
            }
        });
    });
});
