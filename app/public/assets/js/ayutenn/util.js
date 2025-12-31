
util = {
    escapeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/\//g, '&#x2F;');
    },
    switchView(group, key){
        const targetList = document.querySelectorAll(`[data-display-group='${group}']`);
        const showTargetList = document.querySelectorAll(`[data-display-group='${group}'][data-display-key='${key}']`);
        targetList.forEach(target => {
            target.classList.add("d-none");
        });
        showTargetList.forEach(target => {
            target.classList.remove("d-none");
        });
    }
}