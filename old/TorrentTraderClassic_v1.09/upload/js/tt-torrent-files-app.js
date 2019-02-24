
window.addEventListener('load', function(event){
    var ttfilelistapp = new Vue({
        el: '#tt-filelist-app',
        data: {
            numfiles: 0,
            files: [],
            open: false,
            error: false,
            fullhumansize: 0,
            fullsize: 0
        },
        methods: {
            tt_load_filelist: function(val, event){
                this.open = this.open ? false : true;
                if (this.numfiles < 1) {
                    axios.get('ajax.php?filelist=1&id=' + val).then(response => {
                        this.files = response.data[0];
                        this.fullhumansize = response.data[1];
                        this.fullsize = response.data[2];
                        // console.log(this.files);
                        this.numfiles = this.files.length;
                    });
                }
            }
        }
    });

    var elem = document.getElementById('tt-filelist-app');
    elem.style.display = 'block';
});
