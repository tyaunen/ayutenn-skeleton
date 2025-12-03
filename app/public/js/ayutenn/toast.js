function showToast(message, type = 'info', duration = 3000) {
    const container = document.getElementById('toast-container');

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerText = message;

    const progress = document.createElement('div');
    progress.className = `toast-progress toast-progress-${type}`;
    progress.style.animationDuration = `${duration}ms`;
    toast.appendChild(progress);

    const removeToast = () => {
        toast.style.animation = 'fadeout 0.5s forwards';
        setTimeout(() => toast.remove(), 500);
    };

    toast.addEventListener('click', removeToast);
    container.appendChild(toast);

    setTimeout(removeToast, duration);
}