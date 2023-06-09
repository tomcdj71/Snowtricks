document.addEventListener('DOMContentLoaded', function () {
    const loadMoreTricksButton = document.getElementById('load-more-tricks');
    const loadMoreCommentsButton = document.getElementById('load-more-comments');

    const fetchData = (url, containerId, pageCounter) => {
        fetch(url)
            .then(response => response.text())
            .then(data => {
                document.getElementById(containerId).innerHTML += data;
                pageCounter.page++;
            })
            .catch(error => console.error('Error:', error));
    };

    if (loadMoreTricksButton) {
        let trickPage = { page: 2 };
        loadMoreTricksButton.addEventListener('click', function () {
            fetchData(`/load_more/tricks/${trickPage.page}`, 'tricks-container', trickPage);
        });
    }

    if (loadMoreCommentsButton) {
        let commentPage = { page: 2 };
        let trick = document.querySelector('[data-trick]').getAttribute('data-trick');
        loadMoreCommentsButton.addEventListener('click', function () {
            fetchData(`/load_more/comments/${trick}/${commentPage.page}`, 'comments-container', commentPage);
        });
    }
});
