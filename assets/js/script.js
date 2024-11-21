document.getElementById('flexSwitchCheckDefault').addEventListener('change', function() {
    var returnDateRow = document.getElementById('return-date');
    if (this.checked) {
        returnDateRow.classList.remove('d-none');
    } else {
        returnDateRow.classList.add('d-none');
    }
});