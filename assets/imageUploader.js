let links = document.querySelectorAll('[data-delete]');
for (let link of links) {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        let confirmationMessage = this.dataset.confirm;
        if (confirm(confirmationMessage)) {
            fetch(this.getAttribute('href'), {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ '_csrf': this.dataset.token }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.parentElement.remove();
                    } else {
                        alert(data.error);
                    }
                }).catch(error => console.error('Error:', error));
        }
    });
}

window.addEventListener('DOMContentLoaded', () => {
    const collectionHolder = document.getElementById('videos-container');
    if (!collectionHolder) {
        return;
    }
    let index = collectionHolder.dataset.index;
    document.getElementById('add-video').addEventListener('click', function () {
        let prototype = collectionHolder.dataset.prototype;
        let newForm = prototype.replace(/__name__/g, index);
        index++;
        let div = document.createElement('div');
        div.innerHTML = newForm;
        div.classList.add('video');
        let deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.type = 'button';
        deleteButton.classList.add('delete-video');
        deleteButton.addEventListener('click', function () {
            div.remove();
        });

        div.appendChild(deleteButton);
        collectionHolder.appendChild(div);
    });
    document.querySelectorAll('.delete-video').forEach(function (button) {
        button.addEventListener('click', function () {
            button.parentElement.remove();
        });
    });
});
