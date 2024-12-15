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
            buchungsnummer: '',
            benutzer_id: '',
            bookings: []
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
        },
        loadBookings() {
            this.benutzer_id = this.getCookie('user');
            fetch('/backend?operation=bookings')
                .then(r => r.json())
                .then(l => this.bookings = l)
        },
        getCookie(cname) {
            let name = cname + "=";
            let decodedCookie = decodeURIComponent(document.cookie);
            let ca = decodedCookie.split(';');
            for(let i = 0; i <ca.length; i++) {
              let c = ca[i];
              while (c.charAt(0) == ' ') {
                c = c.substring(1);
              }
              if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
              }
            }
            return "";
          }
    },
    created: function() {
        fetch('/backend?operation=list')
            .then(r => r.json())
            .then(l => this.carList = l)
        fetch('/backend?operation=initcookie', { method: 'PUT' })
            .then(_ => this.loadBookings())
    },
    setup() {
        const message = ref('Hello vue!')
        return {
            message
        }
    }
}).mount('#app')