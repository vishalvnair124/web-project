window.initRequestsPage = function () {
        let lastClickedButton = null;
     get1=true;
    document.addEventListener('click', function (e) {
        if (e.target.tagName === 'BUTTON' && e.target.form) {
            lastClickedButton = e.target; 
        }
    });

    // Form handling
    document.addEventListener('submit', function (e) {
        if (e.target.matches('form[action="blockRequest.php"]')) {
            e.preventDefault();
            if(!get1){
                return
            }
            get1=false;
            const form = e.target;
            const formData = new FormData(form);
            if (lastClickedButton && lastClickedButton.name) {
                formData.append(lastClickedButton.name, 'true');
            }
            fetch('blockRequest.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                object=e.target.closest('tr').querySelector('[name=Status]')
                e.target.closest('tr').querySelector('td:nth-child(8)').innerHTML =
                    '<span class="status unknown">Updating</span>';
                    setTimeout(() => {
                        changeStatus(object.value,e.target.closest('tr'))
                        object.value=(object.value==5)?3:5;
                        get1=true;
                    }, 300);
            })
            .catch(error => console.error('Fetch error:', error));
            
        }
    });
    function changeStatus(status,object){
        if(status!=5){
            object.querySelector('.actionButtonsCollection').innerHTML=`<button class='btn-block Block'>Block</button>`;
        }
        if(status==3){
            object.querySelector('td:nth-child(8)').innerHTML =
            `<span class='status pending'>Pending</span>`;
        }else if(status==5){
            object.querySelector('td:nth-child(8)').innerHTML =
            `<span class='status Blocked'>Blocked</span>`;
            object.querySelector('.actionButtonsCollection').innerHTML=`<button class='btn-block UnBlock'>UnBlock</button>`;
        }
    }
}
    