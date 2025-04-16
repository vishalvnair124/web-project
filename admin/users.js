window.initUsersPage = function () {
    let currentPage = 1;
    let loading = false;
    let allLoaded = false;
    get2=true;
    function loadUsers(page = 1) {
        if (loading || allLoaded) return;
        loading = true;
        document.getElementById('loading').style.display = 'block';

        fetch(`load_users.php?page=${page}`)
        .then(res => res.text())
        .then(data => {
            if (data.trim() === 'NO_MORE') {
                allLoaded = true;
            } else {
                document.getElementById('userData').insertAdjacentHTML('beforeend', data);
                currentPage++;
            }
            loading = false;
            document.getElementById('loading').style.display = 'none';
        });
    }
    let lastClickedButton = null;
    document.addEventListener('click', function (e) {
        if (e.target.tagName === 'BUTTON' && e.target.form) {
            lastClickedButton = e.target; 
        }
    });

    // Form handling
    document.addEventListener('submit', function (e) {
        if (e.target.matches('form[action="update_user_status.php"]')) {
            e.preventDefault();
            if(!get2){
                return
            }
            get2=false;
            const form = e.target;
            const formData = new FormData(form);
            if (lastClickedButton && lastClickedButton.name) {
                formData.append(lastClickedButton.name, 'true');
            }
            fetch('update_user_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // console.log('Response:', data);
                e.target.closest('tr').querySelector('td:nth-child(6)').innerHTML =
                    '<span class="badge badge-updated">Updating</span>';
                    setTimeout(() => {
                        changeStatus(data,e.target.closest('tr'))
                        get2=true;
                    }, 300);
            })
            .catch(error => console.error('Fetch error:', error));
            
        }
    });

    // Infinite Scroll
    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            loadUsers(currentPage);
        }
    }, {
        rootMargin: '200px',
    });

    observer.observe(document.getElementById('sentinel'));

    loadUsers();
}

function changeStatus(status,object){
    td=object.querySelector('td:nth-child(6)');
    buttonsCol=object.querySelector('.actionButtonsCollection');
    if(status==1){
        td.innerHTML=`<span class="badge badge-active">Active</span>`;
        buttonsCol.innerHTML=`<button class='btn-block' name='block'>Block</button><button class='btn-temp' name='temp'>Temp Block</button>`;
    }else if(status==2){
        td.innerHTML=`<span class="badge badge-temp">Temp Block</span>`;
        buttonsCol.innerHTML=`<button class='btn-block' name='block'>Block</button><button class='btn-activate' name='activate'>Activate</button>`;
    }else if(status==0){
        td.innerHTML=`<span class="badge badge-blocked">Blocked</span>`;
        buttonsCol.innerHTML=`<button class='btn-temp' name='temp'>Temp Block</button><button class='btn-activate' name='activate'>Activate</button>`;
    }
}
