document.getElementById('commentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('commentBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> SUBMITTING...';
    btn.disabled = true;

    const formData = new FormData(this);
    fetch('save_comment.php', { method: 'POST', body: formData })
    .then(response => response.text())
    .then(data => {
        alert("Feedback submitted! It will appear once approved.");
        location.reload();
    })
    .catch(error => {
        alert("Submission failed.");
        btn.disabled = false;
        btn.innerHTML = 'RETRY';
    });
});