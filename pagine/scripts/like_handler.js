function likeToggle(btn, id, element = 'post') {
    fetch('../backend/like_handler.php?id=' + id + '&element=' + element)
        .then(res => {
            console.log('STATUS:', res.status, res.url);
            return res.text();
        })
        .then(text => {
            console.log('RAW:', JSON.stringify(text));
            const data = JSON.parse(text.trim());
            const countEl = btn.nextElementSibling;
            let count = parseInt(countEl.textContent) || 0;
            if (data.liked) {
                btn.classList.add('liked');
                countEl.textContent = count + 1;
            } else {
                btn.classList.remove('liked');
                countEl.textContent = Math.max(0, count - 1);
            }
        })
        .catch(err => console.error('Like error:', err));
}