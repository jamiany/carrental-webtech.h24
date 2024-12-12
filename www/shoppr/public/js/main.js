const { createApp, ref } = Vue

createApp({
    data: function() {
        return {
            carList: null
        }
    },
    methods: {
        myFunction() {
            var x = document.getElementById("demo");
            if (x.className.indexOf("w3-show") == -1) {
                x.className += " w3-show";
            } else {
                x.className = x.className.replace(" w3-show", "");
            }
        }
    },
    created: function() {
        fetch('/backend.php?operation=list')
            .then(r => r.json())
            .then(l => this.carList = l)
    },
    setup() {
        const message = ref('Hello vue!')
        return {
            message
        }
    }
}).mount('#app')