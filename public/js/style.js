function t() {
    document.getElementById('time').innerHTML = new Date();
}
t();
window.setInterval(t, 1000);
