const { createApp, ref } = Vue

createApp({
    data: function() {
        let rentalDate = new Date();
        rentalDate.setDate(rentalDate.getDate() + 1);
        let rentalDateString = `${rentalDate.getFullYear()}-${rentalDate.getMonth()}-${rentalDate.getDate()}`

        return {
            carList: null,
            navigationItems: [{display: 'Information zur Webseite', route: 'information'}, {display: 'Buchen (Eingabebereich)', route: 'eingabe'}, {display: 'getätigte Buchungen (Ausgabebereich)', route: 'ausgabe'}],
            route: '',
            param: null,
            currentInput: {},
            name: '',
            datum: rentalDateString,
            dauer: 1,
            buchungsnummer: ''
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
        },
        setroute(path, param = null){
            this.route = path;
            this.param = param;
        },
        book() {
            fetch('/backend?operation=book', {
                method: 'POST',
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify({ benutzer_name: this.name, fahrzeug_id: +this.param.id, date: this.datum, dauer: this.dauer })
            }).then(x => {
                buchungsnummer = x.buchungsnummer;
                document.getElementById('confirmBooking').style.display = 'block';
            })
        }
    },
    created: function() {
        fetch('/backend?operation=list')
            .then(r => r.json())
            .then(l => this.carList = l)
        fetch('/backend?operation=initcookie', { method: 'PUT' }).then()
    },
    setup() {
        const message = ref('Hello vue!')
        return {
            message
        }
    }
}).mount('#app')