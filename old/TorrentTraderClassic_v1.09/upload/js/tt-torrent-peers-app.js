
window.addEventListener('load', function(event){
    var app = new Vue({
        el: '#tt-peerslist-app',
        data: {
            open: false,
            is_load_data: false,
            error: false,
            result_html: ''
        },
        methods: {
            tt_load_peerslist: function(val, event){
                this.open = this.open ? false : true;
                if (!this.is_load_data) {
                    axios.get('ajax.php?peerslist=1&id=' + val).then(response => {
                        this.result_html = response.data[0];
                    });
                    this.is_load_data = true;
                }
            }
        }
    });

    var elem = document.getElementById('tt-peerslist-app');
    elem.style.display = 'block';
});
